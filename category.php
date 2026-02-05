<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/controllers/PostsController.php';

$slug = $_GET['slug'] ?? '';

// Get category
$categoryModel = new Category();
$category = $categoryModel->getBySlug($slug);

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="container">
        <div class="card state-card state-empty">
            <h2>Category Not Found</h2>
            <p class="muted">The category you're looking for doesn't exist.</p>
            <a href="<?= BASE_URL . '/index.php'; ?>" class="btn">Return Home</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Set category parameter for PostsController
$_GET['category'] = $slug;

// Use PostsController to get posts in this category
$postsController = new PostsController();
$data = $postsController->index();
extract($data);

// Update page info
$pageHeading = esc($category['name']);
$pageTitle = esc($category['name']) . ' - ' . SITE_NAME;
$metaDescription = !empty($category['description']) 
    ? esc($category['description']) 
    : 'Browse all posts in ' . esc($category['name']) . ' category.';
$canonicalUrl = build_query_url(BASE_URL . '/category.php', ['slug' => $slug]);

require_once __DIR__ . '/templates/layout/header.html.php';
?>

<section class="container">
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
        <div class="posts-list">
            <?php foreach ($posts as $post): ?>
                <?php require __DIR__ . '/templates/post/card.html.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?= build_query_url(BASE_URL . '/category.php', array_merge(['slug' => $slug], $baseParams, ['page' => $page - 1])); ?>" class="btn">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="btn btn-primary"><?= $i; ?></span>
                    <?php else: ?>
                        <a href="<?= build_query_url(BASE_URL . '/category.php', array_merge(['slug' => $slug], $baseParams, ['page' => $i])); ?>" class="btn">
                            <?= $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= build_query_url(BASE_URL . '/category.php', array_merge(['slug' => $slug], $baseParams, ['page' => $page + 1])); ?>" class="btn">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="card state-card state-empty">
            <h2>No Posts Yet</h2>
            <p class="muted">There are no posts in the <?= esc($category['name']); ?> category yet.</p>
            <a href="<?= BASE_URL . '/index.php'; ?>" class="btn">Browse All Posts</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/templates/layout/footer.html.php'; ?>