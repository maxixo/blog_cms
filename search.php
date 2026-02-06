<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/SearchController.php';

$controller = new SearchController();
$data = $controller->index();
extract($data);

require_once __DIR__ . '/templates/layout/header.html.php';
?>
<section class="container">
    <div class="layout">
        <div class="content">
            <h1><?= $query !== '' ? 'Search Results' : 'Search'; ?></h1>

            <?php if (!empty($error)): ?>
                <div class="card state-card">
                    <h2>Search needs a longer term</h2>
                    <p class="muted"><?= esc($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($query !== '' && empty($error)): ?>
                <p class="muted search-info">
                    Found <strong><?= esc(number_format((int) $total)); ?></strong>
                    result(s) for "<em><?= esc($query); ?></em>"
                    <?php if ($categorySlug !== ''): ?>
                        in category "<strong><?= esc($categorySlug); ?></strong>"
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if (empty($results) && empty($error)): ?>
                <div class="card state-card state-empty">
                    <h2>No results found</h2>
                    <p class="muted">
                        <?php if ($query !== ''): ?>
                            No posts match "<strong><?= esc($query); ?></strong>".
                        <?php else: ?>
                            Enter a search term to find posts.
                        <?php endif; ?>
                    </p>
                    <div class="state-actions">
                        <?php if ($categorySlug !== ''): ?>
                            <a class="button-link" href="<?= esc(build_query_url(BASE_URL . '/search.php', ['q' => $query])); ?>">
                                Clear category filter
                            </a>
                        <?php endif; ?>
                        <a class="button-link" href="<?= esc(BASE_URL); ?>/posts.php">Browse all posts</a>
                    </div>
                </div>
            <?php elseif (!empty($results)): ?>
                <?php foreach ($results as $post): ?>
                    <?php require __DIR__ . '/templates/post/card.html.php'; ?>
                <?php endforeach; ?>

                <?php if (!empty($totalPages) && $totalPages > 1): ?>
                    <nav class="pagination card" aria-label="Search results pagination">
                        <?php if ($page > 1): ?>
                            <?php
                            $prevParams = $baseParams;
                            $prevParams['page'] = $page - 1;
                            ?>
                            <a class="page-link page-prev" href="<?= esc(build_query_url(BASE_URL . '/search.php', $prevParams)); ?>">
                                Previous
                            </a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php
                                $pageParams = $baseParams;
                                $pageParams['page'] = $i;
                                ?>
                                <?php if ($i === $page): ?>
                                    <span class="page-link is-current" aria-current="page"><?= esc($i); ?></span>
                                <?php else: ?>
                                    <a class="page-link" href="<?= esc(build_query_url(BASE_URL . '/search.php', $pageParams)); ?>">
                                        <?= esc($i); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <?php
                            $nextParams = $baseParams;
                            $nextParams['page'] = $page + 1;
                            ?>
                            <a class="page-link page-next" href="<?= esc(build_query_url(BASE_URL . '/search.php', $nextParams)); ?>">
                                Next
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <aside class="sidebar">
            <?php if (!empty($searchHistory)): ?>
                <div class="card">
                    <h2>Recent searches</h2>
                    <ul>
                        <?php foreach ($searchHistory as $term): ?>
                            <li>
                                <a href="<?= esc(build_query_url(BASE_URL . '/search.php', ['q' => $term])); ?>">
                                    <?= esc($term); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php require __DIR__ . '/templates/home/sidebar.html.php'; ?>
        </aside>
    </div>
</section>
<?php require_once __DIR__ . '/templates/layout/footer.html.php'; ?>
