<?php
/**
 * Revert migration: Remove last_login_at column from users table
 * Run this file to remove the column added for 48-hour re-verification logic
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config/database.php';

echo "========================================\n";
echo "Revert Migration: Remove last_login_at column\n";
echo "========================================\n\n";

try {
    // Check if column exists
    $checkSql = "SHOW COLUMNS FROM users LIKE 'last_login_at'";
    $result = db_fetch_all($checkSql);
    
    if (!$result || count($result) === 0) {
        echo "✓ Column 'last_login_at' does not exist in users table.\n";
        echo "No revert needed.\n\n";
        exit(0);
    }
    
    // Drop the column
    echo "Dropping 'last_login_at' column from users table...\n";
    $sql = "ALTER TABLE users DROP COLUMN last_login_at";
    $result = db_execute($sql);
    
    if (empty($result['success'])) {
        throw new Exception("Failed to drop column: " . ($result['error'] ?? 'Unknown error'));
    }
    
    echo "✓ Column 'last_login_at' dropped successfully.\n";
    
    echo "\n========================================\n";
    echo "✓ Migration reverted successfully!\n";
    echo "========================================\n\n";
    
    echo "The 48-hour re-verification feature has been removed.\n";
    echo "Your authentication system is back to its original state.\n\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration revert failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
