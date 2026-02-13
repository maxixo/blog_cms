<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../services/ResendEmailService.php';

class ProfileController
{
    public function index()
    {
        $pageHeading = 'Profile';
        $pageDescription = 'Update your profile details here.';
        $pageTitle = 'Edit Profile - ' . SITE_NAME;
        $bodyClass = 'admin-page';
        $additionalCss = [ASSETS_URL . '/css/admin.css'];
        $additionalJs = [ASSETS_URL . '/js/admin.js'];

        $flash = getFlashMessage();
        $error = '';
        $success = '';
        if ($flash) {
            if ($flash['type'] === 'success') {
                $success = $flash['message'];
            } else {
                $error = $flash['message'];
            }
        }

        return compact(
            'pageHeading',
            'pageDescription',
            'pageTitle',
            'bodyClass',
            'additionalCss',
            'additionalJs',
            'error',
            'success'
        );
    }

    public function handlePasswordChange($postData): array
    {
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            return ['error' => 'Unable to process request. Please try again.'];
        }

        session_start_safe();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['error' => 'You must be logged in to change your password.'];
        }

        $currentPassword = (string) ($postData['current_password'] ?? '');
        $newPassword = (string) ($postData['new_password'] ?? '');
        $confirmPassword = (string) ($postData['confirm_password'] ?? '');

        if ($currentPassword === '' || !User::verifyCurrentPassword($userId, $currentPassword)) {
            return ['error' => 'Current password is incorrect.'];
        }

        if ($newPassword === '' || strlen($newPassword) < 6) {
            return ['error' => 'New password must be at least 6 characters long.'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['error' => 'New passwords do not match.'];
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        if (!User::updatePassword($userId, $passwordHash)) {
            return ['error' => 'Unable to update your password. Please try again.'];
        }

        $currentUser = User::findById($userId);
        if ($currentUser) {
            $mailer = new ResendEmailService();
            $mailer->sendPasswordChangeConfirmation($currentUser['email'], $currentUser['username'] ?? $currentUser['email']);
        }

        session_regenerate_id(true);
        regenerateCsrfToken();

        return ['success' => 'Your password has been updated successfully.'];
    }
}
