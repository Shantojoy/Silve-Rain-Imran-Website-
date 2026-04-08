<?php

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function uploadImage(string $fieldName, string $targetDir, array $allowed = ['image/jpeg', 'image/png', 'image/webp']): ?string
{
    if (empty($_FILES[$fieldName]['name'])) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload error. Please try again.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Max file size is 5MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowed, true)) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = bin2hex(random_bytes(16)) . '.' . strtolower($extension);
    $destination = rtrim($targetDir, '/') . '/' . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to save uploaded image.');
    }

    return $newName;
}

function deleteUploadedFile(string $path): void
{
    if ($path && file_exists($path)) {
        unlink($path);
    }
}
