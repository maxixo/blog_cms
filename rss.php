<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

$rssLimit = defined('RSS_FEED_LIMIT') ? (int) RSS_FEED_LIMIT : 20;
$rssLimit = max(1, min($rssLimit, 50));
$rssCacheDuration = defined('RSS_CACHE_DURATION') ? (int) RSS_CACHE_DURATION : 1800;
$rssDescriptionLength = defined('RSS_DESCRIPTION_LENGTH') ? (int) RSS_DESCRIPTION_LENGTH : 300;
$rssIncludeImages = defined('RSS_INCLUDE_IMAGES') ? (bool) RSS_INCLUDE_IMAGES : true;

$cacheDir = __DIR__ . '/cache';
$cacheFile = $cacheDir . '/rss.xml';
$useCache = $rssCacheDuration > 0;

if ($useCache && !is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}

$cacheIsFresh = $useCache
    && is_file($cacheFile)
    && (time() - filemtime($cacheFile)) < $rssCacheDuration;

if ($cacheIsFresh) {
    $lastModified = gmdate('D, d M Y H:i:s T', filemtime($cacheFile));
    header('Last-Modified: ' . $lastModified);

    $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
    if ($ifModifiedSince !== '') {
        $ifModifiedTime = strtotime($ifModifiedSince);
        if ($ifModifiedTime !== false && $ifModifiedTime >= filemtime($cacheFile)) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    }

    readfile($cacheFile);
    exit;
}

$sql = "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image,
               p.published_at, p.created_at,
               c.name as category_name,
               u.username as author_name, u.email as author_email
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.status = 'published'
          AND (p.published_at IS NULL OR p.published_at <= NOW())
        ORDER BY p.published_at DESC
        LIMIT ?";

$posts = db_fetch_all($sql, 'i', [$rssLimit]);

$lastBuildTimestamp = 0;
foreach ($posts as $post) {
    $dateValue = $post['published_at'] ?? $post['created_at'] ?? '';
    if ($dateValue !== '') {
        $timestamp = strtotime($dateValue);
        if ($timestamp !== false && $timestamp > $lastBuildTimestamp) {
            $lastBuildTimestamp = $timestamp;
        }
    }
}

if ($lastBuildTimestamp === 0) {
    $lastBuildTimestamp = time();
}

$lastBuildDate = gmdate('D, d M Y H:i:s T', $lastBuildTimestamp);

function rss_format_description($post, $includeImages, $length)
{
    $text = make_excerpt($post, $length);
    $text = esc($text);

    $html = '';
    if ($includeImages && !empty($post['featured_image'])) {
        $imageUrl = resolve_image_url($post['featured_image']);
        $html .= '<img src="' . esc($imageUrl) . '" alt="' . esc($post['title']) . '" /><br/>';
    }

    $html .= $text;

    return str_replace(']]>', ']]]]><![CDATA[>', $html);
}

function rss_image_mime_type($url)
{
    $path = parse_url((string) $url, PHP_URL_PATH);
    $extension = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));

    switch ($extension) {
        case 'png':
            return 'image/png';
        case 'gif':
            return 'image/gif';
        case 'webp':
            return 'image/webp';
        case 'svg':
            return 'image/svg+xml';
        case 'jpeg':
        case 'jpg':
            return 'image/jpeg';
        default:
            return 'image/jpeg';
    }
}

ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
<channel>
    <title><?= esc(SITE_NAME); ?></title>
    <link><?= esc(BASE_URL); ?></link>
    <description><?= esc(SITE_DESCRIPTION); ?></description>
    <language>en-us</language>
    <lastBuildDate><?= esc($lastBuildDate); ?></lastBuildDate>
<?php foreach ($posts as $post): ?>
    <?php
    $postUrl = BASE_URL . '/post.php?slug=' . $post['slug'];
    $commentsUrl = $postUrl . '#comments';
    $pubDateSource = $post['published_at'] ?? $post['created_at'] ?? '';
    $pubDateTimestamp = $pubDateSource !== '' ? strtotime($pubDateSource) : time();
    $pubDate = gmdate('D, d M Y H:i:s T', $pubDateTimestamp);
    $description = rss_format_description($post, $rssIncludeImages, $rssDescriptionLength);

    $author = '';
    if (!empty($post['author_email'])) {
        $author = $post['author_email'];
        if (!empty($post['author_name'])) {
            $author .= ' (' . $post['author_name'] . ')';
        }
    } elseif (!empty($post['author_name'])) {
        $author = $post['author_name'];
    }
    ?>
    <item>
        <title><?= esc($post['title']); ?></title>
        <link><?= esc($postUrl); ?></link>
        <description><![CDATA[<?= $description; ?>]]></description>
        <pubDate><?= esc($pubDate); ?></pubDate>
        <guid isPermaLink="true"><?= esc($postUrl); ?></guid>
        <?php if (!empty($post['category_name'])): ?>
        <category><?= esc($post['category_name']); ?></category>
        <?php endif; ?>
        <?php if ($author !== ''): ?>
        <author><?= esc($author); ?></author>
        <?php endif; ?>
        <?php if ($rssIncludeImages && !empty($post['featured_image'])): ?>
        <?php $imageUrl = resolve_image_url($post['featured_image']); ?>
        <enclosure url="<?= esc($imageUrl); ?>" type="<?= esc(rss_image_mime_type($imageUrl)); ?>" />
        <?php endif; ?>
        <comments><?= esc($commentsUrl); ?></comments>
    </item>
<?php endforeach; ?>
</channel>
</rss>
<?php
$rssContent = ob_get_clean();

if ($useCache && is_dir($cacheDir) && is_writable($cacheDir)) {
    file_put_contents($cacheFile, $rssContent, LOCK_EX);
}

header('Last-Modified: ' . $lastBuildDate);
echo $rssContent;
