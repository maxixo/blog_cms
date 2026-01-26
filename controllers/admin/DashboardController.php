<?php
require_once __DIR__ . '/../../includes/functions.php';

class DashboardController
{
    public function index()
    {
        $pageHeading = 'Admin Dashboard';
        $pageDescription = 'Admin stats and shortcuts will appear here.';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;
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
