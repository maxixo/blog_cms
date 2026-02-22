<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/controllers/AuthController.php';

if (isLoggedIn()) {
    redirect(BASE_PATH . '/index.php');
}

try {
    $controller = new AuthController();
    if (is_post_request()) {
        $data = $controller->register($_POST);
    } else {
        $data = $controller->showRegisterForm();
    }
    extract($data);

    require_once __DIR__ . '/templates/layout/header.html.php';
    require_once __DIR__ . '/templates/auth/register.html.php';
    require_once __DIR__ . '/templates/layout/footer.html.php';
} catch (Exception $e) {
    // Log the error
    error_log('Registration error: ' . $e->getMessage());
    
    // Show error page
    if (APP_DEBUG) {
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>Registration Error</h1>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '<p><a href="' . BASE_URL . '/register.php">Back to Registration</a></p>';
        echo '</body></html>';
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>An Error Occurred</h1>';
        echo '<p>Sorry, something went wrong. Please try again later.</p>';
        echo '<p><a href="' . BASE_URL . '/register.php">Back to Registration</a></p>';
        echo '</body></html>';
    }
} catch (Error $e) {
    // Catch fatal errors
    error_log('Registration fatal error: ' . $e->getMessage());
    
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
    echo '<h1>An Error Occurred</h1>';
    echo '<p>Sorry, something went wrong. Please try again later.</p>';
    echo '<p><a href="' . BASE_URL . '/register.php">Back to Registration</a></p>';
    echo '</body></html>';
}
