<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';

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

    public function login($postData)
    {
        return $this->showLoginForm('Authentication is not configured yet.');
    }

    public function register($postData)
    {
        if (!ENABLE_REGISTRATION) {
            setFlashMessage('error', 'Registration is currently disabled.');
            return $this->showRegisterForm();
        }

        // Verify CSRF token silently - if invalid, just regenerate and continue
        if (!isset($postData['csrf_token']) || !verifyCsrfToken($postData['csrf_token'])) {
            // Silently handle CSRF failure - regenerate token and show form without error
            // This keeps security but doesn't expose technical messages to users
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
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
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
            setFlashMessage('error', 'Registration failed. Please try again later.');
            return $this->showRegisterForm();
        }

        // Set session for the new user
        session_start_safe();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        // Success message and redirect
        setFlashMessage('success', 'Registration successful! Welcome, ' . esc($username) . '!');
        
        // Redirect to home page
        redirect(BASE_URL);
    }
}
