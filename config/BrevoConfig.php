<?php
if (!defined('APP_STARTED')) {
    define('APP_STARTED', true);
}

// Brevo Configuration
define('BREVO_API_KEY', getenv('BREVO_API_KEY') ?: '');
define('BREVO_SENDER_EMAIL', getenv('BREVO_SENDER_EMAIL') ?: 'noreply@yourdomain.com');
define('BREVO_SENDER_NAME', getenv('BREVO_SENDER_NAME') ?: SITE_NAME);

// Email Settings
define('EMAIL_VERIFICATION_REQUIRED', env_flag('EMAIL_VERIFICATION_REQUIRED', true));
define('PASSWORD_RESET_TOKEN_EXPIRY', (int) (getenv('PASSWORD_RESET_TOKEN_EXPIRY') ?: 3600));
define('EMAIL_VERIFICATION_TOKEN_EXPIRY', (int) (getenv('EMAIL_VERIFICATION_TOKEN_EXPIRY') ?: 86400));

// Email URLs
define('EMAIL_VERIFICATION_URL', BASE_URL . '/verify-email.php');
define('PASSWORD_RESET_URL', BASE_URL . '/reset-password.php');

// Debug mode - logs email attempts instead of sending
define('BREVO_DEBUG_MODE', env_flag('BREVO_DEBUG_MODE', false));