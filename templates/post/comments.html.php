<section class="card post-comments">
    <h2>Comments</h2>
    <?php if (empty($comments)): ?>
        <div class="state state-empty state-compact">
            <p class="muted">No comments yet. Start the conversation when you are ready.</p>
        </div>
    <?php else: ?>
        <ul class="comment-list">
            <?php foreach ($comments as $comment): ?>
                <?php require __DIR__ . '/comment-item.html.php'; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
