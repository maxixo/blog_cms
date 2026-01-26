<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/PostManageController.php';

$controller = new PostManageController();
$data = $controller->index();
extract($data);

require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/post-list.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
