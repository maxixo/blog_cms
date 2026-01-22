<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Post - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Post</h1>
    <div class="card">
        <p class="muted">Single post content and comments will render here.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
