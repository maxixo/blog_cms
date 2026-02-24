<section class="container">
    <div class="posts-page-header">
        <h1><?= esc($pageHeading ?? 'All Posts'); ?></h1>
        <a class="button-link" href="<?= esc(BASE_URL); ?>/post-create.php">Create Post</a>
    </div>

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

    <?php if (!empty($isLoading)): ?>
        <div class="card state-card state-loading" aria-live="polite">
            <div class="loading-line loading-line-lg"></div>
            <div class="loading-line loading-line-md"></div>
            <div class="loading-line loading-line-sm"></div>
        </div>
    <?php elseif (empty($posts)): ?>
        <div class="card state-card state-empty">
            <h2>No posts match this view</h2>
            <p class="muted">Adjust your filters or return to the full archive.</p>
            <div class="state-actions">
                <?php if (!empty($filtersActive)): ?>
                    <a class="button-link" href="<?= esc(BASE_URL); ?>/posts.php">View all posts</a>
                <?php endif; ?>
                <a class="button-link" href="<?= esc(BASE_URL); ?>/index.php">Back to home</a>
            </div>
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
            <nav class="pagination card" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <?php
                    $prevParams = $baseParams;
                    $prevParams['page'] = $page - 1;
                    ?>
                    <a class="page-link page-prev" href="<?= esc(build_query_url(BASE_URL . '/posts.php', $prevParams)); ?>">Previous</a>
                <?php endif; ?>

                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $pageParams = $baseParams;
                        $pageParams['page'] = $i;
                        $pageUrl = build_query_url(BASE_URL . '/posts.php', $pageParams);
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
                    <a class="page-link page-next" href="<?= esc(build_query_url(BASE_URL . '/posts.php', $nextParams)); ?>">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
