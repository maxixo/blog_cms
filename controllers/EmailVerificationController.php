<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/BrevoEmailService.php';

class EmailVerificationController
{
    /**
     * Show email verification pending page
     * 
     * @param string $email User's email address
     * @param string $error Error message
     * @param string $success Success message
     * @return array Template data
     */
    public function showPending($email = '', $error = '', $success = ''): array
    {
        $pageHeading = 'Email Verification Required';
        $pageTitle = 'Verify Your Email - ' . SITE_NAME;
        
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
            'email' => $email,
            'error' => $error,
            'success' => $success,
            'csrf_token' => generateCsrfToken()
        ];
    }
    
    /**
     * Process email verification from link
     * 
     * @param string $token Verification token
     * @return void
     */
    public function verify($token): void
    {
        // Validate token
        if (empty($token)) {
            setFlashMessage('error', 'Invalid verification link.');
            redirect(BASE_URL . '/login.php');
        }
        
        // Attempt to verify email
        $verified = User::verifyEmail($token);
        
        if ($verified) {
            setFlashMessage('success', 'Your email has been verified successfully! You can now login.');
            redirect(BASE_URL . '/login.php');
        } else {
            setFlashMessage('error', 'Invalid or expired verification link. Please request a new verification email.');
            redirect(BASE_URL . '/resend-verification.php');
        }
    }
    
    /**
     * Resend verification email
     * 
     * @param array $postData POST data
     * @return array Template data
     */
    public function resend($postData): array
    {
        // Verify CSRF token
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            return $this->showPending($postData['email'] ?? '', '', '');
        }
        
        $email = trim($postData['email'] ?? '');
        
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Please provide a valid email address.');
            return $this->showPending($email);
        }
        
        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user) {
            setFlashMessage('error', 'If an account exists with this email, a verification link has been sent.');
            return $this->showPending($email);
        }
        
        // Check if already verified
        if (User::isEmailVerified($user['id'])) {
            setFlashMessage('success', 'Your email is already verified. You can login.');
            redirect(BASE_URL . '/login.php');
            return [];
        }
        
        // Delete any existing verification tokens
        User::deleteExpiredPasswordResets();
        
        // Generate new token
        $token = User::generateSecureToken();
        User::createEmailVerification($user['id'], $email, $token, EMAIL_VERIFICATION_TOKEN_EXPIRY);
        
        // Send verification email
        $emailService = new BrevoEmailService();
        $result = $emailService->sendVerificationEmail($email, $user['username'], $token);
        
        if ($result['success']) {
            setFlashMessage('success', 'A new verification email has been sent to ' . esc($email) . '. Please check your inbox.');
        } else {
            setFlashMessage('error', 'Failed to send verification email. ' . $result['message']);
        }
        
        return $this->showPending($email);
    }
}
