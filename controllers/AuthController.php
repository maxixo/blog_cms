<?php
require_once __DIR__ . '/../includes/functions.php';

class AuthController
{
    public function showLoginForm($error = '')
    {
        $pageHeading = 'Login';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;

        return [
            'pageHeading' => $pageHeading,
            'pageTitle' => $pageTitle,
            'error' => $error
        ];
    }

    public function showRegisterForm($error = '')
    {
        $pageHeading = 'Register';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;

        return [
            'pageHeading' => $pageHeading,
            'pageTitle' => $pageTitle,
            'error' => $error
        ];
    }

    public function login($postData)
    {
        return $this->showLoginForm('Authentication is not configured yet.');
    }

    public function register($postData)
    {
        if (!ENABLE_REGISTRATION) {
            return $this->showRegisterForm('Registration is currently disabled.');
        }

        return $this->showRegisterForm('Registration is not configured yet.');
    }
}
