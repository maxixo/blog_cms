<div class="card">
    <h2>Categories</h2>
    <?php if (empty($categories)): ?>
        <div class="state state-empty state-compact">
            <p class="muted">No categories yet.</p>
        </div>
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
        <div class="state state-empty state-compact">
            <p class="muted">No tags yet.</p>
        </div>
    <?php else: ?>
        <div class="tag-cloud">
            <?php foreach ($tags as $tag): ?>
                <?php
                $count = (int) ($tag['tag_count'] ?? 0);
                if (!isset($tagMin, $tagMax) || $tagMax <= $tagMin) {
                    $weight = 2;
                } else {
                    $weight = 1 + (int) floor((($count - $tagMin) / ($tagMax - $tagMin)) * 3);
                }
                $sizes = [1 => '0.85rem', 2 => '1rem', 3 => '1.15rem', 4 => '1.3rem'];
                $size = $sizes[$weight] ?? '1rem';
                $tagClass = 'tag tag-weight-' . $weight;
                ?>
                <a class="<?= esc($tagClass); ?>" href="<?= esc(build_query_url(BASE_URL . '/index.php', ['tag' => $tag['slug']])); ?>" style="font-size: <?= esc($size); ?>;">
                    <?= esc($tag['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Recent Posts</h2>
    <?php if (empty($recentPosts)): ?>
        <div class="state state-empty state-compact">
            <p class="muted">No recent posts yet.</p>
        </div>
    <?php else: ?>
        <ul>
            <?php foreach ($recentPosts as $recent): ?>
                <?php
                $recentUrl = build_query_url(BASE_URL . '/post.php', ['slug' => $recent['slug'] ?? '', 'id' => $recent['id'] ?? '']);
                $recentDate = !empty($recent['published_at']) ? date('M j, Y', strtotime($recent['published_at'])) : '';
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
