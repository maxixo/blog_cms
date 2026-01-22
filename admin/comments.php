<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Moderate Comments - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Comments</h1>
    <p>Review and moderate comments here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
