<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/PostManageController.php';

requireAdmin();

$controller = new PostManageController();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    if ($postId) {
        $controller->delete($postId);
    }
    exit;
}

$data = $controller->index();
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/post-list.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
