<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
// Get token from query parameter
$token = trim($_GET['token'] ?? '');

if ($token === '') {
    setFlashMessage('error', 'Invalid verification link.');
    redirect(BASE_URL . '/login.php');
}

// Legacy endpoint kept for backward compatibility with old email links.
redirect(BASE_URL . '/verify-email.php?token=' . urlencode($token));
