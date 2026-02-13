<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';

$controller = new PasswordResetController();

// Handle POST request to send password reset email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $controller->handleForgotRequest($_POST);
} else {
    $data = $controller->showForgotForm();
}

extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/forgot-password.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
