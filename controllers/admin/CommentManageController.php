<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Comment.php';

class CommentManageController
{
    private $commentModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    public function index()
    {
        $page = (int) ($_GET['page'] ?? 1);
        $status = $_GET['status'] ?? 'all';
        
        $comments = $this->commentModel->getAll(
            $status === 'all' ? null : $status,
            $page,
            20
        );
        
        $totalComments = $this->commentModel->getCount(
            $status === 'all' ? null : $status
        );
        
        $totalPages = ceil($totalComments / 20);

        $currentPage = $page;
        $currentStatus = $status;
        $pageHeading = 'Comments';
        $pageDescription = 'Review and moderate comments here.';
        $pageTitle = 'Moderate Comments - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        return compact(
            'comments',
            'totalComments',
            'totalPages',
            'currentPage',
            'currentStatus',
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs'
        );
    }

    public function delete($id)
    {
        $comment = $this->commentModel->getById($id);
        
        if (!$comment) {
            $_SESSION['error'] = 'Comment not found.';
            header('Location: ' . BASE_URL . '/admin/comments.php');
            exit;
        }

        $result = $this->commentModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Comment deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete comment.';
        }

        header('Location: ' . BASE_URL . '/admin/comments.php');
        exit;
    }
}
