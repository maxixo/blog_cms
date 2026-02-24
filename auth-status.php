<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() !== 'cli' && !headers_sent()) {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Vary: Cookie', false);
}

$currentUser = getCurrentUser();
if ($currentUser === null) {
    echo json_encode([
        'authenticated' => false
    ]);
    exit;
}

$username = (string) ($currentUser['username'] ?? ($_SESSION['username'] ?? 'User'));

echo json_encode([
    'authenticated' => true,
    'username' => $username,
    'initials' => getInitials($username),
    'is_admin' => getEffectiveUserRole($currentUser) === 'admin'
], JSON_UNESCAPED_SLASHES);

