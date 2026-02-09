<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/CommentManageController.php';

requireAdmin();

$controller = new CommentManageController();

// Handle comment deletion
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $commentId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($commentId) {
        $controller->delete($commentId);
    }
    exit;
}

$data = $controller->index();
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/comment-moderation.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
