<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/EmailVerificationController.php';

$controller = new EmailVerificationController();

// Handle POST request to resend verification email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $controller->resend($_POST);
} else {
    // Get email from query parameter
    $email = trim($_GET['email'] ?? '');
    $data = $controller->showPending($email);
}

extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/verify-email.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
