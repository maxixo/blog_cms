<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'About - ' . SITE_NAME;
$metaDescription = SITE_DESCRIPTION;
$bodyClass = 'about-page';

require_once __DIR__ . '/templates/layout/header.html.php';
require_once __DIR__ . '/templates/static/about.html.php';
require_once __DIR__ . '/templates/layout/footer.html.php';
