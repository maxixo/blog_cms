<section class="container">
    <div class="admin-header">
        <div>
            <h1><?= esc($pageHeading ?? 'Posts'); ?></h1>
            <p><?= esc($pageDescription ?? ''); ?></p>
        </div>
        <a href="/blog_cms/admin/post-create.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create New Post
        </a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <h3>No posts yet</h3>
            <p>Create your first blog post to get started.</p>
            <a href="/blog_cms/admin/post-create.php" class="btn btn-primary">Create Post</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table" id="postsTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <div class="post-title-cell">
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <img src="<?= esc(BASE_URL . '/' . $post['featured_image']); ?>" 
                                             alt="" class="post-thumbnail">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= esc($post['title']); ?></strong>
                                        <?php if ($post['status'] === 'draft'): ?>
                                            <span class="badge badge-draft">Draft</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($post['author_name'] ?? 'Unknown'); ?></td>
                            <td><?= esc($post['category_name'] ?? 'Uncategorized'); ?></td>
                            <td>
                                <span class="badge badge-<?= $post['status'] === 'published' ? 'success' : 'secondary'; ?>">
                                    <?= ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td><?= number_format($post['views'] ?? 0); ?></td>
                            <td>
                                <?= date('M j, Y', strtotime($post['created_at'])); ?>
                                <?php if ($post['published_at']): ?>
                                    <br><small class="text-muted">Published: <?= date('M j, Y', strtotime($post['published_at'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($post['status'] === 'published'): ?>
                                        <a href="/blog_cms/post.php?slug=<?= esc($post['slug']); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline" 
                                           title="View">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                <polyline points="15 3 21 3 21 9"/>
                                                <line x1="10" y1="14" x2="21" y2="3"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="/blog_cms/admin/post-edit.php?id=<?= $post['id']; ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                    
                                    <form method="POST" action="/blog_cms/admin/posts.php" 
                                          class="delete-form" 
                                          onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($posts) > 10): ?>
            <div class="pagination">
                <span class="pagination-info">
                    Showing <?= count($posts); ?> posts
                </span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete form submissions
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const confirmed = confirm('Are you sure you want to delete this post? This action cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // Search functionality (optional enhancement)
    const searchInput = document.getElementById('searchPosts');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#postsTable tbody tr');
            
            rows.forEach(function(row) {
                const title = row.querySelector('.post-title-cell strong')?.textContent.toLowerCase() || '';
                const author = row.querySelectorAll('td')[1]?.textContent.toLowerCase() || '';
                const category = row.querySelectorAll('td')[2]?.textContent.toLowerCase() || '';
                
                const matches = title.includes(searchTerm) || 
                               author.includes(searchTerm) || 
                               category.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
            });
        });
    }

    // Filter by status (optional enhancement)
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function(e) {
            const status = e.target.value;
            const rows = document.querySelectorAll('#postsTable tbody tr');
            
            rows.forEach(function(row) {
                const statusBadge = row.querySelector('.badge');
                const postStatus = statusBadge?.textContent.toLowerCase() || '';
                
                if (status === 'all' || postStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
