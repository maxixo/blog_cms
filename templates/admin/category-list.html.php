<section class="container">
    <h1><?= esc($pageHeading ?? 'Categories'); ?></h1>
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

    <div class="actions-bar">
        <a href="<?= BASE_URL . '/admin/category-create.php'; ?>" class="btn btn-primary">
            + Create Category
        </a>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="categories-list">
            <?php foreach ($categories as $category): ?>
                <div class="card category-item">
                    <div class="category-info">
                        <h3><?= esc($category['name']); ?></h3>
                        <div class="category-meta">
                            <span class="muted">Slug:</span> <code><?= esc($category['slug']); ?></code>
                        </div>
                        <?php if (!empty($category['description'])): ?>
                            <p class="category-description muted">
                                <?= esc($category['description']); ?>
                            </p>
                        <?php endif; ?>
                        <div class="category-stats">
                            <span class="badge">
                                <?= (int) $category['post_count']; ?> post(s)
                            </span>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="<?= BASE_URL . '/admin/category-edit.php?id=' . $category['id']; ?>" class="btn btn-sm">
                            Edit
                        </a>
                        <form method="POST" action="<?= BASE_URL . '/admin/categories.php'; ?>" class="inline-form delete-category-form">
                            <input type="hidden" name="csrf_token" value="<?= esc($csrfToken ?? ''); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $category['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card state-empty">
            <h2>No Categories Yet</h2>
            <p class="muted">Create your first category to start organizing your posts.</p>
            <a href="<?= BASE_URL . '/admin/category-create.php'; ?>" class="btn btn-primary">
                Create Category
            </a>
        </div>
    <?php endif; ?>
</section>

<script nonce="<?= esc(CSP_NONCE ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.delete-category-form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!confirm('Are you sure you want to delete this category?')) {
                event.preventDefault();
            }
        });
    });
});
</script>
