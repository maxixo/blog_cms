<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Manage Categories - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Categories</h1>
    <p>Create and organize categories here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
