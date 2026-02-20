<?php
/**
 * Database Schema Check Script
 *
 * This script verifies that all required database tables and columns exist.
 * Run from console: php check_database_schema.php
 *
 * Usage:
 *   php check_database_schema.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ANSI color codes for terminal output
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_CYAN', "\033[36m");
define('COLOR_RESET', "\033[0m");
define('COLOR_BOLD', "\033[1m");

/**
 * Print colored text
 */
function printColor($text, $color = COLOR_RESET)
{
    echo $color . $text . COLOR_RESET . PHP_EOL;
}

/**
 * Print section header
 */
function printHeader($title)
{
    echo PHP_EOL;
    echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
    echo COLOR_BOLD . COLOR_CYAN . "  $title" . COLOR_RESET . PHP_EOL;
    echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
}

// Display header
echo PHP_EOL;
echo COLOR_BOLD . COLOR_CYAN . "  Database Schema Check Tool" . COLOR_RESET . PHP_EOL;
echo COLOR_CYAN . "  Verifying database tables and columns for Blog CMS" . COLOR_RESET . PHP_EOL;

// Display database configuration
printHeader("Database Configuration");

echo "  Host: " . COLOR_CYAN . DB_HOST . COLOR_RESET . PHP_EOL;
echo "  Database: " . COLOR_CYAN . DB_NAME . COLOR_RESET . PHP_EOL;
echo "  User: " . COLOR_CYAN . DB_USER . COLOR_RESET . PHP_EOL;
echo "  Password: " . COLOR_CYAN . (empty(DB_PASS) ? '[empty]' : '[set]') . COLOR_RESET . PHP_EOL;

// Test database connection
printHeader("Testing Database Connection");

$conn = db_connect();

if (!$conn) {
    printColor("  FAIL: Could not connect to database", COLOR_RED);
    echo "  Please check your database configuration in .env file" . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;
    echo "  Common issues:" . PHP_EOL;
    echo "    - Database server not running" . PHP_EOL;
    echo "    - Incorrect database credentials" . PHP_EOL;
    echo "    - Database does not exist" . PHP_EOL;
    echo "    - User does not have proper permissions" . PHP_EOL;
    exit(1);
}

printColor("  OK: Database connection successful", COLOR_GREEN);

// Define required schema
$requiredSchema = [
    'users' => [
        'columns' => ['id', 'username', 'email', 'password_hash', 'avatar', 'role', 'created_at', 'email_verified', 'email_verified_at'],
        'description' => 'User accounts and authentication'
    ],
    'posts' => [
        'columns' => ['id', 'title', 'slug', 'content', 'excerpt', 'featured_image', 'author_id', 'category_id', 'status', 'published_at', 'created_at', 'updated_at'],
        'description' => 'Blog posts'
    ],
    'categories' => [
        'columns' => ['id', 'name', 'slug', 'description', 'created_at'],
        'description' => 'Post categories'
    ],
    'comments' => [
        'columns' => ['id', 'post_id', 'author_id', 'content', 'status', 'created_at'],
        'description' => 'Post comments'
    ],
    'email_verifications' => [
        'columns' => ['id', 'user_id', 'email', 'token', 'expires_at', 'verified_at', 'created_at'],
        'description' => 'Email verification tokens'
    ],
    'password_resets' => [
        'columns' => ['id', 'user_id', 'email', 'token', 'expires_at', 'used_at', 'created_at'],
        'description' => 'Password reset tokens'
    ]
];

// Check tables
printHeader("Checking Tables");

$tablesChecked = 0;
$tablesOK = 0;
$missingTables = [];
$missingColumns = [];

foreach ($requiredSchema as $tableName => $tableInfo) {
    $tablesChecked++;
    
    // Check if table exists
    $sql = "SHOW TABLES LIKE ?";
    $result = db_fetch($sql, 's', [$tableName]);
    
    if (!$result) {
        printColor("  ✗ Table '$tableName' - NOT FOUND", COLOR_RED);
        $missingTables[] = $tableName;
        $missingColumns[$tableName] = $tableInfo['columns'];
        continue;
    }
    
    echo "  ✓ Table '$tableName' - Found" . COLOR_RESET . PHP_EOL;
    echo "    Description: {$tableInfo['description']}" . PHP_EOL;
    $tablesOK++;
    
    // Check columns
    $sql = "SHOW COLUMNS FROM $tableName";
    $columnResult = db_query($sql);
    
    if (!$columnResult) {
        printColor("    ✗ Could not fetch columns", COLOR_RED);
        $missingColumns[$tableName] = $tableInfo['columns'];
        continue;
    }
    
    $existingColumns = [];
    while ($row = $columnResult->fetch_assoc()) {
        $existingColumns[] = $row['Field'];
    }
    
    $tableMissingColumns = [];
    foreach ($tableInfo['columns'] as $requiredColumn) {
        if (!in_array($requiredColumn, $existingColumns)) {
            $tableMissingColumns[] = $requiredColumn;
        }
    }
    
    if (!empty($tableMissingColumns)) {
        echo "    " . COLOR_RED . "✗ Missing columns: " . implode(', ', $tableMissingColumns) . COLOR_RESET . PHP_EOL;
        $missingColumns[$tableName] = $tableMissingColumns;
    } else {
        echo "    " . COLOR_GREEN . "✓ All required columns present" . COLOR_RESET . PHP_EOL;
    }
    
    echo PHP_EOL;
}

