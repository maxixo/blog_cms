<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/EmailVerificationController.php';

$controller = new EmailVerificationController();

// Get email from query parameter
$email = trim($_GET['email'] ?? '');

// Show pending verification page
$data = $controller->showPending($email);

extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/auth/verify-email.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';

unset($_SESSION['email_verification_result']);
