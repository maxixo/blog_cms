<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= esc($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="<?= $formAction ?? ''; ?>">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">

        <div class="form-group">
            <label for="name">Category Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= esc($category['name'] ?? ''); ?>" 
                maxlength="100"
                required
                placeholder="e.g., Technology, Lifestyle, Business"
            >
            <small class="form-help">The name will be displayed to users and used to generate the URL slug.</small>
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input 
                type="text" 
                id="slug" 
                name="slug" 
                value="<?= esc($category['slug'] ?? ''); ?>" 
                readonly
                class="readonly"
            >
            <small class="form-help">This URL-friendly version is automatically generated from the category name.</small>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description" 
                name="description" 
                rows="4"
                placeholder="Optional: Add a brief description of this category"
            ><?= esc($category['description'] ?? ''); ?></textarea>
            <small class="form-help">A short description helps users understand what content is in this category.</small>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL . '/admin/categories.php'; ?>" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <?= isset($category['id']) ? 'Update Category' : 'Create Category'; ?>
            </button>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from category name
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('input', function() {
        const name = this.value;
        const slug = name
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    });
});
</script>