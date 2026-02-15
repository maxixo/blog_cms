<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/PasswordResetController.php';

try {
    $controller = new PasswordResetController();

    // Handle POST request to send password reset email
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $controller->handleForgotRequest($_POST);
    } else {
        $data = $controller->showForgotForm();
    }

    extract($data);

    require_once __DIR__ . '/templates/layout/header.html.php';
    require_once __DIR__ . '/templates/auth/forgot-password.html.php';
    require_once __DIR__ . '/templates/layout/footer.html.php';
} catch (Exception $e) {
    // Log error
    error_log('Password reset error: ' . $e->getMessage());
    
    // Show error page
    if (APP_DEBUG) {
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>Password Reset Error</h1>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '<p><a href="' . BASE_URL . '/forgot-password.php">Back to Forgot Password</a></p>';
        echo '</body></html>';
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>An Error Occurred</h1>';
        echo '<p>Sorry, something went wrong. Please try again later.</p>';
        echo '<p><a href="' . BASE_URL . '/forgot-password.php">Back to Forgot Password</a></p>';
        echo '</body></html>';
    }
} catch (Error $e) {
    // Catch fatal errors
    error_log('Password reset fatal error: ' . $e->getMessage());
    
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
    echo '<h1>An Error Occurred</h1>';
    echo '<p>Sorry, something went wrong. Please try again later.</p>';
    echo '<p><a href="' . BASE_URL . '/forgot-password.php">Back to Forgot Password</a></p>';
    echo '</body></html>';
}
