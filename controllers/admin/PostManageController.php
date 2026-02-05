<?php
require_once __DIR__ . '/../../includes/functions.php';
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

    public function create()
    {
        $pageHeading = 'Create New Post';
        $pageDescription = 'Write and publish your blog post.';
        $pageTitle = 'Create Post - ' . SITE_NAME;
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
            'formData'
        );
    }

    public function store()
    {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Invalid request. Please try again.';
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

        // If validation errors, redirect back with data
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $_SESSION['error_message'] = 'Please fix the errors below.';
            header('Location: ' . BASE_URL . '/admin/post-create.php');
            exit;
        }

        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImage($_FILES['featured_image']);
            if ($upload['success']) {
                $featured_image = $upload['path'];
            } else {
                $_SESSION['error_message'] = $upload['error'];
                header('Location: ' . BASE_URL . '/admin/post-create.php');
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
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to create post. Please try again.';
            header('Location: ' . BASE_URL . '/admin/post-create.php');
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
        if ($post['author_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
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
        if ($post['author_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
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
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if ($featured_image) {
                $oldImagePath = __DIR__ . '/../../public/' . $featured_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $upload = $this->uploadImage($_FILES['featured_image']);
            if ($upload['success']) {
                $featured_image = $upload['path'];
            } else {
                $_SESSION['error_message'] = $upload['error'];
                header('Location: ' . BASE_URL . '/admin/post-edit.php?id=' . $id);
                exit;
            }
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
        if ($post['author_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_message'] = 'You do not have permission to delete this post.';
            header('Location: ' . BASE_URL . '/admin/posts.php');
            exit;
        }

        // Delete featured image if exists
        if (!empty($post['featured_image'])) {
            $imagePath = __DIR__ . '/../../public/' . $post['featured_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
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
        // Validate file
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size must be less than 5MB.'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_', true) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/posts/';
        $uploadPath = $uploadDir . $filename;

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'path' => 'uploads/posts/' . $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to upload file. Please try again.'];
        }
    }
}
