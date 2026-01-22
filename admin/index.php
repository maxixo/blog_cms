<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Admin Dashboard - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Admin Dashboard</h1>
    <p>Admin stats and shortcuts will appear here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
