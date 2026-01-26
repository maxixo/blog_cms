<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/admin/UserManageController.php';

$controller = new UserManageController();
$data = $controller->index();
extract($data);

require_once __DIR__ . '/../templates/layout/header.html.php';
require_once __DIR__ . '/../templates/admin/user-list.html.php';
require_once __DIR__ . '/../templates/layout/footer.html.php';
