<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rate-limiter.php';
require_once __DIR__ . '/../includes/security-logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/ResendEmailService.php';
require_once __DIR__ . '/../config/EmailConfig.php';

class AuthController
{
    public function showLoginForm($error = '', $success = '')
    {
        $pageHeading = 'Login';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;

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

    public function showRegisterForm($error = '', $success = '')
    {
        $pageHeading = 'Register';
        $pageTitle = $pageHeading . ' - ' . SITE_NAME;

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

    private function formatLockoutDuration($seconds)
    {
        $seconds = max(0, (int) $seconds);

        if ($seconds < 60) {
            return $seconds . ' second' . ($seconds === 1 ? '' : 's');
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds === 0) {
            return $minutes . ' minute' . ($minutes === 1 ? '' : 's');
        }

        return $minutes . ' minute' . ($minutes === 1 ? '' : 's') . ' ' .
            $remainingSeconds . ' second' . ($remainingSeconds === 1 ? '' : 's');
    }

    private function logVerificationRedirectDecision(array $context): void
    {
        logSecurityEvent('login_verification_redirect', $context);
    }

    public function login($postData)
    {
        // Verify CSRF token silently
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            // Silently handle CSRF failure - regenerate token and show form without error
            return $this->showLoginForm();
        }

        // Get and sanitize input
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';

        // Validation
        $errors = [];

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            return $this->showLoginForm();
        }

        $clientIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $normalizedEmail = strtolower($email);
        $user = User::findByEmail($email);

        if ($user && User::isAccountLocked((int) $user['id'])) {
            $remainingSeconds = User::getRemainingLockoutSeconds((int) $user['id']);
            logLoginAttempt($email, false, $clientIp);
            logSecurityEvent('account_lockout_active', [
                'user_id' => (int) $user['id'],
                'email' => $normalizedEmail,
                'ip' => $clientIp,
                'remaining_seconds' => $remainingSeconds
            ]);

            setFlashMessage(
                'error',
                'Your account is temporarily locked. Try again in ' . $this->formatLockoutDuration($remainingSeconds) . '.'
            );
            return $this->showLoginForm();
        }

        $loginRateLimitKey = 'login:' . $normalizedEmail . ':' . $clientIp;
        if (!rateLimit($loginRateLimitKey, 5, 15 * 60)) {
            logLoginAttempt($email, false, $clientIp);
            logSecurityEvent('login_rate_limited', [
                'email' => $normalizedEmail,
                'ip' => $clientIp
            ]);

            setFlashMessage('error', 'Too many login attempts. Please try again in 15 minutes.');
            return $this->showLoginForm();
        }

        // Check if user exists
        if (!$user) {
            logLoginAttempt($email, false, $clientIp);
            setFlashMessage('error', 'Invalid email or password.');
            return $this->showLoginForm();
        }

        // Verify password
        if (!User::verifyPassword($password, $user['password_hash'])) {
            $failedAttempts = User::recordFailedLoginAttempt((int) $user['id']);
            logLoginAttempt($email, false, $clientIp);

            if ($failedAttempts >= 5) {
                User::lockAccount((int) $user['id'], 30);
                $remainingSeconds = User::getRemainingLockoutSeconds((int) $user['id']);

                logSecurityEvent('account_lockout', [
                    'user_id' => (int) $user['id'],
                    'email' => $normalizedEmail,
                    'ip' => $clientIp,
                    'failed_attempts' => $failedAttempts,
                    'remaining_seconds' => $remainingSeconds
                ]);

                setFlashMessage(
                    'error',
                    'Too many failed login attempts. Your account is locked for ' .
                    $this->formatLockoutDuration($remainingSeconds) . '.'
                );
                return $this->showLoginForm();
            }

            $remainingAttempts = max(0, 5 - $failedAttempts);
            $messageSuffix = $remainingAttempts > 0
                ? ' ' . $remainingAttempts . ' attempt(s) remaining before lockout.'
                : '';

            setFlashMessage('error', 'Invalid email or password.' . $messageSuffix);
            return $this->showLoginForm();
        }

        User::resetLoginAttempts((int) $user['id']);
        logLoginAttempt($email, true, $clientIp);

        $emailVerified = true;
        if (array_key_exists('email_verified', $user)) {
            $emailVerified = !empty($user['email_verified']);
        }

        // Evaluate re-verification requirement before updating last login.
        // This preserves the previous login timestamp for threshold comparison.
        $lastLoginAt = User::getLastLoginTime((int) $user['id']);
        $lastLoginTimestamp = ($lastLoginAt !== null) ? strtotime((string) $lastLoginAt) : false;
        $hoursSinceLastLogin = null;
        if ($lastLoginTimestamp !== false) {
            $hoursSinceLastLogin = round((time() - $lastLoginTimestamp) / 3600, 2);
        }

        $reverifyThresholdHours = 48;
        $shouldReverify = $emailVerified && User::shouldReverifyEmail($user['id'], $reverifyThresholdHours);
        $verificationContext = [
            'user_id' => (int) $user['id'],
            'email' => strtolower((string) $user['email']),
            'email_verified' => $emailVerified ? 1 : 0,
            'email_verification_required' => EMAIL_VERIFICATION_REQUIRED ? 1 : 0,
            'last_login_at' => $lastLoginAt,
            'hours_since_last_login' => $hoursSinceLastLogin,
            'reverify_threshold_hours' => $reverifyThresholdHours,
            'should_reverify' => $shouldReverify ? 1 : 0,
            'ip' => $clientIp
        ];

        // If email is not verified, handle verification
        if (!$emailVerified) {
            clearAuthSession();
            if (EMAIL_VERIFICATION_REQUIRED) {
                $verificationContext['redirect_reason'] = 'email_unverified';
                $this->logVerificationRedirectDecision($verificationContext);
                setFlashMessage('error', 'Please verify your email address to continue.');
                redirect(BASE_URL . '/verify-email.php?email=' . urlencode($user['email']));
            }

            setFlashMessage('warning', 'Please verify your email address before logging in.');
            redirect(BASE_URL . '/verify-email.php?email=' . urlencode($user['email']));
        }

        if ($shouldReverify && EMAIL_VERIFICATION_REQUIRED) {
            clearAuthSession();
            $verificationContext['redirect_reason'] = 'last_login_threshold_exceeded';
            $this->logVerificationRedirectDecision($verificationContext);
            setFlashMessage(
                'warning',
                'For security, please verify your email address. You haven\'t logged in for over ' .
                $reverifyThresholdHours .
                ' hours.'
            );
            redirect(BASE_URL . '/verify-email.php?email=' . urlencode($user['email']));
        }

        $effectiveRole = getEffectiveUserRole($user);

        // Create session for fully-authenticated users only.
        session_start_safe();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $effectiveRole;
        $_SESSION['user_role'] = $effectiveRole;
        $_SESSION['email_verified'] = 1;

        // Update last login timestamp only after authentication succeeds.
        User::updateLastLogin((int) $user['id']);
        regenerateCsrfToken();

        setFlashMessage('success', 'Welcome back, ' . esc($user['username']) . '!');
        redirect(BASE_URL);
    }

