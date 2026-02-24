<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/uploads.php';
require_once __DIR__ . '/../../models/Post.php';
require_once __DIR__ . '/../../models/Category.php';

class PostManageController
{
    public function index()
    {
        $pageHeading = 'Posts';
        $pageDescription = 'Create, edit, and manage blog posts here.';
        $pageTitle = 'Manage Posts - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        // Get all posts
        $postModel = new Post();
        $posts = $this->getAllPosts();

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs',
            'posts'
        );
    }

    public function create(array $options = [])
    {
        $isAdminView = isset($options['isAdminView']) ? (bool) $options['isAdminView'] : true;
        $backUrl = $options['backUrl'] ?? (BASE_URL . '/admin/posts.php');
        $uploadUrl = $options['uploadUrl'] ?? (BASE_URL . '/admin/image-upload.php');

        $pageHeading = 'Create New Post';
        $pageDescription = 'Write and publish your blog post.';
        $pageTitle = 'Create Post - ' . SITE_NAME;
        $bodyClass = $isAdminView ? 'admin-page' : '';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [
            ASSETS_URL . '/js/admin.js',
            TINYMCE_SCRIPT_URL
        ];

        // Get categories
        $categoryModel = new Category();
        $categories = $this->getAllCategories();

        // Get form data if errors exist
        $errors = $_SESSION['form_errors'] ?? [];
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_data']);

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs',
            'categories',
            'errors',
            'formData',
            'backUrl',
            'uploadUrl'
        );
    }

    public function store(array $options = [])
    {
        $listUrl = $options['listUrl'] ?? (BASE_URL . '/admin/posts.php');
        $createUrl = $options['createUrl'] ?? (BASE_URL . '/admin/post-create.php');

        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Invalid request. Please try again.';
            header('Location: ' . $listUrl);
            exit;
        }

        // Validate required fields
        $required = ['title', 'content'];
        $errors = [];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        // If validation errors, redirect back with data
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = 'Please fix the errors below.';
            header('Location: ' . $createUrl);
            exit;
        }

        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->uploadImage($_FILES['featured_image']);
            if ($upload['success']) {
                $featured_image = $upload['path'];
            } else {
                $_SESSION['error_message'] = $upload['error'];
                header('Location: ' . $createUrl);
                exit;
            }
        }

        // Generate excerpt from content if not provided
        $excerpt = !empty($_POST['excerpt']) ? $_POST['excerpt'] : 
                   substr(strip_tags($_POST['content']), 0, 160) . '...';

        // Prepare post data
        $postData = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'excerpt' => $excerpt,
            'featured_image' => $featured_image,
            'author_id' => $_SESSION['user_id'],
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'status' => $_POST['status'] ?? 'draft',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? ''
        ];

        // Create post
        $postModel = new Post();
        $postId = $postModel->create($postData);

        if ($postId) {
            $_SESSION['success_message'] = 'Post ' . 
                (($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'saved as draft') . ' successfully!';
            header('Location: ' . $listUrl);
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to create post. Please try again.';
            header('Location: ' . $createUrl);
            exit;
        }
    }

    public function edit($id)
    {
        $postModel = new Post();
        $post = $this->getPostById($id);

        if (!$post) {
            $_SESSION['error_message'] = 'Post not found.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Check if user owns the post or is admin
        if ($post['author_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $_SESSION['error_message'] = 'You do not have permission to edit this post.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        $pageHeading = 'Edit Post';
        $pageDescription = 'Edit your blog post.';
        $pageTitle = 'Edit Post - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [
            ASSETS_URL . '/js/admin.js',
            TINYMCE_SCRIPT_URL
        ];

        // Get categories
        $categoryModel = new Category();
        $categories = $this->getAllCategories();

        // Get form data if errors exist
        $errors = $_SESSION['form_errors'] ?? [];
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_data']);

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs',
            'categories',
            'errors',
            'formData',
            'post'
        );
    }

    public function update($id)
    {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Invalid request. Please try again.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        $postModel = new Post();
        $post = $this->getPostById($id);

        if (!$post) {
            $_SESSION['error_message'] = 'Post not found.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Check permissions
        if ($post['author_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $_SESSION['error_message'] = 'You do not have permission to edit this post.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Validate required fields
        $required = ['title', 'content'];
        $errors = [];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = 'Please fix the errors below.';
            header('Location: ' . BASE_URL . '/admin/post-edit.php?id=' . $id);
            exit;
        }

        // Handle image upload
        $featured_image = $post['featured_image'];
        $removeFeaturedImage = isset($_POST['remove_featured_image']) && $_POST['remove_featured_image'] === '1';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($featured_image) {
                $this->deleteImageFile($featured_image);
            }

            $upload = $this->uploadImage($_FILES['featured_image']);
            if ($upload['success']) {
                $featured_image = $upload['path'];
            } else {
                $_SESSION['error_message'] = $upload['error'];
                header('Location: ' . BASE_URL . '/admin/post-edit.php?id=' . $id);
                exit;
            }
        } elseif ($removeFeaturedImage && $featured_image) {
            $this->deleteImageFile($featured_image);
            $featured_image = null;
        }

        // Generate excerpt from content if not provided
        $excerpt = !empty($_POST['excerpt']) ? $_POST['excerpt'] : 
                   substr(strip_tags($_POST['content']), 0, 160) . '...';

        // Prepare update data
        $updateData = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'excerpt' => $excerpt,
            'featured_image' => $featured_image,
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'status' => $_POST['status'] ?? 'draft',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? ''
        ];

        // Update post
        $result = $postModel->update($id, $updateData);

        if ($result) {
            $_SESSION['success_message'] = 'Post updated successfully!';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to update post. Please try again.';
            header('Location: ' . BASE_URL . '/admin/post-edit.php?id=' . $id);
            exit;
        }
    }

    public function delete($id)
    {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Invalid request. Please try again.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        $postModel = new Post();
        $post = $this->getPostById($id);

        if (!$post) {
            $_SESSION['error_message'] = 'Post not found.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Check permissions
        if ($post['author_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $_SESSION['error_message'] = 'You do not have permission to delete this post.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Delete featured image if exists
        if (!empty($post['featured_image'])) {
            $this->deleteImageFile($post['featured_image']);
        }

        // Delete post
        $result = $postModel->delete($id);

        if ($result) {
            $_SESSION['success_message'] = 'Post deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete post. Please try again.';
        }

        header('Location: ' . BASE_URL . '/admin/posts.php');
        exit;
    }

    private function getAllPosts()
    {
        $sql = "SELECT p.*, u.username as author_name, c.name as category_name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC";
        
        return db_fetch_all($sql);
    }

    private function getPostById($id)
    {
        $sql = "SELECT * FROM posts WHERE id = ? LIMIT 1";
        return db_fetch($sql, 'i', [$id]);
    }

    private function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return db_fetch_all($sql);
    }

    private function uploadImage($file)
    {
        $upload = upload_image_file($file, [
            'subdir' => 'posts',
            'prefix' => 'featured_',
            'max_size' => 5 * 1024 * 1024
        ]);

        if (!$upload['success']) {
            return $upload;
        }

        return ['success' => true, 'path' => $upload['path']];
    }

    private function deleteImageFile($path)
    {
        $path = ltrim((string) $path, '/');
        if ($path === '') {
            return;
        }

        if (strpos($path, 'public/') === 0) {
            $path = substr($path, strlen('public/'));
        }

        $fullPath = __DIR__ . '/../../public/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
