<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';

$controller = new PasswordResetController();

// Handle POST request to reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    $data = $controller->handleReset($token, $_POST);
} else {
    // Get token from query parameter
    $token = trim($_GET['token'] ?? '');
    $data = $controller->showResetForm($token);
}

extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/reset-password.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
