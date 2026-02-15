<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    /**
     * Create a new user
     * 
     * @param string $username
     * @param string $email
     * @param string $passwordHash
     * @return int|false Returns user ID on success, false on failure
     */
    public static function create($username, $email, $passwordHash)
    {
        $sql = "INSERT INTO users (username, email, password_hash, avatar, role) 
                VALUES (?, ?, ?, 'default-avatar.png', 'user')";
        
        $result = db_execute($sql, 'sss', [$username, $email, $passwordHash]);

        return (!empty($result['success'])) ? ($result['insert_id'] ?? false) : false;
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|null
     */
    public static function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        return db_fetch($sql, 's', [$email]);
    }

    /**
     * Find user by username
     * 
     * @param string $username
     * @return array|null
     */
    public static function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        return db_fetch($sql, 's', [$username]);
    }

    /**
     * Find user by ID
     * 
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        return db_fetch($sql, 'i', [$id]);
    }

    /**
     * Verify user password
     * 
     * @param string $password
     * @param string $passwordHash
     * @return bool
     */
    public static function verifyPassword($password, $passwordHash)
    {
        return password_verify($password, $passwordHash);
    }

    /**
     * Check if email exists
     * 
     * @param string $email
     * @return bool
     */
    public static function emailExists($email)
    {
        $user = self::findByEmail($email);
        return $user !== null;
    }

    /**
     * Check if username exists
     * 
     * @param string $username
     * @return bool
     */
    public static function usernameExists($username)
    {
        $user = self::findByUsername($username);
        return $user !== null;
    }

    public static function createEmailVerification($userId, $email, $token, $expiresIn)
    {
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (int) $expiresIn);

        $sql = "INSERT INTO email_verifications (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)";
        $result = db_execute($sql, 'isss', [$userId, $email, $tokenHash, $expiresAt]);

        return (!empty($result['success'])) ? ($result['insert_id'] ?? false) : false;
    }

    public static function findEmailVerificationByToken($token)
    {
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT * FROM email_verifications WHERE token = ? LIMIT 1";
        return db_fetch($sql, 's', [$tokenHash]);
    }

    public static function verifyEmail($token)
    {
        $verification = self::findEmailVerificationByToken($token);
        if (!$verification) {
            return false;
        }

        if (!empty($verification['verified_at'])) {
            return self::markEmailAsVerified((int) $verification['user_id']);
        }

        $expiresAt = strtotime($verification['expires_at']);
        if ($expiresAt !== false && $expiresAt < time()) {
            return false;
        }

        $updateSql = "UPDATE email_verifications SET verified_at = NOW() WHERE id = ? AND verified_at IS NULL";
        $updateResult = db_execute($updateSql, 'i', [$verification['id']]);
        if (empty($updateResult['success'])) {
            return false;
        }

        return self::markEmailAsVerified((int) $verification['user_id']);
    }

    public static function deleteEmailVerificationsForUser($userId)
    {
        $sql = "DELETE FROM email_verifications WHERE user_id = ?";
        $result = db_execute($sql, 'i', [(int) $userId]);
        return !empty($result['success']);
    }

    public static function markEmailAsVerified($userId)
    {
        $sql = "UPDATE users SET email_verified = 1, email_verified_at = NOW() WHERE id = ?";
        $result = db_execute($sql, 'i', [$userId]);
        return !empty($result['success']);
    }

    public static function isEmailVerified($userId)
    {
        $sql = "SELECT email_verified FROM users WHERE id = ? LIMIT 1";
        $user = db_fetch($sql, 'i', [$userId]);
        return !empty($user) && !empty($user['email_verified']);
    }

    public static function createPasswordReset($userId, $email, $token, $expiresIn)
    {
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (int) $expiresIn);

        $sql = "INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)";
        $result = db_execute($sql, 'isss', [$userId, $email, $tokenHash, $expiresAt]);

        return (!empty($result['success'])) ? ($result['insert_id'] ?? false) : false;
    }

    public static function findPasswordResetByToken($token)
    {
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT * FROM password_resets WHERE token = ? LIMIT 1";
        return db_fetch($sql, 's', [$tokenHash]);
    }

    public static function isValidPasswordResetToken($token)
    {
        $reset = self::findPasswordResetByToken($token);
        if (!$reset) {
            return false;
        }

        if (!empty($reset['used_at'])) {
            return false;
        }

        $expiresAt = strtotime($reset['expires_at']);
        return $expiresAt !== false && $expiresAt >= time();
    }

    public static function deletePasswordResetToken($token)
    {
        $tokenHash = hash('sha256', $token);
        $sql = "DELETE FROM password_resets WHERE token = ?";
        $result = db_execute($sql, 's', [$tokenHash]);
        return !empty($result['success']);
    }

    public static function deleteExpiredPasswordResets()
    {
        $sql = "DELETE FROM password_resets WHERE expires_at <= NOW() OR used_at IS NOT NULL";
        $result = db_execute($sql);
        return !empty($result['success']);
    }

    public static function updatePassword($userId, $newPasswordHash)
    {
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $result = db_execute($sql, 'si', [$newPasswordHash, $userId]);
        return !empty($result['success']);
    }

    public static function verifyCurrentPassword($userId, $currentPassword)
    {
        $user = self::findById($userId);
        if (!$user || empty($user['password_hash'])) {
            return false;
        }

        return password_verify($currentPassword, $user['password_hash']);
    }

    public static function generateSecureToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Update user's last login timestamp
     * 
     * @param int $userId
     * @return bool
     */
    public static function updateLastLogin($userId)
    {
        $sql = "UPDATE users SET last_login_at = NOW() WHERE id = ?";
        $result = db_execute($sql, 'i', [$userId]);
        return !empty($result['success']);
    }

    /**
     * Get user's last login time
     * 
     * @param int $userId
     * @return string|null Returns timestamp or null if never logged in
     */
    public static function getLastLoginTime($userId)
    {
        $sql = "SELECT last_login_at FROM users WHERE id = ? LIMIT 1";
        $user = db_fetch($sql, 'i', [$userId]);
        return $user['last_login_at'] ?? null;
    }

    /**
     * Check if user should reverify email (hasn't logged in for X hours)
     * 
     * @param int $userId
     * @param int $hours Number of hours threshold (default 48)
     * @return bool True if user should reverify
     */
    public static function shouldReverifyEmail($userId, $hours = 48)
    {
        $lastLogin = self::getLastLoginTime($userId);
        
        if ($lastLogin === null) {
            return true; // User has never logged in
        }
        
        $lastLoginTime = strtotime($lastLogin);
        if ($lastLoginTime === false) {
            return true; // Invalid timestamp
        }
        
        $hoursSinceLastLogin = (time() - $lastLoginTime) / 3600;
        
        return $hoursSinceLastLogin >= $hours;
    }
}
