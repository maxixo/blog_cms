<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/controllers/PostsController.php';

$slug = $_GET['slug'] ?? '';

// Set page info for categories list
$pageTitle = 'Categories - ' . SITE_NAME;
$metaDescription = 'Browse all categories available on ' . SITE_NAME;
$canonicalUrl = BASE_URL . '/category.php';

if (empty($slug)) {
    // Show all categories
    $categoryModel = new Category();
    $categories = $categoryModel->getAllWithCounts();
    
    require_once __DIR__ . '/templates/layout/header.html.php';
    ?>
    <section class="container">
        <div class="layout">
            <div class="content">
                <h1>All Categories</h1>
                <p class="muted">Browse posts by topic</p>
                
                <?php if (!empty($categories)): ?>
                    <div class="categories-grid">
                        <?php foreach ($categories as $category): ?>
                            <a href="<?= esc(BASE_URL . '/category.php?slug=' . $category['slug']); ?>" class="category-card">
                                <h2><?= esc($category['name']); ?></h2>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="muted"><?= esc($category['description']); ?></p>
                                <?php endif; ?>
                                <div class="category-stats">
                                    <span class="badge badge-secondary">
                                        <?= $category['post_count']; ?> post<?= $category['post_count'] !== 1 ? 's' : ''; ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card state-card state-empty">
                        <h2>No Categories Yet</h2>
                        <p class="muted">Categories haven't been created yet.</p>
                        <a class="button-link" href="<?= BASE_URL . '/index.php'; ?>">Browse All Posts</a>
                    </div>
                <?php endif; ?>
            </div>
            <aside class="sidebar">
                <?php require __DIR__ . '/templates/home/sidebar.html.php'; ?>
            </aside>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/templates/layout/footer.html.php';
    exit;
}

// Get specific category
$categoryModel = new Category();
$category = $categoryModel->getBySlug($slug);

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/templates/layout/header.html.php';
    ?>
    <section class="container">
        <div class="card state-card state-empty">
            <h2>Category Not Found</h2>
            <p class="muted">The category you're looking for doesn't exist.</p>
            <a class="button-link" href="<?= BASE_URL . '/category.php'; ?>">Browse All Categories</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/templates/layout/footer.html.php';
    exit;
}

// Set category parameter for PostsController
$_GET['category'] = $slug;

// Use PostsController to get posts in this category
$postsController = new PostsController();
$data = $postsController->index();
extract($data);

// Update page info for specific category
$pageTitle = esc($category['name']) . ' - ' . SITE_NAME;
$metaDescription = !empty($category['description']) 
    ? esc($category['description']) 
    : 'Browse all posts in ' . esc($category['name']) . ' category.';
$canonicalUrl = build_query_url(BASE_URL . '/category.php', ['slug' => $slug]);

require_once __DIR__ . '/templates/layout/header.html.php';
?>

<section class="container">
    <div class="layout">
        <div class="content">
            <div class="category-header">
                <h1><?= esc($category['name']); ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="muted"><?= esc($category['description']); ?></p>
                <?php endif; ?>
                <div class="category-stats">
                    <span class="muted">
                        <?= $totalPosts; ?> post<?= $totalPosts !== 1 ? 's' : ''; ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <?php require __DIR__ . '/templates/post/card.html.php'; ?>
                <?php endforeach; ?>

                <?php if ($totalPages > 1): ?>
                    <?php
                    $baseParams = ['slug' => $slug];
                    ?>
                    <nav class="pagination card" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <?php
                            $prevParams = $baseParams;
                            $prevParams['page'] = $page - 1;
                            ?>
                            <a class="page-link page-prev" href="<?= esc(build_query_url(BASE_URL . '/category.php', $prevParams)); ?>">Previous</a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php
                                $pageParams = $baseParams;
                                $pageParams['page'] = $i;
                                $pageUrl = build_query_url(BASE_URL . '/category.php', $pageParams);
                                ?>
                                <?php if ($i === $page): ?>
                                    <span class="page-link is-current" aria-current="page"><?= esc($i); ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="<?= esc($pageUrl); ?>"><?= esc($i); ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <?php
                            $nextParams = $baseParams;
                            $nextParams['page'] = $page + 1;
                            ?>
                            <a class="page-link page-next" href="<?= esc(build_query_url(BASE_URL . '/category.php', $nextParams)); ?>">Next</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="card state-card state-empty">
                    <h2>No Posts Yet</h2>
                    <p class="muted">There are no posts in the <?= esc($category['name']); ?> category yet.</p>
                    <div class="state-actions">
                        <a class="button-link" href="<?= BASE_URL . '/category.php'; ?>">Browse All Categories</a>
                        <a class="button-link" href="<?= BASE_URL . '/posts.php'; ?>">View All Posts</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <aside class="sidebar">
            <?php require __DIR__ . '/templates/home/sidebar.html.php'; ?>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/templates/layout/footer.html.php'; ?>