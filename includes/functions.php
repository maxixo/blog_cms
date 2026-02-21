<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/helpers.php';

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function is_post_request()
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function current_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    return $protocol . '://' . $host . $uri;
}

function build_query_url($base, $params)
{
    $query = http_build_query($params);
    return $query ? $base . '?' . $query : $base;
}

// Session management functions
function session_start_safe()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn()
{
    session_start_safe();
    return isset($_SESSION['user_id']);
}

function getCurrentUser()
{
    session_start_safe();
    if (isset($_SESSION['user_id'])) {
        require_once __DIR__ . '/../models/User.php';
        return User::findById($_SESSION['user_id']);
    }
    return null;
}

function getCurrentUserRole()
{
    session_start_safe();
    if (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'];
    }
    return 'user';
}

function isAdmin()
{
    return getCurrentUserRole() === 'admin';
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        setFlashMessage('error', 'You do not have permission to access that page.');
        redirect(BASE_URL);
    }
}

function addSearchToHistory($query)
{
    $query = trim((string) $query);
    if ($query === '') {
        return;
    }

    session_start_safe();
    if (!isset($_SESSION['search_history']) || !is_array($_SESSION['search_history'])) {
        $_SESSION['search_history'] = [];
    }

    $history = $_SESSION['search_history'];
    $history = array_values(array_diff($history, [$query]));
    array_unshift($history, $query);
    $_SESSION['search_history'] = array_slice($history, 0, 10);
}

function getSearchHistory()
{
    session_start_safe();
    if (empty($_SESSION['search_history']) || !is_array($_SESSION['search_history'])) {
        return [];
    }

    return array_values($_SESSION['search_history']);
}

// CSRF Protection
function generateCsrfToken()
{
    session_start_safe();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    session_start_safe();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function regenerateCsrfToken()
{
    session_start_safe();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// Snake_case aliases for compatibility
function generate_csrf_token()
{
    return generateCsrfToken();
}

function verify_csrf_token($token)
{
    return verifyCsrfToken($token);
}

function regenerate_csrf_token()
{
    return regenerateCsrfToken();
}

// Flash messages
function setFlashMessage($type, $message)
{
    session_start_safe();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage()
{
    session_start_safe();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Get initials from username
function getInitials($username)
{
    if (empty($username)) {
        return '?';
    }
    
    // Split username by spaces
    $words = explode(' ', trim($username));
    
    // If only one word, take first 2 letters
    if (count($words) === 1) {
        return strtoupper(substr($username, 0, 2));
    }
    
    // Take first letter of first two words
    $initials = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    
    return $initials;
}
