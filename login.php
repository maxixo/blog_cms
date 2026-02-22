<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/AuthController.php';

if (isLoggedIn()) {
    redirect(BASE_PATH . '/index.php');
}

$controller = new AuthController();
if (is_post_request()) {
    $data = $controller->login($_POST);
} else {
    $data = $controller->showLoginForm();
}
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/login.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
