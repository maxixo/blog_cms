<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/HomeController.php';

$controller = new HomeController();
$data = $controller->index();
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/home/index.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
