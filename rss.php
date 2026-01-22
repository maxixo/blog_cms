<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
<channel>
    <title><?= esc(SITE_NAME); ?></title>
    <link><?= esc(BASE_URL); ?></link>
    <description><?= esc(SITE_DESCRIPTION); ?></description>
    <language>en-us</language>
</channel>
</rss>
