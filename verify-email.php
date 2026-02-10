<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/EmailVerificationController.php';

$controller = new EmailVerificationController();

// Get email from query parameter
$email = trim($_GET['email'] ?? '');

// Show pending verification page
$data = $controller->showPending($email);

includeTemplate('layout/header', $data);
includeTemplate('auth/verify-email', $data);
includeTemplate('layout/footer', $data);unset($_SESSION['email_verification_result']);