    public function register($postData)
    {
        if (!ENABLE_REGISTRATION) {
            setFlashMessage('error', 'Registration is currently disabled.');
            return $this->showRegisterForm();
        }

        // Verify CSRF token and return a user-facing session-expired message.
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            setFlashMessage('error', 'Your session expired. Please submit the form again.');
            return $this->showRegisterForm();
        }

        // Get and sanitize input
        $username = trim($postData['name'] ?? '');
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';
        $passwordConfirm = $postData['password_confirm'] ?? '';

        // Validation
        $errors = [];

        // Validate username
        if (empty($username)) {
            $errors[] = 'Name is required.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Name must be at least 3 characters long.';
        } elseif (strlen($username) > 50) {
            $errors[] = 'Name must not exceed 50 characters.';
        }

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } else {
            if (strlen($password) < 12) {
                $errors[] = 'Password must be at least 12 characters long.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter.';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = 'Password must contain at least one lowercase letter.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Password must contain at least one number.';
            }
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
                $errors[] = 'Password must contain at least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?).';
            }
        }

        // Validate password confirmation
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            return $this->showRegisterForm();
        }

        $clientIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $registrationRateLimitKey = 'register:' . strtolower($email) . ':' . $clientIp;
        if (!rateLimit($registrationRateLimitKey, 3, 60 * 60)) {
            setFlashMessage('error', 'Too many registration attempts. Please try again in 1 hour.');
            return $this->showRegisterForm();
        }

        // Check if email already exists
        if (User::emailExists($email)) {
            setFlashMessage('error', 'This email is already registered. Please use a different email or login.');
            return $this->showRegisterForm();
        }

        // Check if username already exists
        if (User::usernameExists($username)) {
            setFlashMessage('error', 'This name is already taken. Please choose a different name.');
            return $this->showRegisterForm();
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Create the user
        $userId = User::create($username, $email, $passwordHash);

        if (!$userId) {
            error_log('Registration failed at user creation step for email: ' . strtolower($email));
            setFlashMessage('error', 'Registration failed. Please try again later.');
            return $this->showRegisterForm();
        }

        // Create email verification
        $token = User::generateSecureToken();
        $expiresIn = EmailConfig::emailVerificationExpirySeconds();
        $verificationCreated = User::createEmailVerification($userId, $email, $token, $expiresIn);

        $sendResult = ['success' => false];
        if ($verificationCreated) {
            $mailer = new ResendEmailService();
            $sendResult = $mailer->sendVerificationEmail($email, $username, $token);
        }

        clearAuthSession();

        if (!$verificationCreated) {
            setFlashMessage('error', 'Registration complete, but we could not create a verification request. Please resend the email.');
        } elseif (!empty($sendResult['success'])) {
            setFlashMessage('success', 'Registration successful! Please check your email to verify your account.');
        } else {
            setFlashMessage('error', 'Registration complete, but we could not send a verification email. Please resend it.');
        }

        redirect(BASE_URL . '/verify-email.php?email=' . urlencode($email));
    }
}
