<header class="site-header">
    <div class="container site-header-inner">
        <div class="site-brand">
            <a class="site-logo" href="<?= esc(BASE_PATH); ?>/">
                <?= esc(SITE_NAME); ?>
            </a>
            <p class="site-tagline"><?= esc(SITE_TAGLINE); ?></p>
        </div>
        <nav class="site-nav" aria-label="Primary">
            <div class="nav-links">
                <a class="nav-link" href="<?= esc(BASE_PATH); ?>/index.php">Home</a>
                <a class="nav-link" href="<?= esc(BASE_PATH); ?>/posts.php">Posts</a>
                <div class="nav-dropdown">
                    <a class="nav-link nav-dropdown-trigger" href="<?= esc(BASE_PATH); ?>/category.php">
                        Categories
                        <span class="dropdown-arrow">▼</span>
                    </a>
                    <div class="dropdown-menu">
                        <?php if (!empty($navCategories)): ?>
                            <?php foreach ($navCategories as $category): ?>
                                <a href="<?= esc(BASE_PATH . '/category.php?slug=' . urlencode((string) $category['slug'])); ?>" class="dropdown-item">
                                    <?= esc($category['name']); ?>
                                    <span class="badge badge-secondary"><?= esc($category['post_count']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="dropdown-item dropdown-empty">
                                <span class="muted">No categories available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <a class="nav-link" href="<?= esc(BASE_PATH); ?>/search.php">Search</a>
                <a class="nav-link" href="<?= esc(BASE_PATH); ?>/about.php">About</a>
            </div>
            <form class="nav-search" method="get" action="<?= esc(BASE_PATH); ?>/search.php" role="search">
                <label class="sr-only" for="nav-search-input">Search posts</label>
                <input
                    id="nav-search-input"
                    type="search"
                    name="q"
                    placeholder="Search posts..."
                    aria-label="Search posts"
                    value="<?= esc($_GET['q'] ?? ''); ?>"
                >
                <button type="submit" aria-label="Submit search">Search</button>
            </form>
            <div class="nav-auth">
                <?php if (isLoggedIn()): ?>
                    <?php 
                        $currentUser = getCurrentUser();
                        $username = $currentUser['username'] ?? 'User';
                        $initials = getInitials($username);
                        $isAdminPage = !empty($bodyClass) && strpos($bodyClass, 'admin-page') !== false;
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
                                <?php if ($isAdminPage): ?>
                                    <a href="<?= esc(BASE_PATH); ?>/index.php" class="user-dropdown-link">
                                        <i class="user-icon">🏠</i> View Site
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/index.php" class="user-dropdown-link">
                                        <i class="user-icon">📊</i> Dashboard
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/posts.php" class="user-dropdown-link">
                                        <i class="user-icon">📝</i> Posts
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/post-create.php" class="user-dropdown-link">
                                        <i class="user-icon">➕</i> Add Post
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/categories.php" class="user-dropdown-link">
                                        <i class="user-icon">📁</i> Categories
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/comments.php" class="user-dropdown-link">
                                        <i class="user-icon">💬</i> Comments
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/users.php" class="user-dropdown-link">
                                        <i class="user-icon">👥</i> Users
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/profile.php" class="user-dropdown-link">
                                        <i class="user-icon">👤</i> Profile
                                    </a>
                                <?php else: ?>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/index.php" class="user-dropdown-link">
                                        <i class="user-icon">⚙️</i> Admin Panel
                                    </a>
                                    <a href="<?= esc(BASE_PATH); ?>/admin/profile.php" class="user-dropdown-link">
                                        <i class="user-icon">👤</i> Profile
                                    </a>
                                <?php endif; ?>
                                <a href="<?= esc(BASE_PATH); ?>/logout.php" class="user-dropdown-link">
                                    <i class="user-icon">🚪</i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="<?= esc(BASE_PATH); ?>/login.php">Login</a>
                    <span class="nav-sep">/</span>
                    <a class="nav-link" href="<?= esc(BASE_PATH); ?>/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
