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
<header class="site-header">
    <div class="container site-header-inner">
        <div class="site-brand">
            <a class="site-logo" href="<?= esc(BASE_URL); ?>/">
                <?= esc(SITE_NAME); ?>
            </a>
            <p class="site-tagline"><?= esc(SITE_TAGLINE); ?></p>
        </div>
        <nav class="site-nav" aria-label="Primary">
            <div class="nav-links">
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/index.php">Home</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/posts.php">Posts</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/category.php">Categories</a>
            </div>
            <form class="nav-search" method="get" action="<?= esc(BASE_URL); ?>/search.php" role="search">
                <label class="sr-only" for="nav-search-input">Search posts</label>
                <input id="nav-search-input" type="search" name="q" placeholder="Search posts" value="<?= esc($_GET['q'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
            <div class="nav-auth">
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/login.php">Login</a>
                <span class="nav-sep">/</span>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/register.php">Register</a>
            </div>
        </nav>
    </div>
</header>
<main class="site-main">
