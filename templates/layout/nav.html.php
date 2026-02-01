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
                <?php if (isLoggedIn()): ?>
                    <?php 
                        $currentUser = getCurrentUser();
                        $username = $currentUser['username'] ?? 'User';
                        $initials = getInitials($username);
                    ?>
                    <div class="user-menu">
                        <div class="user-avatar" title="<?= esc($username); ?>">
                            <?= esc($initials); ?>
                        </div>
                        <div class="user-dropdown">
                            <div class="user-dropdown-header">
                                <span class="user-dropdown-name"><?= esc($username); ?></span>
                            </div>
                            <div class="user-dropdown-links">
                                <a href="<?= esc(BASE_URL); ?>/admin/profile.php" class="user-dropdown-link">
                                    <i class="user-icon">👤</i> Profile
                                </a>
                                <a href="<?= esc(BASE_URL); ?>/logout.php" class="user-dropdown-link">
                                    <i class="user-icon">🚪</i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="<?= esc(BASE_URL); ?>/login.php">Login</a>
                    <span class="nav-sep">/</span>
                    <a class="nav-link" href="<?= esc(BASE_URL); ?>/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
