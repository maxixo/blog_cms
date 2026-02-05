<section class="container">
    <div class="admin-header">
        <h1><?= esc($pageHeading ?? 'Post'); ?></h1>
        <p><?= esc($pageDescription ?? ''); ?></p>
        <a href="/blog_cms/admin/posts.php" class="btn btn-secondary">← Back to Posts</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> Please fix the errors below.
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="post-form" id="postForm">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
        
        <div class="form-group">
            <label for="title">
                Title <span class="required">*</span>
            </label>
            <input 
                type="text" 
                name="title" 
                id="title" 
                class="form-control <?= isset($errors['title']) ? 'is-invalid' : ''; ?>"
                value="<?= esc($formData['title'] ?? $post['title'] ?? ''); ?>"
                required
                placeholder="Enter post title"
                maxlength="255"
            >
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= esc($category['id']); ?>" 
                        <?= (isset($formData['category_id']) && $formData['category_id'] == $category['id']) || 
                           (isset($post['category_id']) && $post['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?= esc($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <div class="image-upload-area" id="imageUploadArea">
                <input 
                    type="file" 
                    name="featured_image" 
                    id="featured_image" 
                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                    class="form-control-file"
                >
                <div class="image-preview" id="imagePreview">
                    <?php if (isset($post['featured_image']) && $post['featured_image']): ?>
                        <img src="<?= esc(BASE_URL . '/' . $post['featured_image']); ?>" alt="Featured Image">
                        <button type="button" class="btn-remove-image" id="removeImage">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    <?php else: ?>
                        <div class="image-upload-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <p>Click to upload or drag and drop</p>
                            <small>JPG, PNG, GIF, WebP (max 5MB)</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="content">
                Content <span class="required">*</span>
            </label>
            <textarea 
                name="content" 
                id="content" 
                class="form-control tinymce-editor <?= isset($errors['content']) ? 'is-invalid' : ''; ?>"
                required
            ><?= $formData['content'] ?? $post['content'] ?? ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea 
                name="excerpt" 
                id="excerpt" 
                class="form-control"
                rows="3"
                placeholder="Short description (auto-generated from content if left blank)"
                maxlength="500"
            ><?= esc($formData['excerpt'] ?? $post['excerpt'] ?? ''); ?></textarea>
            <small class="form-text text-muted">
                <?php 
                $excerptLength = strlen($formData['excerpt'] ?? $post['excerpt'] ?? '');
                echo "$excerptLength/500 characters";
                ?>
            </small>
        </div>

        <div class="form-group">
            <label for="meta_description">Meta Description (SEO)</label>
            <textarea 
                name="meta_description" 
                id="meta_description" 
                class="form-control"
                rows="2"
                placeholder="Description for search engines"
                maxlength="160"
            ><?= esc($formData['meta_description'] ?? $post['meta_description'] ?? ''); ?></textarea>
            <small class="form-text text-muted">
                <?php 
                $metaDescLength = strlen($formData['meta_description'] ?? $post['meta_description'] ?? '');
                echo "$metaDescLength/160 characters";
                ?>
            </small>
        </div>

        <div class="form-group">
            <label for="meta_keywords">Meta Keywords (SEO)</label>
            <input 
                type="text" 
                name="meta_keywords" 
                id="meta_keywords" 
                class="form-control"
                value="<?= esc($formData['meta_keywords'] ?? $post['meta_keywords'] ?? ''); ?>"
                placeholder="keyword1, keyword2, keyword3"
            >
            <small class="form-text text-muted">Separate keywords with commas</small>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft" 
                    <?= (isset($formData['status']) && $formData['status'] === 'draft') || 
                       (isset($post['status']) && $post['status'] === 'draft') ? 'selected' : ''; ?>>
                    Draft
                </option>
                <option value="published" 
                    <?= (isset($formData['status']) && $formData['status'] === 'published') || 
                       (isset($post['status']) && $post['status'] === 'published') ? 'selected' : ''; ?>>
                    Published
                </option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" data-loading-text="Saving...">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <?= isset($post) ? 'Update Post' : 'Create Post'; ?>
            </button>
            
            <button type="submit" name="status" value="draft" class="btn btn-secondary" data-loading-text="Saving...">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save as Draft
            </button>

            <?php if (isset($post['id']) && $post['status'] === 'published'): ?>
                <a href="/blog_cms/post.php?slug=<?= esc($post['slug']); ?>" target="_blank" class="btn btn-outline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    View Post
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<script>
// TinyMCE Editor Initialization
(function() {
    var contentTextarea = document.getElementById('content');
    
    // Initialize TinyMCE when script is loaded
    function initTinyMCE() {
        if (typeof tinymce !== 'undefined' && contentTextarea) {
            try {
                tinymce.init({
                    selector: '.tinymce-editor',
                    height: 400,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic underline strikethrough | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'removeformat | link image | code preview fullscreen | help',
                    menubar: 'file edit view insert format tools table help',
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }',
                    image_caption: true,
                    image_title: true,
                    automatic_uploads: true,
                    file_picker_types: 'image',
                    promotion: false,
                    resize: true,
                    branding: false,
                    statusbar: true,
                    elementpath: true,
                    wordcount: true
                });
                console.log('TinyMCE initialized successfully');
            } catch (error) {
                console.error('TinyMCE initialization error:', error);
            }
        } else {
            console.warn('TinyMCE not loaded or textarea not found');
        }
    }
    
    // Try to initialize immediately
    if (typeof tinymce !== 'undefined') {
        initTinyMCE();
    } else {
        // If not loaded yet, wait for it
        var checkCount = 0;
        var maxChecks = 50; // Check for 5 seconds
        var checkInterval = setInterval(function() {
            checkCount++;
            if (typeof tinymce !== 'undefined') {
                clearInterval(checkInterval);
                initTinyMCE();
            } else if (checkCount >= maxChecks) {
                clearInterval(checkInterval);
                console.error('TinyMCE failed to load after 5 seconds');
            }
        }, 100);
    }

    // Character counter for excerpt
    const excerptTextarea = document.getElementById('excerpt');
    if (excerptTextarea) {
        excerptTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const counter = this.parentElement.querySelector('.form-text');
            if (counter) {
                counter.textContent = length + '/500 characters';
            }
        });
    }

    // Character counter for meta description
    const metaDescTextarea = document.getElementById('meta_description');
    if (metaDescTextarea) {
        metaDescTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const counter = this.parentElement.querySelector('.form-text');
            if (counter) {
                counter.textContent = length + '/160 characters';
            }
        });
    }

    // Image upload preview
    const imageInput = document.getElementById('featured_image');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageButton = document.getElementById('removeImage');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB.');
                    imageInput.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" alt="Featured Image Preview">
                        <button type="button" class="btn-remove-image" id="removeImage">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    
                    // Re-bind remove button
                    const newRemoveButton = document.getElementById('removeImage');
                    if (newRemoveButton) {
                        newRemoveButton.addEventListener('click', removeImage);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function removeImage(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to remove the featured image?')) {
            const imageInput = document.getElementById('featured_image');
            const imagePreview = document.getElementById('imagePreview');
            
            if (imageInput) {
                imageInput.value = '';
            }
            
            if (imagePreview) {
                imagePreview.innerHTML = `
                    <div class="image-upload-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p>Click to upload or drag and drop</p>
                        <small>JPG, PNG, GIF, WebP (max 5MB)</small>
                    </div>
                `;
            }
        }
    }

    // Bind existing remove button
    if (removeImageButton) {
        removeImageButton.addEventListener('click', removeImage);
    }

    // Form validation
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = tinymce.get('content')?.getContent() || document.getElementById('content').value;
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a title.');
                return false;
            }
            
            if (!content.trim()) {
                e.preventDefault();
                alert('Please enter content.');
                return false;
            }
        });
    }
});
</script>
