<?php
if (!defined('APP_STARTED')) {
    define('APP_STARTED', true);
}

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

define('POSTS_PER_PAGE', 6);
define('COMMENTS_PER_PAGE', 10);

define('ENABLE_REGISTRATION', true);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Get the directory path of the script being executed
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');

// Find the project root by looking for the admin directory
// If we're in the admin directory or deeper, go up to find the root
$parts = explode('/', trim($scriptPath, '/'));
$basePath = '';

// If we have a 'blog_cms' folder in the path, use it as base
foreach ($parts as $index => $part) {
    $basePath .= '/' . $part;
    // Check if this is our project root (where admin folder exists)
    if (file_exists(__DIR__ . '/../../admin') || file_exists(__DIR__ . '/../../index.php')) {
        break;
    }
}

// If we're in a subdirectory of admin, go up one more level
if (in_array('admin', $parts)) {
    $basePath = '';
    foreach ($parts as $index => $part) {
        if ($part === 'admin') {
            break;
        }
        $basePath .= '/' . $part;
    }
}

$basePath = rtrim($basePath, '/');
$basePath = $basePath === '' ? '' : $basePath;

define('BASE_URL', $protocol . '://' . $host . $basePath);

define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_PATH', __DIR__ . '/../public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

define('DEFAULT_AVATAR', UPLOADS_URL . '/avatars/default-avatar.png');
define('DEFAULT_OG_IMAGE', UPLOADS_URL . '/avatars/default-avatar.png');
