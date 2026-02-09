<section class="container">
    <h1><?= esc($pageHeading ?? 'Comments'); ?></h1>
    <p><?= esc($pageDescription ?? ''); ?></p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= esc($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= esc($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="filter-bar">
        <a href="<?= BASE_URL . '/admin/comments.php?status=all'; ?>" class="btn <?= ($currentStatus ?? 'all') === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">
            All (<?= $totalComments ?? 0; ?>)
        </a>
        <a href="<?= BASE_URL . '/admin/comments.php?status=approved'; ?>" class="btn <?= ($currentStatus ?? 'all') === 'approved' ? 'btn-primary' : 'btn-secondary'; ?>">
            Approved
        </a>
        <a href="<?= BASE_URL . '/admin/comments.php?status=spam'; ?>" class="btn <?= ($currentStatus ?? 'all') === 'spam' ? 'btn-primary' : 'btn-secondary'; ?>">
            Spam
        </a>
        <a href="<?= BASE_URL . '/admin/comments.php?status=rejected'; ?>" class="btn <?= ($currentStatus ?? 'all') === 'rejected' ? 'btn-primary' : 'btn-secondary'; ?>">
            Rejected
        </a>
    </div>

    <?php if (!empty($comments)): ?>
        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
                <div class="card comment-item">
                    <div class="comment-header">
                        <div class="comment-author">
                            <?php if (!empty($comment['author_avatar'])): ?>
                                <img src="<?= esc($comment['author_avatar']); ?>" alt="" class="comment-avatar">
                            <?php endif; ?>
                            <div>
                                <strong><?= esc($comment['author_name'] ?? 'Unknown'); ?></strong>
                                <?php if (!empty($comment['post_title'])): ?>
                                    <span class="muted">on <a href="<?= BASE_URL . '/post.php?slug=' . esc($comment['post_slug'] ?? ''); ?>"><?= esc($comment['post_title']); ?></a></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="comment-date">
                            <?= esc(date('M j, Y \a\t g:i A', strtotime($comment['created_at']))); ?>
                        </span>
                    </div>
                    <div class="comment-body">
                        <p><?= esc($comment['content']); ?></p>
                    </div>
                    <div class="comment-footer">
                        <span class="badge badge-<?= $comment['status']; ?>">
                            <?= esc(ucfirst($comment['status'])); ?>
                        </span>
                        <form method="POST" action="<?= BASE_URL . '/admin/comments.php'; ?>" class="inline-form"
                              onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            <input type="hidden" name="csrf_token" value="<?= esc($csrfToken ?? ''); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $comment['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($totalPages) && $totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= build_query_url(BASE_URL . '/admin/comments.php', ['page' => $currentPage - 1, 'status' => $currentStatus]); ?>" class="btn">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="btn btn-primary"><?= $i; ?></span>
                    <?php else: ?>
                        <a href="<?= build_query_url(BASE_URL . '/admin/comments.php', ['page' => $i, 'status' => $currentStatus]); ?>" class="btn">
                            <?= $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= build_query_url(BASE_URL . '/admin/comments.php', ['page' => $currentPage + 1, 'status' => $currentStatus]); ?>" class="btn">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="card">
            <p class="muted">No comments found.</p>
        </div>
    <?php endif; ?>
</section>
