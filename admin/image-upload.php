<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/uploads.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized request.']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
if ($csrfToken === '' || !verify_csrf_token($csrfToken)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request token.']);
    exit;
}

$file = $_FILES['file'] ?? null;
if (!$file) {
    http_response_code(400);
    echo json_encode(['error' => 'No file provided.']);
    exit;
}

$upload = upload_image_file($file, [
    'subdir' => 'posts/content',
    'prefix' => 'content_',
    'max_size' => 5 * 1024 * 1024
]);

if (!$upload['success']) {
    http_response_code(400);
    echo json_encode(['error' => $upload['error'] ?? 'Upload failed.']);
    exit;
}

echo json_encode(['location' => $upload['url']]);
