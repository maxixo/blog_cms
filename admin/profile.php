<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$seoTitle = 'Edit Profile - ' . SITE_NAME;
$bodyClass = 'admin-page';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="container">
    <h1>Profile</h1>
    <p>Update your profile details here.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
