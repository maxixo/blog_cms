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
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
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

function clearAuthSession()
{
    session_start_safe();
    unset(
        $_SESSION['user_id'],
        $_SESSION['username'],
        $_SESSION['email'],
        $_SESSION['role'],
        $_SESSION['user_role'],
        $_SESSION['email_verified']
    );
    session_regenerate_id(true);
    regenerateCsrfToken();
}

function isUserRecordVerified($user)
{
    if (!is_array($user)) {
        return false;
    }

    return !empty($user['email_verified']);
}

function isAdminEmail($email)
{
    $normalizedEmail = strtolower(trim((string) $email));
    if ($normalizedEmail === '' || !filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $allowlist = defined('ADMIN_EMAIL_ALLOWLIST') && is_array(ADMIN_EMAIL_ALLOWLIST)
        ? ADMIN_EMAIL_ALLOWLIST
        : [];

    return in_array($normalizedEmail, $allowlist, true);
}

function getEffectiveUserRole($user)
{
    if (!is_array($user) || !isUserRecordVerified($user)) {
        return 'user';
    }

    return isAdminEmail($user['email'] ?? '') ? 'admin' : 'user';
}

function isLoggedIn()
{
    return getCurrentUser() !== null;
}

function getCurrentUser()
{
    session_start_safe();
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    require_once __DIR__ . '/../models/User.php';
    $user = User::findById((int) $_SESSION['user_id']);
    if (!$user || !isUserRecordVerified($user)) {
        clearAuthSession();
        return null;
    }

    $effectiveRole = getEffectiveUserRole($user);
    $_SESSION['username'] = $user['username'] ?? '';
    $_SESSION['email'] = $user['email'] ?? '';
    $_SESSION['email_verified'] = 1;
    $_SESSION['role'] = $effectiveRole;
    $_SESSION['user_role'] = $effectiveRole;

    return $user;
}

function getCurrentUserRole()
{
    $currentUser = getCurrentUser();
    if ($currentUser === null) {
        return 'user';
    }

    return getEffectiveUserRole($currentUser);
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
