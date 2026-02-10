<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';

$controller = new PasswordResetController();

// Handle POST request to reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $controller->resetPassword($_POST);
} else {
    // Get token from query parameter
    $token = trim($_GET['token'] ?? '');
    $data = $controller->showResetForm($token);
}

includeTemplate('layout/header', $data);
includeTemplate('auth/reset-password', $data);
includeTemplate('layout/footer', $data);require_once __DIR__ . '/templates/auth/reset-password.html.php';
