<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rate-limiter.php';
require_once __DIR__ . '/../includes/security-logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/ResendEmailService.php';

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
        $clientIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

        // Validate token
        if (empty($token)) {
            logSecurityEvent('email_verification_failed', [
                'reason' => 'missing_token',
                'ip' => $clientIp
            ]);
            setFlashMessage('error', 'Invalid verification link.');
            redirect(BASE_URL . '/login.php');
        }

        $verification = User::findEmailVerificationByToken($token);
        
        // Attempt to verify email
        $verified = User::verifyEmail($token);
        
        if ($verified) {
            logSecurityEvent('email_verification_success', [
                'user_id' => (int) ($verification['user_id'] ?? 0),
                'email' => strtolower((string) ($verification['email'] ?? '')),
                'ip' => $clientIp
            ]);
            setFlashMessage('success', 'Your email has been verified successfully! You can now login.');
            redirect(BASE_URL . '/login.php');
        } else {
            logSecurityEvent('email_verification_failed', [
                'reason' => 'invalid_or_expired_token',
                'user_id' => (int) ($verification['user_id'] ?? 0),
                'email' => strtolower((string) ($verification['email'] ?? '')),
                'ip' => $clientIp
            ]);
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
        $clientIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

        // Verify CSRF token
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            return $this->showPending($postData['email'] ?? '', '', '');
        }
        
        $email = trim($postData['email'] ?? '');
        
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            logSecurityEvent('email_verification_resend_invalid', [
                'email' => strtolower($email),
                'ip' => $clientIp
            ]);
            setFlashMessage('error', 'Please provide a valid email address.');
            return $this->showPending($email);
        }

        $verificationRateLimitKey = 'email-verification:' . strtolower($email) . ':' . $clientIp;
        if (!rateLimit($verificationRateLimitKey, 3, 60 * 60)) {
            logSecurityEvent('email_verification_resend_rate_limited', [
                'email' => strtolower($email),
                'ip' => $clientIp
            ]);
            setFlashMessage('error', 'Too many verification email requests. Please try again in 1 hour.');
            return $this->showPending($email);
        }
        
        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user) {
            logSecurityEvent('email_verification_resend_requested', [
                'email' => strtolower($email),
                'ip' => $clientIp,
                'user_found' => false
            ]);
            setFlashMessage('error', 'If an account exists with this email, a verification link has been sent.');
            return $this->showPending($email);
        }
        
        // Check if already verified
        if (User::isEmailVerified($user['id'])) {
            logSecurityEvent('email_verification_already_verified', [
                'user_id' => (int) $user['id'],
                'email' => strtolower($email),
                'ip' => $clientIp
            ]);
            setFlashMessage('success', 'Your email is already verified. You can login.');
            redirect(BASE_URL . '/login.php');
            return [];
        }
        
        // Delete any existing verification tokens
        User::deleteEmailVerificationsForUser((int) $user['id']);
        
        // Generate new token
        $token = User::generateSecureToken();
        User::createEmailVerification($user['id'], $email, $token, EMAIL_VERIFICATION_TOKEN_EXPIRY);
        
        // Send verification email
        $emailService = new ResendEmailService();
        $result = $emailService->sendVerificationEmail($email, $user['username'], $token);
        
        if ($result['success']) {
            logSecurityEvent('email_verification_resend_sent', [
                'user_id' => (int) $user['id'],
                'email' => strtolower($email),
                'ip' => $clientIp
            ]);
            setFlashMessage('success', 'A new verification email has been sent to ' . esc($email) . '. Please check your inbox.');
        } else {
            logSecurityEvent('email_verification_resend_failed', [
                'user_id' => (int) $user['id'],
                'email' => strtolower($email),
                'ip' => $clientIp,
                'error' => (string) ($result['message'] ?? 'unknown_error')
            ]);
            setFlashMessage('error', 'Failed to send verification email. ' . $result['message']);
        }
        
        return $this->showPending($email);
    }
}
