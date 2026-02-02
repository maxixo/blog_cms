<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/CommentManageController.php';

$controller = new CommentManageController();
$data = $controller->index();
extract($data);

$bodyClass = 'admin-page';
require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/comment-moderation.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
