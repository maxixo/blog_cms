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
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$basePath = $basePath === '/' ? '' : $basePath;

define('BASE_URL', $protocol . '://' . $host . $basePath);

define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_PATH', __DIR__ . '/../public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

define('DEFAULT_AVATAR', UPLOADS_URL . '/avatars/default-avatar.png');
define('DEFAULT_OG_IMAGE', UPLOADS_URL . '/avatars/default-avatar.png');
