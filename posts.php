<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/PostsController.php';

$controller = new PostsController();
$data = $controller->index();
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/post/list.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
