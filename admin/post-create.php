<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/PostManageController.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$controller = new PostManageController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store();
}

$data = $controller->create();
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/post-form.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';