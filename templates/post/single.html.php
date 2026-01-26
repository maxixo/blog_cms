<section class="container">
    <article class="card post-single">
        <h1><?= esc($post['title'] ?? 'Untitled'); ?></h1>
        <p class="muted">
            <?php if (!empty($post['author_name'])): ?>
                By
                <a href="<?= esc(build_query_url(BASE_URL . '/author.php', ['id' => $post['author_id'] ?? 0])); ?>">
                    <?= esc($post['author_name']); ?>
                </a>
            <?php else: ?>
                By Unknown
            <?php endif; ?>
            <?php if (!empty($post['published_at']) || !empty($post['created_at'])): ?>
                on <?= esc(format_post_date($post)); ?>
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

        <?php if (!empty($post['featured_image'])): ?>
            <?php $imageUrl = resolve_image_url($post['featured_image']); ?>
            <img src="<?= esc($imageUrl); ?>" alt="<?= esc($post['title'] ?? 'Post image'); ?>">
        <?php endif; ?>

        <div class="post-body">
            <?= $post['content'] ?? ''; ?>
        </div>
    </article>

    <?php require __DIR__ . '/comments.html.php'; ?>
</section>
