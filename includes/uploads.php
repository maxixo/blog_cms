<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

function upload_error_message($errorCode)
{
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'File size exceeds the allowed limit.';
        case UPLOAD_ERR_PARTIAL:
            return 'The file was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder on the server.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk.';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by a PHP extension.';
        default:
            return 'File upload failed.';
    }
}

function upload_image_file($file, $options = [])
{
    if (!is_array($file) || !isset($file['error'])) {
        return ['success' => false, 'error' => 'Invalid upload data.'];
    }

    if (is_array($file['error'])) {
        return ['success' => false, 'error' => 'Multiple file uploads are not supported.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => upload_error_message($file['error'])];
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Upload verification failed.'];
    }

    $maxSize = $options['max_size'] ?? (5 * 1024 * 1024);
    if (!empty($file['size']) && $file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size must be less than 5MB.'];
    }

    $allowedMime = $options['allowed_mime'] ?? [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    $mimeType = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mimeType = finfo_file($finfo, $file['tmp_name']) ?: '';
            finfo_close($finfo);
        }
    }

    if ($mimeType === '') {
        $mimeType = $file['type'] ?? '';
    }

    if (!in_array($mimeType, $allowedMime, true)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }

    $extensionMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if ($extension === 'jpeg') {
        $extension = 'jpg';
    }
    if (!in_array($extension, $extensionMap, true)) {
        $extension = $extensionMap[$mimeType] ?? '';
    }
    if ($extension === '') {
        return ['success' => false, 'error' => 'Unable to determine file extension.'];
    }

    $subdir = trim((string) ($options['subdir'] ?? 'posts'), '/');
    $prefix = (string) ($options['prefix'] ?? 'image_');

    $random = function_exists('random_bytes') ? bin2hex(random_bytes(8)) : uniqid();
    $filename = $prefix . $random . '.' . $extension;

    $uploadDir = rtrim(UPLOADS_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if ($subdir !== '') {
        $uploadDir .= str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subdir) . DIRECTORY_SEPARATOR;
    }

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'error' => 'Failed to create upload directory.'];
        }
    }

    $uploadPath = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'error' => 'Failed to upload file. Please try again.'];
    }

    $relativePath = 'uploads' . ($subdir !== '' ? '/' . $subdir : '') . '/' . $filename;
    $relativePath = str_replace('\\', '/', $relativePath);
    $url = rtrim(UPLOADS_URL, '/') . ($subdir !== '' ? '/' . $subdir : '') . '/' . $filename;

    return ['success' => true, 'path' => $relativePath, 'url' => $url];
}
