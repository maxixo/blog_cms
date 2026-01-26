<li class="comment-item">
    <p class="muted">
        <?= esc($comment['author_name'] ?? 'Anonymous'); ?>
        <?php if (!empty($comment['created_at'])): ?>
            on <?= esc(date('M j, Y', strtotime($comment['created_at']))); ?>
        <?php endif; ?>
    </p>
    <div class="comment-content">
        <?= esc($comment['content'] ?? ''); ?>
    </div>
</li>
