<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/EmailVerificationController.php';

$controller = new EmailVerificationController();

// Get token from query parameter
$token = trim($_GET['token'] ?? '');

// Process email verification
$controller->verify($token);
