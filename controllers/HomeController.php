<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Tag.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/helpers.php';

class HomeController
{
    private $postModel;
    private $categoryModel;
    private $tagModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
    }

    public function index()
    {
        $categorySlug = get_query_value('category');
        $tagSlug = get_query_value('tag');

        $page = (int) get_query_value('page');
        $page = $page > 0 ? $page : 1;
        $perPage = POSTS_PER_PAGE;

        $totalPosts = $this->postModel->getTotal($categorySlug ?: null, $tagSlug ?: null);
        $totalPages = $totalPosts > 0 ? (int) ceil($totalPosts / $perPage) : 0;
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $posts = $this->postModel->getPaginated($page, $perPage, $categorySlug ?: null, $tagSlug ?: null);
        $categories = $this->categoryModel->getAllWithCounts();
        $tags = $this->tagModel->getPopular(20);
        $recentPosts = $this->postModel->getRecent(5);

        $tagCounts = array_column($tags, 'tag_count');
        $tagMin = $tagCounts ? (int) min($tagCounts) : 0;
        $tagMax = $tagCounts ? (int) max($tagCounts) : 0;

        $pageHeading = 'Latest Posts';
        if ($categorySlug !== '' && $tagSlug !== '') {
            $pageHeading = 'Posts in ' . $categorySlug . ' tagged ' . $tagSlug;
        } elseif ($categorySlug !== '') {
            $pageHeading = 'Category: ' . $categorySlug;
        } elseif ($tagSlug !== '') {
            $pageHeading = 'Tag: ' . $tagSlug;
        }

        $canonicalParams = [];
        if ($categorySlug !== '') {
            $canonicalParams['category'] = $categorySlug;
        }
        if ($tagSlug !== '') {
            $canonicalParams['tag'] = $tagSlug;
        }
        if ($page > 1) {
            $canonicalParams['page'] = $page;
        }

        $pageTitle = $pageHeading . ' - ' . SITE_NAME;
        $metaDescription = SITE_DESCRIPTION;
        $canonicalUrl = build_query_url(BASE_URL . '/index.php', $canonicalParams);

        return compact(
            'posts',
            'categories',
            'tags',
            'recentPosts',
            'tagMin',
            'tagMax',
            'page',
            'totalPages',
            'totalPosts',
            'pageHeading',
            'pageTitle',
            'metaDescription',
            'canonicalUrl',
            'categorySlug',
            'tagSlug'
        );
    }
}
