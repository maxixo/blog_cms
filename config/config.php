<?php
if (!defined('APP_STARTED')) {
    define('APP_STARTED', true);
}

function env_flag($key, $default = false)
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file from project root
loadEnv(__DIR__ . '/../.env');

date_default_timezone_set('UTC');

$appEnv = strtolower(getenv('APP_ENV') ?: 'production');
$appDebug = env_flag('APP_DEBUG', $appEnv !== 'production');

define('APP_ENV', $appEnv);
define('APP_DEBUG', $appDebug);

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('display_startup_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('html_errors', '0');
ini_set('default_charset', 'UTF-8');

$errorLogPath = getenv('APP_ERROR_LOG');
if ($errorLogPath) {
    ini_set('error_log', $errorLogPath);
}

$forceHttps = env_flag('APP_FORCE_HTTPS', false);
$forwardedProto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
$forwardedSsl = strtolower($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '');
$requestIsHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $forwardedProto === 'https'
    || $forwardedSsl === 'on';
$isHttps = $forceHttps || $requestIsHttps;

if ($forceHttps && !$requestIsHttps && php_sapi_name() !== 'cli') {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if ($host !== '') {
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');

$basePathEnv = getenv('APP_BASE_PATH');
$basePath = $basePathEnv !== false ? trim($basePathEnv) : '/blog_cms';
if ($basePath !== '' && (preg_match('#^https?://#i', $basePath) === 1 || strpos($basePath, '.') !== false)) {
    error_log('Invalid APP_BASE_PATH detected; expected only a path segment. Falling back to root.');
    $basePath = '';
}
$basePath = '/' . trim($basePath, '/');
if ($basePath === '/') {
    $basePath = '';
}

$cookiePath = $basePath !== '' ? $basePath . '/' : '/';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookiePath,
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Database configuration
// Update these values for your environment.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'blog_cms');

// Site settings
define('SITE_NAME', getenv('SITE_NAME') ?: 'Blog CMS');
define('SITE_TAGLINE', getenv('SITE_TAGLINE') ?: 'Publishing with clarity.');
define('SITE_DESCRIPTION', getenv('SITE_DESCRIPTION') ?: 'A full-featured blog and content management system.');
define('SITE_EMAIL', getenv('SITE_EMAIL') ?: 'admin@example.com');

// RSS Feed Settings
define('RSS_FEED_LIMIT', (int) (getenv('RSS_FEED_LIMIT') ?: 20));
define('RSS_CACHE_DURATION', (int) (getenv('RSS_CACHE_DURATION') ?: 1800));
define('RSS_DESCRIPTION_LENGTH', (int) (getenv('RSS_DESCRIPTION_LENGTH') ?: 300));
define('RSS_INCLUDE_IMAGES', env_flag('RSS_INCLUDE_IMAGES', true));

define('POSTS_PER_PAGE', (int) (getenv('POSTS_PER_PAGE') ?: 6));
define('COMMENTS_PER_PAGE', (int) (getenv('COMMENTS_PER_PAGE') ?: 10));

// Search Settings
define('MIN_SEARCH_LENGTH', (int) (getenv('MIN_SEARCH_LENGTH') ?: 2)); // Minimum characters for search
define('SEARCH_MAX_RESULTS', (int) (getenv('SEARCH_MAX_RESULTS') ?: 100)); // Maximum results to return
define('SEARCH_CACHE_DURATION', (int) (getenv('SEARCH_CACHE_DURATION') ?: 300)); // Cache duration in seconds (5 min)

define('ENABLE_REGISTRATION', env_flag('ENABLE_REGISTRATION', true));

define('EMAIL_VERIFICATION_REQUIRED', env_flag('EMAIL_VERIFICATION_REQUIRED', true));
define('PASSWORD_RESET_TOKEN_EXPIRY', (int) (getenv('PASSWORD_RESET_TOKEN_EXPIRY') ?: 3600));
define('EMAIL_VERIFICATION_TOKEN_EXPIRY', (int) (getenv('EMAIL_VERIFICATION_TOKEN_EXPIRY') ?: 86400));

// TinyMCE Configuration - using free jsDelivr CDN (no API key required)
// You can still use TINYMCE_API_KEY in .env if you want to use TinyMCE Cloud
$tinymceApiKey = getenv('TINYMCE_API_KEY') ?: '';
$tinymceAllowLocalhost = getenv('TINYMCE_ALLOW_LOCALHOST') ?: 'false';

define('TINYMCE_API_KEY', $tinymceApiKey);
define('TINYMCE_ALLOW_LOCALHOST', filter_var($tinymceAllowLocalhost, FILTER_VALIDATE_BOOLEAN));

// Use jsDelivr CDN by default (free, no API key needed)
// If you want to use TinyMCE Cloud, set TINYMCE_API_KEY in .env
if (!empty($tinymceApiKey) && $tinymceApiKey !== 'your_api_key_here') {
    // Using TinyMCE Cloud with API key
    $tinymceKey = trim($tinymceApiKey);
    $tinymceHost = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
    $tinymceHost = preg_replace('/:\d+$/', '', $tinymceHost);
    $isLocalHost = in_array($tinymceHost, ['localhost', '127.0.0.1'], true);
    
    if ($isLocalHost && !TINYMCE_ALLOW_LOCALHOST) {
        $tinymceKey = 'no-api-key';
    }
    
    if (preg_match('/^https?:\/\//', $tinymceKey) === 1) {
        define('TINYMCE_SCRIPT_URL', $tinymceKey);
    } else {
        define('TINYMCE_SCRIPT_URL', 'https://cdn.tiny.cloud/1/' . $tinymceKey . '/tinymce/6/tinymce.min.js');
    }
} else {
    // Using free jsDelivr CDN (no API key required)
    define('TINYMCE_SCRIPT_URL', 'https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js');
}

// Explicitly set the base path for XAMPP setup
// If your project is in a subdirectory, set APP_BASE_PATH (e.g., /blog_cms).
$protocol = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrlEnv = getenv('APP_BASE_URL');
$baseUrl = $baseUrlEnv ? rtrim($baseUrlEnv, '/') : ($protocol . '://' . $host . $basePath);

define('BASE_PATH', $basePath);
define('BASE_URL', $baseUrl);

define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_PATH', __DIR__ . '/../public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

define('DEFAULT_AVATAR', UPLOADS_URL . '/avatars/default-avatar.png');
define('DEFAULT_OG_IMAGE', UPLOADS_URL . '/avatars/default-avatar.png');

if (!defined('CSP_NONCE')) {
    try {
        define('CSP_NONCE', base64_encode(random_bytes(16)));
    } catch (Throwable $e) {
        define('CSP_NONCE', '');
        error_log('Failed to generate CSP nonce: ' . $e->getMessage());
    }
}

if (php_sapi_name() !== 'cli' && !headers_sent()) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-" . CSP_NONCE . "' https://cdn.jsdelivr.net https://cdn.tiny.cloud; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self';");
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
