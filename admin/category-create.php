<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/CategoryManageController.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$controller = new CategoryManageController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
    exit;
}

$pageHeading = 'Create Category';
$pageDescription = 'Add a new category to organize your posts.';
$pageTitle = 'Create Category - ' . SITE_NAME;
$bodyClass = 'admin-page';
$additionalCss = [ASSETS_URL . '/css/admin.css'];
$additionalJs = [ASSETS_URL . '/js/admin.js'];
$formAction = BASE_URL . '/admin/category-create.php';

require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/category-form.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';