// Summary
printHeader("Summary");

$totalTables = count($requiredSchema);
$tablesPercent = round(($tablesOK / $tablesChecked) * 100, 0);

echo "  Tables Checked: $tablesChecked/$totalTables" . PHP_EOL;
echo "  Tables Found: " . ($tablesOK === $tablesChecked ? COLOR_GREEN : COLOR_YELLOW) . "$tablesOK" . COLOR_RESET . PHP_EOL;
echo "  Success Rate: " . ($tablesPercent === 100 ? COLOR_GREEN : COLOR_YELLOW) . "$tablesPercent%" . COLOR_RESET . PHP_EOL;

if (empty($missingTables) && empty($missingColumns)) {
    echo PHP_EOL;
    printColor("  ✓ All database tables and columns are present!", COLOR_GREEN);
} else {
    echo PHP_EOL;
    printColor("  ✗ Some database elements are missing", COLOR_RED);
    
    if (!empty($missingTables)) {
        echo PHP_EOL;
        echo COLOR_RED . "  Missing Tables:" . COLOR_RESET . PHP_EOL;
        foreach ($missingTables as $table) {
            echo "    - $table" . PHP_EOL;
        }
    }
    
    if (!empty($missingColumns)) {
        echo PHP_EOL;
        echo COLOR_RED . "  Missing Columns:" . COLOR_RESET . PHP_EOL;
        foreach ($missingColumns as $table => $columns) {
            echo "    - $table: " . implode(', ', $columns) . PHP_EOL;
        }
    }
    
    echo PHP_EOL;
    echo COLOR_YELLOW . "  To fix these issues, run the following SQL scripts:" . COLOR_RESET . PHP_EOL;
    
    if (in_array('comments', $missingTables)) {
        echo "    mysql -u " . DB_USER . " -p " . DB_NAME . " < database/create_comments_table.sql" . PHP_EOL;
    }
    if (in_array('email_verifications', $missingTables)) {
        echo "    mysql -u " . DB_USER . " -p " . DB_NAME . " < database/create_email_verifications_table.sql" . PHP_EOL;
    }
    if (in_array('password_resets', $missingTables)) {
        echo "    mysql -u " . DB_USER . " -p " . DB_NAME . " < database/create_password_resets_table.sql" . PHP_EOL;
    }
    
    echo PHP_EOL;
    echo COLOR_YELLOW . "  Or manually execute the SQL files in the database/ directory" . COLOR_RESET . PHP_EOL;
}

// Check for specific critical columns that might cause 500 errors
printHeader("Critical Columns Check");

$criticalChecks = [
    'users' => ['email_verified', 'email_verified_at'],
];

$criticalOK = true;
foreach ($criticalChecks as $table => $columns) {
    $sql = "SHOW COLUMNS FROM $table";
    $result = db_query($sql);
    
    if (!$result) {
        printColor("  ✗ Could not check table '$table'", COLOR_RED);
        $criticalOK = false;
        continue;
    }
    
    $existingColumns = [];
    while ($row = $result->fetch_assoc()) {
        $existingColumns[] = $row['Field'];
    }
    
    foreach ($columns as $column) {
        if (in_array($column, $existingColumns)) {
            echo "  ✓ $table.$column - Found" . COLOR_RESET . PHP_EOL;
        } else {
            echo "  " . COLOR_RED . "✗ $table.$column - MISSING (Critical!)" . COLOR_RESET . PHP_EOL;
            $criticalOK = false;
            
            echo COLOR_YELLOW . "    SQL to add this column:" . COLOR_RESET . PHP_EOL;
            if ($column === 'email_verified') {
                echo "    ALTER TABLE users ADD COLUMN email_verified TINYINT(1) DEFAULT 0;" . PHP_EOL;
            } elseif ($column === 'email_verified_at') {
                echo "    ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL;" . PHP_EOL;
            }
        }
    }
}

if ($criticalOK) {
    echo PHP_EOL;
    printColor("  ✓ All critical columns are present", COLOR_GREEN);
} else {
    echo PHP_EOL;
    printColor("  ✗ Missing critical columns may cause 500 errors", COLOR_RED);
}

echo PHP_EOL;
echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
echo PHP_EOL;

exit($criticalOK ? 0 : 1);
