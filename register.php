<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$seoTitle = 'Register - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Register</h1>
    <form class="card" method="post" action="">
        <label>
            Name
            <input type="text" name="name" required>
        </label>
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Create Account</button>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
