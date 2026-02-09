<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/CategoryManageController.php';

requireAdmin();

$controller = new CategoryManageController();
$id = (int) ($_GET['id'] ?? 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->update($id);
    exit;
}

// Get category data
$data = $controller->edit($id);
extract($data);

$formAction = BASE_URL . '/admin/category-edit.php?id=' . $id;

require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/category-form.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
