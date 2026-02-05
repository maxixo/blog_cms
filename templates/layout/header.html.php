<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? SITE_NAME); ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? SITE_DESCRIPTION); ?>">
    <?php if (!empty($metaKeywords)): ?>
        <meta name="keywords" content="<?= esc($metaKeywords); ?>">
    <?php endif; ?>
    <?php if (!empty($canonicalUrl)): ?>
        <link rel="canonical" href="<?= esc($canonicalUrl); ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?= esc(ASSETS_URL); ?>/css/style.css">
    <?php if (!empty($bodyClass) && strpos($bodyClass, 'admin-page') !== false): ?>
        <link rel="stylesheet" href="<?= esc(ASSETS_URL); ?>/css/admin.css">
    <?php endif; ?>
    <?php if (!empty($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?= esc($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php // Load TinyMCE in HEAD for admin pages ?>
    <?php if (!empty($additionalJs)): ?>
        <?php foreach ($additionalJs as $js): ?>
            <?php if (strpos($js, 'tinymce') !== false): ?>
                <script src="<?= esc($js); ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= esc($bodyClass ?? ''); ?>">
<?php require __DIR__ . '/nav.html.php'; ?>
<main class="site-main">
