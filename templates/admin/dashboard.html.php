<section class="container">
    <div class="admin-header">
        <div>
            <h1><?= esc($pageHeading ?? 'Admin Dashboard'); ?></h1>
            <p><?= esc($pageDescription ?? ''); ?></p>
        </div>
        <a href="<?= esc(BASE_URL); ?>/admin/post-create.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create New Post
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($totalPosts ?? 0); ?></div>
                <div class="stat-label">Total Posts</div>
                <div class="stat-meta">
                    <span class="stat-success"><?= number_format($publishedPosts ?? 0); ?> published</span>
                    <span class="stat-secondary"><?= number_format($draftPosts ?? 0); ?> drafts</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-secondary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($totalUsers ?? 0); ?></div>
                <div class="stat-label">Total Users</div>
                <div class="stat-meta">
                    <span class="stat-info">Registered members</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-accent">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($totalComments ?? 0); ?></div>
                <div class="stat-label">Total Comments</div>
                <div class="stat-meta">
                    <?php if ($pendingComments ?? 0 > 0): ?>
                        <span class="stat-warning"><?= number_format($pendingComments); ?> pending</span>
                    <?php else: ?>
                        <span class="stat-success">All reviewed</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?= number_format($totalCategories ?? 0); ?></div>
                <div class="stat-label">Categories</div>
                <div class="stat-meta">
                    <span class="stat-info">Content sections</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="quick-actions-grid">
            <a href="<?= esc(BASE_URL); ?>/admin/post-create.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">New Post</div>
                    <div class="quick-action-desc">Create a blog post</div>
                </div>
            </a>

            <a href="<?= esc(BASE_URL); ?>/admin/categories.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        <line x1="12" y1="11" x2="12" y2="17"/>
                        <line x1="9" y1="14" x2="15" y2="14"/>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Add Category</div>
                    <div class="quick-action-desc">Organize content</div>
                </div>
            </a>

            <a href="<?= esc(BASE_URL); ?>/admin/comments.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Manage Comments</div>
                    <div class="quick-action-desc">Moderate discussions</div>
                </div>
            </a>

            <a href="<?= esc(BASE_URL); ?>/admin/users.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Manage Users</div>
                    <div class="quick-action-desc">User administration</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-grid">
        <!-- Recent Posts -->
        <div class="dashboard-section">
            <div class="dashboard-section-header">
                <h2>Recent Posts</h2>
                <a href="<?= esc(BASE_URL); ?>/admin/posts.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <?php if (empty($recentPosts)): ?>
                <div class="empty-state-small">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <p>No posts yet</p>
                </div>
            <?php else: ?>
                <div class="recent-list">
                    <?php foreach ($recentPosts as $post): ?>
                        <div class="recent-item">
                            <?php if (!empty($post['featured_image'])): ?>
                                <?php $thumbnailUrl = resolve_image_url($post['featured_image']); ?>
                                <img src="<?= esc($thumbnailUrl); ?>" alt="" class="recent-thumbnail">
                            <?php else: ?>
                                <div class="recent-thumbnail-placeholder">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="recent-content">
                                <a href="<?= esc(BASE_URL); ?>/admin/post-edit.php?id=<?= $post['id']; ?>" class="recent-title">
                                    <?= esc($post['title']); ?>
                                </a>
                                <div class="recent-meta">
                                    <span><?= date('M j, Y', strtotime($post['published_at'])); ?></span>
                                    <span>•</span>
                                    <span><?= number_format($post['views'] ?? 0); ?> views</span>
                                </div>
                            </div>
                            <div class="recent-actions">
                                <a href="<?= esc(BASE_URL); ?>/post.php?slug=<?= esc($post['slug']); ?>" 
                                   target="_blank" 
                                   class="btn-icon" title="View">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                </a>
                                <a href="<?= esc(BASE_URL); ?>/admin/post-edit.php?id=<?= $post['id']; ?>" 
                                   class="btn-icon" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Comments -->
        <div class="dashboard-section">
            <div class="dashboard-section-header">
                <h2>Recent Comments</h2>
                <a href="<?= esc(BASE_URL); ?>/admin/comments.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <?php if (empty($recentComments)): ?>
                <div class="empty-state-small">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    <p>No comments yet</p>
                </div>
            <?php else: ?>
                <div class="recent-list">
                    <?php foreach ($recentComments as $comment): ?>
                        <div class="recent-item comment-item">
                            <div class="recent-content">
                                <div class="comment-author">
                                    <strong><?= esc($comment['author_name'] ?? 'Anonymous'); ?></strong>
                                    <?php if ($comment['status'] === 'pending'): ?>
                                        <span class="badge badge-draft">Pending</span>
                                    <?php endif; ?>
                                </div>
                                <div class="comment-text">
                                    <?= esc(mb_substr($comment['content'], 0, 100)); ?><?= mb_strlen($comment['content']) > 100 ? '...' : ''; ?>
                                </div>
                                <div class="recent-meta">
                                    <span><?= date('M j, Y', strtotime($comment['created_at'])); ?></span>
                                    <?php if (!empty($comment['post_title'])): ?>
                                        <span>•</span>
                                        <span><?= esc($comment['post_title']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Most Viewed Posts -->
    <?php if (!empty($mostViewedPosts)): ?>
        <div class="dashboard-section dashboard-section-full">
            <div class="dashboard-section-header">
                <h2>Most Viewed Posts</h2>
                <a href="<?= esc(BASE_URL); ?>/admin/posts.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mostViewedPosts as $post): ?>
                            <tr>
                                <td>
                                    <div class="post-title-cell">
                                        <?php if (!empty($post['featured_image'])): ?>
                                            <?php $thumbnailUrl = resolve_image_url($post['featured_image']); ?>
                                            <img src="<?= esc($thumbnailUrl); ?>" alt="" class="post-thumbnail">
                                        <?php endif; ?>
                                        <strong><?= esc($post['title']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <?= number_format($post['views'] ?? 0); ?> views
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= esc(BASE_URL); ?>/post.php?slug=<?= esc($post['slug']); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline" 
                                           title="View">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                <polyline points="15 3 21 3 21 9"/>
                                                <line x1="10" y1="14" x2="21" y2="3"/>
                                            </svg>
                                        </a>
                                        <a href="<?= esc(BASE_URL); ?>/admin/post-edit.php?id=<?= $post['id']; ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>
