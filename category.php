<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Category - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Category</h1>
    <div class="card state-card state-empty">
        <h2>No category selected</h2>
        <p class="muted">Choose a category from the sidebar to explore posts.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
