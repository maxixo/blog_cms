<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Category.php';

class CategoryManageController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $categories = $this->categoryModel->getAllWithCounts();
        
        $pageHeading = 'Categories';
        $pageDescription = 'Create and organize categories here.';
        $pageTitle = 'Manage Categories - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        return compact(
            'categories',
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs'
        );
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        // CSRF validation
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'Category name is required.';
            header('Location: ' . BASE_URL . '/admin/category-create.php');
            exit;
        }

        if (strlen($name) > 100) {
            $_SESSION['error'] = 'Category name must be less than 100 characters.';
            header('Location: ' . BASE_URL . '/admin/category-create.php');
            exit;
        }

        // Create category
        $data = [
            'name' => $name,
            'description' => $description
        ];

        $categoryId = $this->categoryModel->create($data);

        if ($categoryId) {
            $_SESSION['success'] = 'Category created successfully.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
        } else {
            $_SESSION['error'] = 'Failed to create category.';
            header('Location: ' . BASE_URL . '/admin/category-create.php');
        }
        exit;
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error'] = 'Category not found.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        $pageHeading = 'Edit Category';
        $pageDescription = 'Update category details.';
        $pageTitle = 'Edit Category - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        return compact(
            'category',
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs'
        );
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        // CSRF validation
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'Category name is required.';
            header('Location: ' . BASE_URL . '/admin/category-edit.php?id=' . $id);
            exit;
        }

        if (strlen($name) > 100) {
            $_SESSION['error'] = 'Category name must be less than 100 characters.';
            header('Location: ' . BASE_URL . '/admin/category-edit.php?id=' . $id);
            exit;
        }

        // Update category
        $data = [
            'name' => $name,
            'description' => $description
        ];

        $result = $this->categoryModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Category updated successfully.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
        } else {
            $_SESSION['error'] = 'Failed to update category.';
            header('Location: ' . BASE_URL . '/admin/category-edit.php?id=' . $id);
        }
        exit;
    }

    public function delete($id)
    {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Category not found.';
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        // Check if category has posts
        $postCount = $this->categoryModel->getPostCount($id);
        
        if ($postCount > 0) {
            $_SESSION['error'] = "Cannot delete category '{$category['name']}' because it has {$postCount} post(s). Please reassign or delete the posts first.";
            header('Location: ' . BASE_URL . '/admin/categories.php');
            exit;
        }

        $result = $this->categoryModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Category deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete category.';
        }

        header('Location: ' . BASE_URL . '/admin/categories.php');
        exit;
    }
}
