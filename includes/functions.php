<?php

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function uploadImage(string $fieldName, string $targetDir, array $allowed = ['image/jpeg', 'image/png']): ?string
{
    if (empty($_FILES[$fieldName]['name'])) return null;
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('File upload error.');
    if ($file['size'] > 5 * 1024 * 1024) throw new RuntimeException('Image size must be under 5MB.');

    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mimeType, $allowed, true)) throw new RuntimeException('Only JPG and PNG images are allowed.');

    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $newName = bin2hex(random_bytes(16)) . ($mimeType === 'image/png' ? '.png' : '.jpg');
    $destination = rtrim($targetDir, '/') . '/' . $newName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('Could not save uploaded image.');
    return $newName;
}

function uploadMultipleImages(string $fieldName, string $targetDir): array
{
    $saved = [];
    if (empty($_FILES[$fieldName]['name'][0])) return $saved;
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
    if ($path && file_exists($path)) unlink($path);
}

function paginate(int $totalRows, int $perPage = 10): array
{
    $page = max(1, (int)($_GET['page'] ?? 1));
    $totalPages = (int)ceil($totalRows / $perPage);
    return ['page' => $page, 'per_page' => $perPage, 'offset' => ($page - 1) * $perPage, 'total_pages' => max(1, $totalPages)];
}

function addNotification(PDO $pdo, string $type, string $title, string $message, ?string $link = null): void
{
    $pdo->prepare('INSERT INTO notifications (type, title, message, link) VALUES (?, ?, ?, ?)')->execute([$type, $title, $message, $link]);
}

function sendTemplateEmail(PDO $pdo, string $triggerType, string $toEmail, array $vars): void
{
    $stmt = $pdo->prepare('SELECT subject, body FROM email_templates WHERE trigger_type = ? AND status = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$triggerType, 'enabled']);
    $template = $stmt->fetch();
    if (!$template) return;

    $settings = $pdo->query('SELECT email_sender_name, email_sender_address FROM settings LIMIT 1')->fetch();
    $subject = $template['subject'];
    $body = $template['body'];
    foreach ($vars as $key => $value) {
        $subject = str_replace('{' . $key . '}', (string)$value, $subject);
        $body = str_replace('{' . $key . '}', (string)$value, $body);
    }

    $fromName = $settings['email_sender_name'] ?? 'PaintPro';
    $fromEmail = $settings['email_sender_address'] ?? 'noreply@paintpro.local';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
    @mail($toEmail, $subject, $body, $headers);
}
