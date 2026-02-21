<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Tag - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Tag</h1>
    <div class="card state-card state-empty">
        <h2>No tag selected</h2>
        <p class="muted">Choose a tag from the sidebar to see related posts.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
