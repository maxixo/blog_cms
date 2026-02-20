<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

function rateLimit($key, $limit, $window)
{
    $key = (string) $key;
    $limit = (int) $limit;
    $window = (int) $window;

    if ($key === '' || $limit <= 0 || $window <= 0) {
        return true;
    }

    $directory = __DIR__ . '/../cache/rate-limits';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        error_log('Rate limiter: failed to create cache directory at ' . $directory);
        return true;
    }

    $file = $directory . '/' . hash('sha256', $key) . '.json';
    $now = time();
    $windowStart = $now - $window;

    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        error_log('Rate limiter: failed to open ' . $file);
        return true;
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return true;
    }

    rewind($handle);
    $raw = stream_get_contents($handle);
    $timestamps = json_decode($raw ?: '[]', true);
    if (!is_array($timestamps)) {
        $timestamps = [];
    }

    $timestamps = array_values(array_filter($timestamps, function ($timestamp) use ($windowStart) {
        return is_int($timestamp) && $timestamp > $windowStart;
    }));

    $allowed = count($timestamps) < $limit;
    if ($allowed) {
        $timestamps[] = $now;
    }

    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, json_encode($timestamps));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $allowed;
}
