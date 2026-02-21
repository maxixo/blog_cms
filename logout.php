<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

session_start_safe();

// Destroy session
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Start a fresh session for the flash message
session_start_safe();
setFlashMessage('success', 'You have been logged out successfully.');

// Redirect to login page
redirect(BASE_URL . '/login.php');
