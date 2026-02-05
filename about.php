<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'About - ' . SITE_NAME;
$metaDescription = SITE_DESCRIPTION . ' Learn about our blog CMS, editorial workflow, and community of writers.';
$metaKeywords = 'blog CMS, content management, publishing, editorial workflow, writing, content strategy';
$canonicalUrl = BASE_URL . '/about.php';
$bodyClass = 'about-page';

$postCount = db_fetch("SELECT COUNT(*) AS total FROM posts WHERE status = 'published' AND (published_at IS NULL OR published_at <= NOW())");
$categoryCount = db_fetch("SELECT COUNT(*) AS total FROM categories");
$userCount = db_fetch("SELECT COUNT(*) AS total FROM users");
$commentCount = db_fetch("SELECT COUNT(*) AS total FROM comments WHERE status = 'approved'");

$aboutStats = [
    'posts' => (int) ($postCount['total'] ?? 0),
    'categories' => (int) ($categoryCount['total'] ?? 0),
    'users' => (int) ($userCount['total'] ?? 0),
    'comments' => (int) ($commentCount['total'] ?? 0)
];

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/static/about.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
