<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/PostController.php';

$slug = get_query_value('slug');

$controller = new PostController();
$data = $controller->show($slug);
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/post/single.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
