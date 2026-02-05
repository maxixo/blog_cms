<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Comment.php';
require_once __DIR__ . '/models/Post.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to comment.';
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request. Please try again.';
    redirect('index.php');
}

// Get and validate input
$post_id = (int) ($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($post_id <= 0) {
    $_SESSION['error'] = 'Invalid post.';
    redirect('index.php');
}

if (empty($content)) {
    $_SESSION['error'] = 'Comment cannot be empty.';
    redirect('index.php');
}

if (strlen($content) > 5000) {
    $_SESSION['error'] = 'Comment is too long. Maximum 5000 characters.';
    redirect('index.php');
}

// Verify post exists
$postModel = new Post();
$post = $postModel->getById($post_id);

if (!$post) {
    $_SESSION['error'] = 'Post not found.';
    redirect('index.php');
}

// Create comment
$commentModel = new Comment();
$user_id = $_SESSION['user_id'];

$data = [
    'post_id' => $post_id,
    'user_id' => $user_id,
    'content' => $content
];

$comment_id = $commentModel->create($data);

if ($comment_id) {
    $_SESSION['success'] = 'Comment posted successfully!';
} else {
    $_SESSION['error'] = 'Failed to post comment. Please try again.';
}

// Redirect back to post
header('Location: ' . build_query_url(BASE_URL . '/post.php', ['slug' => $post['slug']]));
exit;