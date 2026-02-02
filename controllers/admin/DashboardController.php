<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Post.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../models/User.php';

class DashboardController
{
    public function index()
    {
        $pageHeading = 'Admin Dashboard';
        $pageDescription = 'Welcome to your blog administration panel.';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        // Fetch statistics
        $postModel = new Post();
        $categoryModel = new Category();
        
        // Get total posts
        $totalPosts = db_fetch("SELECT COUNT(*) as total FROM posts")['total'] ?? 0;
        $publishedPosts = db_fetch("SELECT COUNT(*) as total FROM posts WHERE status = 'published'")['total'] ?? 0;
        $draftPosts = db_fetch("SELECT COUNT(*) as total FROM posts WHERE status = 'draft'")['total'] ?? 0;
        
        // Get total users
        $totalUsers = db_fetch("SELECT COUNT(*) as total FROM users")['total'] ?? 0;
        
        // Get total categories
        $totalCategories = db_fetch("SELECT COUNT(*) as total FROM categories")['total'] ?? 0;
        
        // Get total comments
        $totalComments = db_fetch("SELECT COUNT(*) as total FROM comments")['total'] ?? 0;
        $pendingComments = db_fetch("SELECT COUNT(*) as total FROM comments WHERE status = 'pending'")['total'] ?? 0;
        
        // Get recent posts (last 5)
        $recentPosts = $postModel->getRecent(5);
        
        // Get recent comments (last 5)
        $recentComments = db_fetch_all("
            SELECT c.*, p.title as post_title, p.slug as post_slug
            FROM comments c
            LEFT JOIN posts p ON c.post_id = p.id
            ORDER BY c.created_at DESC
            LIMIT 5
        ");
        
        // Get most viewed posts
        $mostViewedPosts = db_fetch_all("
            SELECT id, title, slug, views, featured_image
            FROM posts
            WHERE status = 'published'
            ORDER BY views DESC
            LIMIT 5
        ");

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs',
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalUsers',
            'totalCategories',
            'totalComments',
            'pendingComments',
            'recentPosts',
            'recentComments',
            'mostViewedPosts'
        );
    }
}
