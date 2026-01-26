<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/functions.php';

class PostController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }

    public function show($slug)
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            redirect('index.php');
        }

        $post = $this->postModel->getBySlug($slug);
        if (!$post) {
            redirect('index.php');
        }

        $this->postModel->incrementViews($post['id']);

        $pageHeading = $post['title'] ?? 'Post';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;
        $metaDescription = $post['meta_description'] ?? ($post['excerpt'] ?? SITE_DESCRIPTION);
        $metaKeywords = $post['meta_keywords'] ?? '';
        $canonicalUrl = !empty($post['canonical_url'])
            ? $post['canonical_url']
            : build_query_url(BASE_URL . '/post.php', ['slug' => $post['slug'] ?? '']);

        $comments = [];
        $reactionCounts = [];

        return compact(
            'post',
            'comments',
            'reactionCounts',
            'pageHeading',
            'pageTitle',
            'metaDescription',
            'metaKeywords',
            'canonicalUrl'
        );
    }
}
