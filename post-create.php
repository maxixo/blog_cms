<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/admin/PostManageController.php';

requireLogin();

$controller = new PostManageController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store([
        'listUrl' => BASE_URL . '/posts.php',
        'createUrl' => BASE_URL . '/post-create.php'
    ]);
}

$data = $controller->create([
    'isAdminView' => false,
    'backUrl' => BASE_URL . '/posts.php',
    'uploadUrl' => BASE_URL . '/image-upload.php'
]);
extract($data);

$bodyClass = trim(($bodyClass ?? '') . ' post-create-page');
require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/admin/post-form.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
