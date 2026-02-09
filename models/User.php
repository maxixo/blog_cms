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
}
