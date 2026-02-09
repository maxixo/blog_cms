<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/CategoryManageController.php';

requireAdmin();

$controller = new CategoryManageController();

// Handle category deletion
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $categoryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($categoryId) {
        $controller->delete($categoryId);
    }
    exit;
}

$data = $controller->index();
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/category-list.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
