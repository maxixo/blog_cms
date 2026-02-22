<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

$seoTitle = $seoTitle ?? SITE_NAME;
$seoDescription = $seoDescription ?? SITE_DESCRIPTION;
$seoCanonical = $seoCanonical ?? BASE_URL . '/';
$seoImage = $seoImage ?? DEFAULT_OG_IMAGE;
$bodyClass = $bodyClass ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($seoTitle); ?></title>
    <meta name="description" content="<?= esc($seoDescription); ?>">
    <link rel="canonical" href="<?= esc($seoCanonical); ?>">

    <meta property="og:title" content="<?= esc($seoTitle); ?>">
    <meta property="og:description" content="<?= esc($seoDescription); ?>">
    <meta property="og:image" content="<?= esc($seoImage); ?>">
    <meta property="og:url" content="<?= esc($seoCanonical); ?>">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="<?= esc(ASSETS_URL); ?>/css/style.css">
</head>
<body class="<?= esc($bodyClass); ?>">
<?php
$navCategories = [];
try {
    $sql = "SELECT 
                c.id, c.name, c.slug,
                COUNT(p.id) as post_count
            FROM categories c
            LEFT JOIN posts p ON p.category_id = c.id 
                AND p.status = 'published'
                AND (p.published_at IS NULL OR p.published_at <= NOW())
            WHERE c.id IN (
                SELECT DISTINCT category_id FROM posts 
                WHERE category_id IS NOT NULL AND status = 'published'
            )
            GROUP BY c.id, c.name, c.slug
            ORDER BY c.name ASC";

    $navCategories = db_fetch_all($sql);
} catch (Exception $e) {
    $navCategories = [];
}

require __DIR__ . '/../templates/layout/nav.html.php';
?>
<main class="site-main">
