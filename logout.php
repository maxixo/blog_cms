<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

session_start_safe();

// Set logout success message
setFlashMessage('success', 'You have been logged out successfully.');

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
redirect(BASE_URL . '/login.php');
