<?php
require_once __DIR__ . '/config.php';

class EmailConfig
{
    public const DEFAULT_PASSWORD_RESET_EXPIRY = 3600;
    public const DEFAULT_EMAIL_VERIFICATION_EXPIRY = 86400;

    public static function brevoApiKey()
    {
        return trim((string) (getenv('BREVO_API_KEY') ?: ''));
    }

    public static function senderEmail()
    {
        $envValue = trim((string) (getenv('BREVO_SENDER_EMAIL') ?: ''));
        if ($envValue !== '') {
            return $envValue;
        }

        return defined('SITE_EMAIL') ? SITE_EMAIL : '';
    }

    public static function senderName()
    {
        $envValue = trim((string) (getenv('BREVO_SENDER_NAME') ?: ''));
        if ($envValue !== '') {
            return $envValue;
        }

        return defined('SITE_NAME') ? SITE_NAME : 'Blog CMS';
    }

    public static function emailVerificationRequired()
    {
        return defined('EMAIL_VERIFICATION_REQUIRED') ? EMAIL_VERIFICATION_REQUIRED : true;
    }

    public static function passwordResetExpirySeconds()
    {
        return defined('PASSWORD_RESET_TOKEN_EXPIRY')
            ? (int) PASSWORD_RESET_TOKEN_EXPIRY
            : self::DEFAULT_PASSWORD_RESET_EXPIRY;
    }

    public static function emailVerificationExpirySeconds()
    {
        return defined('EMAIL_VERIFICATION_TOKEN_EXPIRY')
            ? (int) EMAIL_VERIFICATION_TOKEN_EXPIRY
            : self::DEFAULT_EMAIL_VERIFICATION_EXPIRY;
    }

    public static function verificationUrl($token)
    {
        return rtrim(BASE_URL, '/') . '/verify-email.php?token=' . urlencode($token);
    }

    public static function passwordResetUrl($token)
    {
        return rtrim(BASE_URL, '/') . '/reset-password.php?token=' . urlencode($token);
    }

    public static function debugMode()
    {
        if (function_exists('env_flag')) {
            return env_flag('EMAIL_DEBUG', defined('APP_DEBUG') ? APP_DEBUG : false);
        }

        $value = getenv('EMAIL_DEBUG');
        if ($value === false) {
            return defined('APP_DEBUG') ? APP_DEBUG : false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
