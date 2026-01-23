<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Helper function to safely get URL parameters
function get_query_value($key)
{
    if (!isset($_GET[$key])) {
        return '';
    }

    $value = $_GET[$key];
    if (is_array($value)) {
        $value = reset($value);
    }

    return trim((string) $value);
}

// Helper function to truncate long text
function truncate_text($text, $limit = 160)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    $length = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($length <= $limit) {
        return $text;
    }

    $slice = function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    $slice = rtrim($slice, " \t\n\r\0\x0B,.;:-");
    return $slice . '...';
}

// Helper function to create post excerpt
function make_excerpt($post, $limit = 160)
{
    $excerpt = trim((string) ($post['excerpt'] ?? ''));
    if ($excerpt === '') {
        $excerpt = trim(strip_tags((string) ($post['content'] ?? '')));
    }

    $excerpt = preg_replace('/\s+/', ' ', $excerpt);
    return truncate_text($excerpt, $limit);
}

// Helper function to resolve image URL
function resolve_image_url($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return DEFAULT_OG_IMAGE;
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

// Helper function to format post date
function format_post_date($post)
{
    $value = $post['published_at'] ?? $post['created_at'] ?? '';
    if ($value === '' || $value === null) {
        return '';
    }

    return date('M j, Y', strtotime($value));
}

// Get filter parameters from URL
$categorySlug = get_query_value('category');
$tagSlug = get_query_value('tag');
$authorId = (int) get_query_value('author');
$page = (int) get_query_value('page');
$page = $page > 0 ? $page : 1;
$perPage = 10;

// Build SQL filters
$filters = [];
$params = [];
$types = '';

// Only show published posts
$filters[] = "p.status = 'published'";
$filters[] = '(p.published_at IS NULL OR p.published_at <= NOW())';

// Add category filter if provided
if ($categorySlug !== '') {
    $filters[] = 'c.slug = ?';
    $params[] = $categorySlug;
    $types .= 's';
}

// Add tag filter if provided
if ($tagSlug !== '') {
    $filters[] = 'EXISTS (SELECT 1 FROM post_tags pt JOIN tags t ON t.id = pt.tag_id WHERE pt.post_id = p.id AND t.slug = ?)';
    $params[] = $tagSlug;
    $types .= 's';
}

// Add author filter if provided
if ($authorId > 0) {
    $filters[] = 'p.author_id = ?';
    $params[] = $authorId;
    $types .= 'i';
}

// Build WHERE clause
$whereSql = implode(' AND ', $filters);

// Count total posts for pagination
$countSql = "SELECT COUNT(DISTINCT p.id) AS total
             FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE {$whereSql}";
$countStmt = db_query($countSql, $types, $params);
$countRow = db_fetch_one($countStmt);
$totalPosts = (int) ($countRow['total'] ?? 0);
$totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 0;

// Adjust page if it exceeds total pages
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

// Calculate offset for pagination
$offset = ($page - 1) * $perPage;

// Fetch posts for current page
$postsSql = "SELECT p.id, p.title, p.slug, p.excerpt, p.content, p.featured_image, p.published_at, p.created_at,
                    u.id AS author_id, u.name AS author_name,
                    c.name AS category_name, c.slug AS category_slug
             FROM posts p
             LEFT JOIN users u ON u.id = p.author_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE {$whereSql}
             ORDER BY p.published_at DESC, p.id DESC
             LIMIT ? OFFSET ?";
$postsParams = array_merge($params, [$perPage, $offset]);
$postsTypes = $types . 'ii';
$postsStmt = db_query($postsSql, $postsTypes, $postsParams);
$posts = db_fetch_all($postsStmt);

// Determine page title based on filters
$pageTitle = 'All Posts';
if ($categorySlug !== '') {
    $pageTitle = 'Category: ' . $categorySlug;
} elseif ($tagSlug !== '') {
    $pageTitle = 'Tag: ' . $tagSlug;
} elseif ($authorId > 0 && !empty($posts)) {
    $author = $posts[0]['author_name'] ?? 'Unknown';
    $pageTitle = 'Posts by ' . $author;
}

// Build canonical URL for SEO
$canonicalParams = [];
if ($categorySlug !== '') {
    $canonicalParams['category'] = $categorySlug;
}
if ($tagSlug !== '') {
    $canonicalParams['tag'] = $tagSlug;
}
if ($authorId > 0) {
    $canonicalParams['author'] = $authorId;
}
if ($page > 1) {
    $canonicalParams['page'] = $page;
}

// Set SEO variables
$seoTitle = $pageTitle . ' - ' . SITE_NAME;
$seoDescription = 'Browse all posts on ' . SITE_NAME . ($categorySlug !== '' ? ' in category ' . $categorySlug : '') . ($tagSlug !== '' ? ' tagged with ' . $tagSlug : '');
$seoCanonical = build_query_url(BASE_URL . '/posts.php', $canonicalParams);

// Include header
require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1><?= esc($pageTitle); ?></h1>

    <!-- Show filter message if any filter is active -->
    <?php if ($categorySlug !== '' || $tagSlug !== '' || $authorId > 0): ?>
        <p class="muted">
            Showing posts
            <?php if ($categorySlug !== ''): ?>
                in category <strong><?= esc($categorySlug); ?></strong>
            <?php endif; ?>
            <?php if ($categorySlug !== '' && $tagSlug !== ''): ?>
                and
            <?php endif; ?>
            <?php if ($tagSlug !== ''): ?>
                tagged <strong><?= esc($tagSlug); ?></strong>
            <?php endif; ?>
            <?php if ($authorId > 0 && !empty($posts)): ?>
                by <strong><?= esc($posts[0]['author_name'] ?? 'Unknown'); ?></strong>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <!-- Show empty state if no posts found -->
    <?php if (empty($posts)): ?>
        <div class="card">
            <h2>No posts found</h2>
            <p class="muted">Try adjusting your filters or check back later for new content.</p>
            <?php if ($categorySlug !== '' || $tagSlug !== '' || $authorId > 0): ?>
                <p><a href="<?= esc(BASE_URL); ?>/posts.php">View all posts</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Display posts count -->
        <p class="muted">
            Showing <strong><?= esc($offset + 1); ?> - <?= esc(min($offset + $perPage, $totalPosts)); ?></strong>
            of <strong><?= esc($totalPosts); ?></strong> posts
        </p>

        <!-- Loop through and display each post -->
        <?php foreach ($posts as $post): ?>
            <?php
            $postUrl = build_query_url(BASE_URL . '/post.php', ['slug' => $post['slug'] ?? '', 'id' => $post['id'] ?? '']);
            $imageUrl = resolve_image_url($post['featured_image'] ?? '');
            $excerpt = make_excerpt($post, 180);
            $postDate = format_post_date($post);
            ?>
            <article class="card post-card">
                <a class="post-image" href="<?= esc($postUrl); ?>">
                    <img src="<?= esc($imageUrl); ?>" alt="<?= esc($post['title'] ?? 'Post image'); ?>">
                </a>
                <div class="post-content">
                    <p class="muted">
                        <?php if (!empty($post['author_name'])): ?>
                            By
                            <a href="<?= esc(build_query_url(BASE_URL . '/author.php', ['id' => $post['author_id'] ?? 0])); ?>">
                                <?= esc($post['author_name']); ?>
                            </a>
                        <?php else: ?>
                            By Unknown
                        <?php endif; ?>
                        <?php if ($postDate !== ''): ?>
                            on <?= esc($postDate); ?>
                        <?php endif; ?>
                        <?php if (!empty($post['category_name'])): ?>
                            in
                            <a href="<?= esc(build_query_url(BASE_URL . '/posts.php', ['category' => $post['category_slug']])); ?>">
                                <?= esc($post['category_name']); ?>
                            </a>
                        <?php else: ?>
                            in Uncategorized
                        <?php endif; ?>
                    </p>
                    <h2><a href="<?= esc($postUrl); ?>"><?= esc($post['title'] ?? 'Untitled'); ?></a></h2>
                    <p><?= esc($excerpt); ?></p>
                </div>
            </article>
        <?php endforeach; ?>

        <!-- Pagination controls -->
        <?php if ($totalPages > 1): ?>
            <?php
            $baseParams = [];
            if ($categorySlug !== '') {
                $baseParams['category'] = $categorySlug;
            }
            if ($tagSlug !== '') {
                $baseParams['tag'] = $tagSlug;
            }
            if ($authorId > 0) {
                $baseParams['author'] = $authorId;
            }
            ?>
            <nav class="pagination card">
                <?php if ($page > 1): ?>
                    <?php
                    $prevParams = $baseParams;
                    $prevParams['page'] = $page - 1;
                    ?>
                    <a href="<?= esc(build_query_url(BASE_URL . '/posts.php', $prevParams)); ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $pageParams = $baseParams;
                    $pageParams['page'] = $i;
                    $pageUrl = build_query_url(BASE_URL . '/posts.php', $pageParams);
                    ?>
                    <?php if ($i === $page): ?>
                        <strong><?= esc($i); ?></strong>
                    <?php else: ?>
                        <a href="<?= esc($pageUrl); ?>"><?= esc($i); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <?php
                    $nextParams = $baseParams;
                    $nextParams['page'] = $page + 1;
                    ?>
                    <a href="<?= esc(build_query_url(BASE_URL . '/posts.php', $nextParams)); ?>">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>