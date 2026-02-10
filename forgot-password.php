<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';

$controller = new PasswordResetController();

// Handle POST request to send password reset email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $controller->requestReset($_POST);
} else {
    $data = $controller->showRequestForm();
}

includeTemplate('layout/header', $data);
includeTemplate('auth/forgot-password', $data);
includeTemplate('layout/footer', $data);require_once __DIR__ . '/templates/auth/forgot-password.html.php';
