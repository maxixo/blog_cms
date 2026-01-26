<?php
require_once __DIR__ . '/../../includes/functions.php';

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
