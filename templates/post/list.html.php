<section class="container">
    <h1><?= esc($pageHeading ?? 'All Posts'); ?></h1>

    <?php if (!empty($filtersActive)): ?>
        <p class="muted">
            Showing posts
            <?php if (!empty($categorySlug)): ?>
                in category <strong><?= esc($categorySlug); ?></strong>
            <?php endif; ?>
            <?php if (!empty($categorySlug) && !empty($tagSlug)): ?>
                and
            <?php endif; ?>
            <?php if (!empty($tagSlug)): ?>
                tagged <strong><?= esc($tagSlug); ?></strong>
            <?php endif; ?>
            <?php if (!empty($authorId)): ?>
                by <strong><?= esc($authorName ?: 'Unknown'); ?></strong>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="card">
            <h2>No posts found</h2>
            <p class="muted">Try adjusting your filters or check back later for new content.</p>
            <?php if (!empty($filtersActive)): ?>
                <p><a href="<?= esc(BASE_URL); ?>/posts.php">View all posts</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="muted">
            Showing <strong><?= esc($showingFrom); ?> - <?= esc($showingTo); ?></strong>
            of <strong><?= esc($totalPosts); ?></strong> posts
        </p>

        <?php foreach ($posts as $post): ?>
            <?php require __DIR__ . '/card.html.php'; ?>
        <?php endforeach; ?>

        <?php if (!empty($totalPages) && $totalPages > 1): ?>
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
