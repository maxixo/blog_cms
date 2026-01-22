<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Manage Users - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Users</h1>
    <p>Manage user accounts and roles here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
