<header class="site-header">
    <div class="container site-header-inner">
        <div class="site-brand">
            <a class="site-logo" href="<?= esc(BASE_URL); ?>/">
                <?= esc(SITE_NAME); ?>
            </a>
            <p class="site-tagline"><?= esc(SITE_TAGLINE); ?></p>
        </div>
        <nav class="site-nav" aria-label="Primary">
            <a class="nav-link" href="<?= esc(BASE_URL); ?>/index.php">Home</a>
            <a class="nav-link" href="<?= esc(BASE_URL); ?>/search.php">Search</a>
            <a class="nav-link" href="<?= esc(BASE_URL); ?>/about.php">About</a>
            <div class="nav-auth">
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/login.php">Login</a>
                <span class="nav-sep">/</span>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/register.php">Register</a>
            </div>
        </nav>
    </div>
</header>
