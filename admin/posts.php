<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Manage Posts - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Posts</h1>
    <p>Create, edit, and manage blog posts here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
