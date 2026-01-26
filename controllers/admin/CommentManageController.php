<?php
require_once __DIR__ . '/../../includes/functions.php';

class CommentManageController
{
    public function index()
    {
        $pageHeading = 'Comments';
        $pageDescription = 'Review and moderate comments here.';
        $pageTitle = 'Moderate Comments - ' . SITE_NAME;
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
