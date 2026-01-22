<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = SITE_NAME . ' - ' . SITE_TAGLINE;
$seoDescription = SITE_DESCRIPTION;
$seoCanonical = BASE_URL . '/';

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Latest Posts</h1>
    <div class="card">
        <h2>Welcome to your Blog CMS</h2>
        <p class="muted">Homepage listing and filters will render here.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
