<?php
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

includeTemplate('layout/header', $data);
includeTemplate('auth/verify-email', $data);
includeTemplate('layout/footer', $data);