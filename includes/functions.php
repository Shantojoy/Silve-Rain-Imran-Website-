<?php

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function uploadImage(string $fieldName, string $targetDir, array $allowed = ['image/jpeg', 'image/png']): ?string
{
    if (empty($_FILES[$fieldName]['name'])) {
        return null;
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload error.');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image size must be under 5MB.');
    }

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mimeType, $allowed, true)) {
        throw new RuntimeException('Only JPG and PNG images are allowed.');
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $extension = $mimeType === 'image/png' ? 'png' : 'jpg';
    $newName = bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return $newName;
}

function uploadMultipleImages(string $fieldName, string $targetDir): array
{
    $saved = [];
    if (empty($_FILES[$fieldName]['name'][0])) {
        return $saved;
    }

    foreach ($_FILES[$fieldName]['name'] as $index => $name) {
        $_FILES['__single'] = [
            'name' => $_FILES[$fieldName]['name'][$index],
            'type' => $_FILES[$fieldName]['type'][$index],
            'tmp_name' => $_FILES[$fieldName]['tmp_name'][$index],
            'error' => $_FILES[$fieldName]['error'][$index],
            'size' => $_FILES[$fieldName]['size'][$index],
        ];
        $saved[] = uploadImage('__single', $targetDir);
    }

    return $saved;
}

function deleteUploadedFile(string $path): void
{
    if ($path && file_exists($path)) {
        unlink($path);
    }
}

function paginate(int $totalRows, int $perPage = 10): array
{
    $page = max(1, (int)($_GET['page'] ?? 1));
    $totalPages = (int)ceil($totalRows / $perPage);
    $offset = ($page - 1) * $perPage;
    return ['page' => $page, 'per_page' => $perPage, 'offset' => $offset, 'total_pages' => max(1, $totalPages)];
}

function sendTemplateEmail(PDO $pdo, string $trigger, string $toEmail, array $vars): void
{
    $stmt = $pdo->prepare('SELECT subject, body FROM email_templates WHERE status_trigger = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$trigger]);
    $template = $stmt->fetch();
    if (!$template) {
        return;
    }

    $subject = $template['subject'];
    $body = $template['body'];
    foreach ($vars as $key => $value) {
        $subject = str_replace('{{' . $key . '}}', (string)$value, $subject);
        $body = str_replace('{{' . $key . '}}', (string)$value, $body);
    }

    @mail($toEmail, $subject, $body, "From: noreply@paintpro.local\r\nContent-Type: text/plain; charset=UTF-8");
}
