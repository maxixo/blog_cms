<?php
if (!defined('APP_STARTED')) {
    define('APP_STARTED', true);
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

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('default_charset', 'UTF-8');

// Database configuration
// Update these values for your environment.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blog_cms');

// Site settings
define('SITE_NAME', 'Blog CMS');
define('SITE_TAGLINE', 'Publishing with clarity.');
define('SITE_DESCRIPTION', 'A full-featured blog and content management system.');
define('SITE_EMAIL', 'admin@example.com');

// RSS Feed Settings
define('RSS_FEED_LIMIT', 20);
define('RSS_CACHE_DURATION', 1800);
define('RSS_DESCRIPTION_LENGTH', 300);
define('RSS_INCLUDE_IMAGES', true);

define('POSTS_PER_PAGE', 6);
define('COMMENTS_PER_PAGE', 10);

define('ENABLE_REGISTRATION', true);

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
// If your project is in a subdirectory, add it here (e.g., '/blog_cms')
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$subdirectory = '/blog_cms'; // Change this if your project is in a different subdirectory

define('BASE_URL', $protocol . '://' . $host . $subdirectory);

define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_PATH', __DIR__ . '/../public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

define('DEFAULT_AVATAR', UPLOADS_URL . '/avatars/default-avatar.png');
define('DEFAULT_OG_IMAGE', UPLOADS_URL . '/avatars/default-avatar.png');
