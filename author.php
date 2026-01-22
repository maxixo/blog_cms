<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Author - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Author</h1>
    <div class="card">
        <p class="muted">Author profile and posts will render here.</p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
