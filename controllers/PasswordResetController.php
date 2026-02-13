<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/ResendEmailService.php';

class PasswordResetController
{
    /**
     * Show forgot password form
     * 
     * @param string $error Error message
     * @param string $success Success message
     * @return array Template data
     */
    public function showForgotForm($error = '', $success = ''): array
    {
        $pageHeading = 'Forgot Password';
        $pageTitle = 'Reset Your Password - ' . SITE_NAME;
        
        // Get flash messages
        $flash = getFlashMessage();
        if ($flash) {
            if ($flash['type'] === 'success') {
                $success = $flash['message'];
            } else {
                $error = $flash['message'];
            }
        }
        
        return [
            'pageHeading' => $pageHeading,
            'pageTitle' => $pageTitle,
            'error' => $error,
            'success' => $success,
            'csrf_token' => generateCsrfToken()
        ];
    }
    
    /**
     * Handle forgot password request
     * 
     * @param array $postData POST data
     * @return array Template data
     */
    public function handleForgotRequest($postData): array
    {
        // Verify CSRF token
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            return $this->showForgotForm();
        }
        
        $email = trim($postData['email'] ?? '');
        
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Please provide a valid email address.');
            return $this->showForgotForm();
        }
        
        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user) {
            // Don't reveal if email exists or not
            setFlashMessage('success', 'If an account exists with this email, a password reset link has been sent.');
            return $this->showForgotForm();
        }
        
        // Delete any existing reset tokens for this user
        User::deleteExpiredPasswordResets();
        
        // Generate new token
        $token = User::generateSecureToken();
        User::createPasswordReset($user['id'], $email, $token, PASSWORD_RESET_TOKEN_EXPIRY);
        
        // Send password reset email
        $emailService = new ResendEmailService();
        $result = $emailService->sendPasswordResetEmail($email, $user['username'], $token);
        
        if ($result['success']) {
            setFlashMessage('success', 'A password reset link has been sent to ' . esc($email) . '. Please check your inbox.');
        } else {
            setFlashMessage('error', 'Failed to send password reset email. ' . $result['message']);
        }
        
        return $this->showForgotForm();
    }
    
    /**
     * Show reset password form
     * 
     * @param string $token Reset token
     * @param string $error Error message
     * @param string $success Success message
     * @return array Template data
     */
    public function showResetForm($token, $error = '', $success = ''): array
    {
        $pageHeading = 'Reset Password';
        $pageTitle = 'Reset Your Password - ' . SITE_NAME;
        
        // Get flash messages
        $flash = getFlashMessage();
        if ($flash) {
            if ($flash['type'] === 'success') {
                $success = $flash['message'];
            } else {
                $error = $flash['message'];
            }
        }
        
        return [
            'pageHeading' => $pageHeading,
            'pageTitle' => $pageTitle,
            'token' => $token,
            'error' => $error,
            'success' => $success,
            'csrf_token' => generateCsrfToken()
        ];
    }
    
    /**
     * Handle password reset
     * 
     * @param string $token Reset token
     * @param array $postData POST data
     * @return array Template data
     */
    public function handleReset($token, $postData): array
    {
        // Validate token
        if (empty($token) || !User::isValidPasswordResetToken($token)) {
            setFlashMessage('error', 'Invalid or expired reset link. Please request a new password reset.');
            redirect(BASE_URL . '/forgot-password.php');
            return [];
        }
        
        // Verify CSRF token
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            return $this->showResetForm($token);
        }
        
        // Get form data
        $password = $postData['password'] ?? '';
        $passwordConfirm = $postData['password_confirm'] ?? '';
        
        // Validate password
        $errors = [];
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            return $this->showResetForm($token);
        }
        
        // Get password reset record
        $reset = User::findPasswordResetByToken($token);
        if (!$reset) {
            setFlashMessage('error', 'Invalid or expired reset link.');
            redirect(BASE_URL . '/forgot-password.php');
            return [];
        }
        
        // Update user password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $updated = User::updatePassword($reset['user_id'], $passwordHash);
        
        if (!$updated) {
            setFlashMessage('error', 'Failed to update password. Please try again.');
            return $this->showResetForm($token);
        }
        
        // Delete used reset token
        User::deletePasswordResetToken($token);
        
        // Send confirmation email
        $user = User::findById($reset['user_id']);
        if ($user) {
            $emailService = new ResendEmailService();
            $emailService->sendPasswordChangeConfirmation($user['email'], $user['username']);
        }
        
        setFlashMessage('success', 'Your password has been reset successfully! You can now login with your new password.');
        redirect(BASE_URL . '/login.php');
        return [];
    }
}
