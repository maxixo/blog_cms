<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

function securityLogPath(): string
{
    return __DIR__ . '/../logs/security.log';
}

function normalizeSecurityIp($ip): string
{
    $normalized = trim((string) $ip);
    if ($normalized === '') {
        $normalized = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    return $normalized !== '' ? $normalized : 'unknown';
}

function writeSecurityLogEntry(array $entry): void
{
    $path = securityLogPath();
    $dir = dirname($path);

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $entry['timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    $encoded = json_encode($entry, JSON_UNESCAPED_SLASHES);

    if ($encoded === false) {
        return;
    }

    @file_put_contents($path, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function logLoginAttempt($email, $success, $ip): void
{
    $event = $success ? 'login_success' : 'login_failure';

    logSecurityEvent($event, [
        'email' => trim((string) $email),
        'ip' => normalizeSecurityIp($ip)
    ]);
}

function logSecurityEvent($event, $details): void
{
    $payloadDetails = is_array($details) ? $details : ['message' => (string) $details];
    if (!isset($payloadDetails['ip'])) {
        $payloadDetails['ip'] = normalizeSecurityIp($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    writeSecurityLogEntry([
        'event' => trim((string) $event),
        'details' => $payloadDetails
    ]);
}
