<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

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

function make_excerpt($post, $limit = 160)
{
    $excerpt = trim((string) ($post['excerpt'] ?? ''));
    if ($excerpt === '') {
        $excerpt = trim(strip_tags((string) ($post['content'] ?? '')));
    }

    $excerpt = preg_replace('/\s+/', ' ', $excerpt);
    return truncate_text($excerpt, $limit);
}

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

function format_post_date($post)
{
    $value = $post['published_at'] ?? $post['created_at'] ?? '';
    if ($value === '' || $value === null) {
        return '';
    }

    return date('M j, Y', strtotime($value));
}

$categorySlug = get_query_value('category');
$tagSlug = get_query_value('tag');
$page = (int) get_query_value('page');
$page = $page > 0 ? $page : 1;
$perPage = 10;

$filters = [];
$params = [];
$types = '';

$filters[] = "p.status = 'published'";
$filters[] = '(p.published_at IS NULL OR p.published_at <= NOW())';

if ($categorySlug !== '') {
    $filters[] = 'c.slug = ?';
    $params[] = $categorySlug;
    $types .= 's';
}

if ($tagSlug !== '') {
    $filters[] = 'EXISTS (SELECT 1 FROM post_tags pt JOIN tags t ON t.id = pt.tag_id WHERE pt.post_id = p.id AND t.slug = ?)';
    $params[] = $tagSlug;
    $types .= 's';
}

$whereSql = implode(' AND ', $filters);

$countSql = "SELECT COUNT(DISTINCT p.id) AS total
             FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE {$whereSql}";
$countStmt = db_query($countSql, $types, $params);
$countRow = db_fetch_one($countStmt);
$totalPosts = (int) ($countRow['total'] ?? 0);
$totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 0;

if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

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

$categoriesStmt = db_query(
    "SELECT c.name, c.slug, COUNT(p.id) AS post_count
     FROM categories c
     LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())
     GROUP BY c.id
     ORDER BY c.name ASC"
);
$categories = db_fetch_all($categoriesStmt);

$tagsStmt = db_query(
    "SELECT t.name, t.slug, COUNT(pt.post_id) AS tag_count
     FROM tags t
     JOIN post_tags pt ON pt.tag_id = t.id
     JOIN posts p ON p.id = pt.post_id AND p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())
     GROUP BY t.id
     ORDER BY tag_count DESC, t.name ASC
     LIMIT 20"
);
$tags = db_fetch_all($tagsStmt);

$recentStmt = db_query(
    "SELECT p.id, p.title, p.slug, p.published_at
     FROM posts p
     WHERE p.status = 'published' AND (p.published_at IS NULL OR p.published_at <= NOW())
     ORDER BY p.published_at DESC, p.id DESC
     LIMIT 5"
);
$recentPosts = db_fetch_all($recentStmt);

$tagCounts = array_column($tags, 'tag_count');
$tagMin = $tagCounts ? (int) min($tagCounts) : 0;
$tagMax = $tagCounts ? (int) max($tagCounts) : 0;

$pageTitle = 'Latest Posts';
if ($categorySlug !== '' && $tagSlug !== '') {
    $pageTitle = 'Posts in ' . $categorySlug . ' tagged ' . $tagSlug;
} elseif ($categorySlug !== '') {
    $pageTitle = 'Category: ' . $categorySlug;
} elseif ($tagSlug !== '') {
    $pageTitle = 'Tag: ' . $tagSlug;
}

$canonicalParams = [];
if ($categorySlug !== '') {
    $canonicalParams['category'] = $categorySlug;
}
if ($tagSlug !== '') {
    $canonicalParams['tag'] = $tagSlug;
}
if ($page > 1) {
    $canonicalParams['page'] = $page;
}

$seoTitle = $pageTitle . ' - ' . SITE_NAME;
$seoDescription = SITE_DESCRIPTION;
$seoCanonical = build_query_url(BASE_URL . '/index.php', $canonicalParams);

