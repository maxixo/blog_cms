<?php
$postUrl = build_query_url(BASE_URL . '/post.php', ['slug' => $post['slug'] ?? '', 'id' => $post['id'] ?? '']);
$categoryListBase = $listBaseUrl ?? (BASE_URL . '/index.php');
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
                <a href="<?= esc(build_query_url($categoryListBase, ['category' => $post['category_slug']])); ?>">
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
