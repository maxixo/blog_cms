<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Author - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Author</h1>
    <div class="card state-card state-empty">
        <h2>No author selected</h2>
        <p class="muted">Pick an author to see their profile and posts.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
