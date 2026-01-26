<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController();
if (is_post_request()) {
    $data = $controller->register($_POST);
} else {
    $data = $controller->showRegisterForm();
}
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/register.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
