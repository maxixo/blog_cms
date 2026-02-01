<section class="container">
    <div class="layout">
        <div class="content">
            <h1><?= esc($pageHeading ?? 'Latest Posts'); ?></h1>

            <?php if (!empty($categorySlug) || !empty($tagSlug)): ?>
                <p class="muted">
                    Filtering by
                    <?php if (!empty($categorySlug)): ?>
                        category <strong><?= esc($categorySlug); ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($categorySlug) && !empty($tagSlug)): ?>
                        and
                    <?php endif; ?>
                    <?php if (!empty($tagSlug)): ?>
                        tag <strong><?= esc($tagSlug); ?></strong>
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
                    <h2>No posts yet</h2>
                    <p class="muted">Once new posts are published, they will show up here.</p>
                    <div class="state-actions">
                        <?php if (!empty($categorySlug) || !empty($tagSlug)): ?>
                            <a class="button-link" href="<?= esc(BASE_URL); ?>/index.php">Clear filters</a>
                        <?php endif; ?>
                        <a class="button-link" href="<?= esc(BASE_URL); ?>/posts.php">Browse all posts</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <?php require __DIR__ . '/../post/card.html.php'; ?>
                <?php endforeach; ?>

                <?php if (!empty($totalPages) && $totalPages > 1): ?>
                    <?php
                    $baseParams = [];
                    if (!empty($categorySlug)) {
                        $baseParams['category'] = $categorySlug;
                    }
                    if (!empty($tagSlug)) {
                        $baseParams['tag'] = $tagSlug;
                    }
                    ?>
                    <nav class="pagination card" aria-label="Pagination">
                        <?php if (!empty($page) && $page > 1): ?>
                            <?php
                            $prevParams = $baseParams;
                            $prevParams['page'] = $page - 1;
                            ?>
                            <a class="page-link page-prev" href="<?= esc(build_query_url(BASE_URL . '/index.php', $prevParams)); ?>">Previous</a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php
                                $pageParams = $baseParams;
                                $pageParams['page'] = $i;
                                $pageUrl = build_query_url(BASE_URL . '/index.php', $pageParams);
                                ?>
                                <?php if ($i === $page): ?>
                                    <span class="page-link is-current" aria-current="page"><?= esc($i); ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="<?= esc($pageUrl); ?>"><?= esc($i); ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if (!empty($page) && $page < $totalPages): ?>
                            <?php
                            $nextParams = $baseParams;
                            $nextParams['page'] = $page + 1;
                            ?>
                            <a class="page-link page-next" href="<?= esc(build_query_url(BASE_URL . '/index.php', $nextParams)); ?>">Next</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <aside class="sidebar">
            <?php require __DIR__ . '/sidebar.html.php'; ?>
        </aside>
    </div>
</section>
