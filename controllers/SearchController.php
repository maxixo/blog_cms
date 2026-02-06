<?php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Tag.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/helpers.php';

class SearchController
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
        $query = trim((string) get_query_value('q'));
        $categorySlug = get_query_value('category');

        $page = (int) get_query_value('page');
        $page = $page > 0 ? $page : 1;
        $perPage = POSTS_PER_PAGE;

        $maxResults = defined('SEARCH_MAX_RESULTS') ? (int) SEARCH_MAX_RESULTS : 0;
        if ($maxResults > 0 && $perPage > $maxResults) {
            $perPage = $maxResults;
        }

        $minLength = defined('MIN_SEARCH_LENGTH') ? (int) MIN_SEARCH_LENGTH : 2;

        $results = [];
        $total = 0;
        $totalPages = 0;
        $error = '';

        if ($query !== '' && $this->stringLength($query) < $minLength) {
            $error = 'Search term must be at least ' . $minLength . ' characters.';
        } elseif ($query !== '') {
            addSearchToHistory($query);
            $total = $this->postModel->getSearchTotal($query, $categorySlug ?: null);

            $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
            }

            if ($total > 0) {
                $results = $this->postModel->search($query, $page, $perPage, $categorySlug ?: null);
            }
        }

        $categories = $this->categoryModel->getAllWithCounts();
        $tags = $this->tagModel->getPopular(20);
        $recentPosts = $this->postModel->getRecent(5);
        $searchHistory = getSearchHistory();

        $tagCounts = array_column($tags, 'tag_count');
        $tagMin = $tagCounts ? (int) min($tagCounts) : 0;
        $tagMax = $tagCounts ? (int) max($tagCounts) : 0;

        $pageTitle = $query !== ''
            ? 'Search Results: "' . $query . '" - ' . SITE_NAME
            : 'Search - ' . SITE_NAME;
        $metaDescription = $query !== ''
            ? 'Search results for "' . $query . '" in ' . SITE_NAME
            : 'Search posts in ' . SITE_NAME;

        $canonicalParams = [];
        if ($query !== '') {
            $canonicalParams['q'] = $query;
        }
        if ($categorySlug !== '') {
            $canonicalParams['category'] = $categorySlug;
        }
        if ($page > 1) {
            $canonicalParams['page'] = $page;
        }
        $canonicalUrl = build_query_url(BASE_URL . '/search.php', $canonicalParams);
        $bodyClass = 'search-page';

        $baseParams = [];
        if ($query !== '') {
            $baseParams['q'] = $query;
        }
        if ($categorySlug !== '') {
            $baseParams['category'] = $categorySlug;
        }

        return compact(
            'query',
            'results',
            'total',
            'page',
            'perPage',
            'totalPages',
            'error',
            'categorySlug',
            'pageTitle',
            'metaDescription',
            'canonicalUrl',
            'bodyClass',
            'categories',
            'tags',
            'recentPosts',
            'tagMin',
            'tagMax',
            'baseParams',
            'searchHistory'
        );
    }

    private function stringLength($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }
}