require_once __DIR__ . '/includes/header.php';
?>
<section class="container">
    <div class="layout">
        <div class="content">
            <h1><?= esc($pageTitle); ?></h1>

            <?php if ($categorySlug !== '' || $tagSlug !== ''): ?>
                <p class="muted">
                    Filtering by
                    <?php if ($categorySlug !== ''): ?>
                        category <strong><?= esc($categorySlug); ?></strong>
                    <?php endif; ?>
                    <?php if ($categorySlug !== '' && $tagSlug !== ''): ?>
                        and
                    <?php endif; ?>
                    <?php if ($tagSlug !== ''): ?>
                        tag <strong><?= esc($tagSlug); ?></strong>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if (empty($posts)): ?>
                <div class="card">
                    <h2>No posts found</h2>
                    <p class="muted">Try a different filter or check back soon.</p>
                </div>
            <?php else: ?>
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
                                    <a href="<?= esc(build_query_url(BASE_URL . '/index.php', ['category' => $post['category_slug']])); ?>">
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

                <?php if ($totalPages > 1): ?>
                    <?php
                    $baseParams = [];
                    if ($categorySlug !== '') {
                        $baseParams['category'] = $categorySlug;
                    }
                    if ($tagSlug !== '') {
                        $baseParams['tag'] = $tagSlug;
                    }
                    ?>
                    <nav class="pagination card">
                        <?php if ($page > 1): ?>
                            <?php
                            $prevParams = $baseParams;
                            $prevParams['page'] = $page - 1;
                            ?>
                            <a href="<?= esc(build_query_url(BASE_URL . '/index.php', $prevParams)); ?>">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php
                            $pageParams = $baseParams;
                            $pageParams['page'] = $i;
                            $pageUrl = build_query_url(BASE_URL . '/index.php', $pageParams);
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
                            <a href="<?= esc(build_query_url(BASE_URL . '/index.php', $nextParams)); ?>">Next</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <aside class="sidebar">
            <div class="card">
                <h2>Categories</h2>
                <?php if (empty($categories)): ?>
                    <p class="muted">No categories yet.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= esc(build_query_url(BASE_URL . '/index.php', ['category' => $category['slug']])); ?>">
                                    <?= esc($category['name']); ?>
                                </a>
                                <span class="muted">(<?= esc($category['post_count']); ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Popular Tags</h2>
                <?php if (empty($tags)): ?>
                    <p class="muted">No tags yet.</p>
                <?php else: ?>
                    <div class="tag-cloud">
                        <?php foreach ($tags as $tag): ?>
                            <?php
                            $count = (int) ($tag['tag_count'] ?? 0);
                            if ($tagMax <= $tagMin) {
                                $weight = 2;
                            } else {
                                $weight = 1 + (int) floor((($count - $tagMin) / ($tagMax - $tagMin)) * 3);
                            }
                            $sizes = [1 => '0.85rem', 2 => '1rem', 3 => '1.15rem', 4 => '1.3rem'];
                            $size = $sizes[$weight] ?? '1rem';
                            ?>
                            <a href="<?= esc(build_query_url(BASE_URL . '/index.php', ['tag' => $tag['slug']])); ?>" style="font-size: <?= esc($size); ?>;">
                                <?= esc($tag['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Recent Posts</h2>
                <?php if (empty($recentPosts)): ?>
                    <p class="muted">No recent posts yet.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($recentPosts as $recent): ?>
                            <?php
                            $recentUrl = build_query_url(BASE_URL . '/post.php', ['slug' => $recent['slug'] ?? '', 'id' => $recent['id'] ?? '']);
                            $recentDate = $recent['published_at'] ? date('M j, Y', strtotime($recent['published_at'])) : '';
                            ?>
                            <li>
                                <a href="<?= esc($recentUrl); ?>"><?= esc($recent['title'] ?? 'Untitled'); ?></a>
                                <?php if ($recentDate !== ''): ?>
                                    <div class="muted"><?= esc($recentDate); ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
