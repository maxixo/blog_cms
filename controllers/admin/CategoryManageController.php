<?php
require_once __DIR__ . '/../../includes/functions.php';

class CategoryManageController
{
    public function index()
    {
        $pageHeading = 'Categories';
        $pageDescription = 'Create and organize categories here.';
        $pageTitle = 'Manage Categories - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs'
        );
    }
}
