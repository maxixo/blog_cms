<?php
require_once __DIR__ . '/../../includes/functions.php';

class UserManageController
{
    public function index()
    {
        $pageHeading = 'Users';
        $pageDescription = 'Manage user accounts and roles here.';
        $pageTitle = 'Manage Users - ' . SITE_NAME;
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
