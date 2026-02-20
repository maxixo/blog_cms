<?php
/**
 * Migration script: Add last_login_at column to users table
 * Run this file to add the column needed for 48-hour re-verification logic
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config/database.php';

echo "========================================\n";
echo "Migration: Add last_login_at column\n";
echo "========================================\n\n";

try {
    // Check if column already exists
    $checkSql = "SHOW COLUMNS FROM users LIKE 'last_login_at'";
    $result = db_fetch_all($checkSql);
    
    if ($result && count($result) > 0) {
        echo "✓ Column 'last_login_at' already exists in users table.\n";
        echo "No migration needed.\n\n";
        exit(0);
    }
    
    // Add the column
    echo "Adding 'last_login_at' column to users table...\n";
    $sql = "ALTER TABLE users ADD COLUMN last_login_at DATETIME NULL AFTER email_verified_at";
    $result = db_execute($sql);
    
    if (empty($result['success'])) {
        throw new Exception("Failed to add column: " . ($result['error'] ?? 'Unknown error'));
    }
    
    echo "✓ Column 'last_login_at' added successfully.\n";
    
    // Add index for better query performance
    echo "\nAdding index for 'last_login_at' column...\n";
    $indexSql = "ALTER TABLE users ADD INDEX idx_last_login_at (last_login_at)";
    $result = db_execute($indexSql);
    
    if (empty($result['success'])) {
        echo "⚠ Warning: Failed to add index (column may already have an index): " . ($result['error'] ?? 'Unknown error') . "\n";
    } else {
        echo "✓ Index 'idx_last_login_at' added successfully.\n";
    }
    
    echo "\n========================================\n";
    echo "✓ Migration completed successfully!\n";
    echo "========================================\n\n";
    
    echo "You can now use the new 48-hour re-verification feature.\n";
    echo "Users who haven't logged in for 48+ hours will be prompted to verify their email.\n\n";
    
} catch (Exception $e) {
    echo "\n✗ Migration failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
