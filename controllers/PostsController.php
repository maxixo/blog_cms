<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/helpers.php';

class PostsController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }

    public function index()
    {
        $categorySlug = get_query_value('category');
        $tagSlug = get_query_value('tag');
        $authorId = (int) get_query_value('author');

        $page = (int) get_query_value('page');
        $page = $page > 0 ? $page : 1;
        $perPage = POSTS_PER_PAGE;

        $totalPosts = $this->postModel->getTotal(
            $categorySlug ?: null,
            $tagSlug ?: null,
            $authorId ?: null
        );
        $totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 0;
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $posts = $this->postModel->getPaginated(
            $page,
            $perPage,
            $categorySlug ?: null,
            $tagSlug ?: null,
            $authorId ?: null
        );

        $offset = max(0, ($page - 1) * $perPage);
        $showingFrom = $totalPosts > 0 ? $offset + 1 : 0;
        $showingTo = $totalPosts > 0 ? min($offset + $perPage, $totalPosts) : 0;

        $authorName = '';
        if ($authorId > 0) {
            $authorName = $posts[0]['author_name'] ?? 'Unknown';
        }

        $pageHeading = 'All Posts';
        if ($categorySlug !== '') {
            $pageHeading = 'Category: ' . $categorySlug;
        } elseif ($tagSlug !== '') {
            $pageHeading = 'Tag: ' . $tagSlug;
        } elseif ($authorId > 0) {
            $pageHeading = 'Posts by ' . ($authorName !== '' ? $authorName : 'Unknown');
        }

        $metaDescription = 'Browse all posts on ' . SITE_NAME;
        if ($categorySlug !== '') {
            $metaDescription .= ' in category ' . $categorySlug;
        }
        if ($tagSlug !== '') {
            $metaDescription .= ' tagged with ' . $tagSlug;
        }

        $canonicalParams = [];
        if ($categorySlug !== '') {
            $canonicalParams['category'] = $categorySlug;
        }
        if ($tagSlug !== '') {
            $canonicalParams['tag'] = $tagSlug;
        }
        if ($authorId > 0) {
            $canonicalParams['author'] = $authorId;
        }
        if ($page > 1) {
            $canonicalParams['page'] = $page;
        }

        $pageTitle = $pageHeading . ' - ' . SITE_NAME;
        $canonicalUrl = build_query_url(BASE_URL . '/posts.php', $canonicalParams);

        $filtersActive = ($categorySlug !== '' || $tagSlug !== '' || $authorId > 0);
        $baseParams = [];
        if ($categorySlug !== '') {
            $baseParams['category'] = $categorySlug;
        }
        if ($tagSlug !== '') {
            $baseParams['tag'] = $tagSlug;
        }
        if ($authorId > 0) {
            $baseParams['author'] = $authorId;
        }

        $listBaseUrl = BASE_URL . '/posts.php';

        return compact(
            'posts',
            'page',
            'perPage',
            'totalPosts',
            'totalPages',
            'showingFrom',
            'showingTo',
            'pageHeading',
            'pageTitle',
            'metaDescription',
            'canonicalUrl',
            'categorySlug',
            'tagSlug',
            'authorId',
            'authorName',
            'filtersActive',
            'baseParams',
            'listBaseUrl'
        );
    }
}
