<header class="site-header">
    <div class="container site-header-inner">
        <div class="site-brand">
            <a class="site-logo" href="<?= esc(BASE_URL); ?>/">
                <?= esc(SITE_NAME); ?>
            </a>
            <p class="site-tagline"><?= esc(SITE_TAGLINE); ?></p>
        </div>
        <nav class="site-nav" aria-label="Primary">
            <div class="nav-links">
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/index.php">Home</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/posts.php">Posts</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/category.php">Categories</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/search.php">Search</a>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/about.php">About</a>
            </div>
            <form class="nav-search" method="get" action="<?= esc(BASE_URL); ?>/search.php" role="search">
                <label class="sr-only" for="nav-search-input">Search posts</label>
                <input id="nav-search-input" type="search" name="q" placeholder="Search posts" value="<?= esc($_GET['q'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
            <div class="nav-auth">
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/login.php">Login</a>
                <span class="nav-sep">/</span>
                <a class="nav-link" href="<?= esc(BASE_URL); ?>/register.php">Register</a>
            </div>
        </nav>
    </div>
</header>
