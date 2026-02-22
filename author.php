<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Author - ' . SITE_NAME;
$canonicalUrl = BASE_URL . '/author.php';

require_once __DIR__ . '/templates/layout/header.html.php';
?>
<section class="container">
    <h1>Author</h1>
    <div class="card state-card state-empty">
        <h2>No author selected</h2>
        <p class="muted">Pick an author to see their profile and posts.</p>
    </div>
</section>
<?php require_once __DIR__ . '/templates/layout/footer.html.php'; ?>
