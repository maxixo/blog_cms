<?php
/**
 * One-time production migration for users table.
 *
 * Run options:
 * 1) CLI (recommended): php migrate.php
 * 2) Web (temporary): /migrate.php?key=YOUR_MIGRATION_KEY
 *    Requires MIGRATION_KEY env var to be set.
 */

header('Content-Type: text/plain; charset=utf-8');

$isCli = (php_sapi_name() === 'cli');
$migrationKey = (string) (getenv('MIGRATION_KEY') ?: '');
$providedKey = (string) ($_GET['key'] ?? '');

if (!$isCli) {
    if ($migrationKey === '' || !hash_equals($migrationKey, $providedKey)) {
        http_response_code(403);
        echo "Forbidden\n";
        echo "Set MIGRATION_KEY and call /migrate.php?key=YOUR_MIGRATION_KEY\n";
        exit;
    }
}

$host = (string) (getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '');
$database = (string) (getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: '');
$user = (string) (getenv('DB_USER') ?: getenv('MYSQLUSER') ?: '');
$password = (string) (getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '');
$port = (int) (getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: 3306);

if ($host === '' || $database === '' || $user === '') {
    http_response_code(500);
    echo "Missing database environment variables.\n";
    echo "Need DB_* or MYSQL* values (host, database, user).\n";
    exit(1);
}

$conn = @new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed: " . $conn->connect_error . "\n";
    exit(1);
}

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

$conn->set_charset('utf8mb4');

echo "Connected to {$database} at {$host}:{$port}\n";
echo "Running users table migration...\n\n";

$usersTableExists = false;
$tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
if ($tableCheck !== false && $tableCheck->num_rows > 0) {
    $usersTableExists = true;
}

if (!$usersTableExists) {
    echo "users table not found. Creating it...\n";
    $createUsersSql = "CREATE TABLE users (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        avatar VARCHAR(255) NOT NULL DEFAULT 'default-avatar.png',
        role VARCHAR(20) NOT NULL DEFAULT 'user',
        bio TEXT NULL,
        email_verified TINYINT(1) NOT NULL DEFAULT 0,
        email_verified_at DATETIME NULL,
        login_attempts INT NOT NULL DEFAULT 0,
        lockout_until DATETIME NULL,
        last_login_at DATETIME NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_users_username (username),
        UNIQUE KEY uniq_users_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($conn->query($createUsersSql) !== true) {
        http_response_code(500);
        echo "Failed to create users table: " . $conn->error . "\n";
        $conn->close();
        exit(1);
    }

    echo "users table created.\n\n";
}

$columns = [
    'role' => "VARCHAR(20) NOT NULL DEFAULT 'user'",
    'email_verified' => "TINYINT(1) NOT NULL DEFAULT 0",
    'email_verified_at' => "DATETIME NULL",
    'login_attempts' => "INT NOT NULL DEFAULT 0",
    'lockout_until' => "DATETIME NULL",
    'last_login_at' => "DATETIME NULL",
];

$added = [];
$skipped = [];
$failed = [];

foreach ($columns as $name => $definition) {
    $checkSql = "SHOW COLUMNS FROM users LIKE '" . $conn->real_escape_string($name) . "'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult === false) {
        $failed[] = $name . " (check failed: " . $conn->error . ")";
        continue;
    }

    if ($checkResult->num_rows > 0) {
        $skipped[] = $name;
        continue;
    }

    $alterSql = "ALTER TABLE users ADD COLUMN {$name} {$definition}";
    if ($conn->query($alterSql) === true) {
        $added[] = $name;
    } else {
        $failed[] = $name . " (" . $conn->error . ")";
    }
}

echo "Added columns: " . (empty($added) ? 'none' : implode(', ', $added)) . "\n";
echo "Already existed: " . (empty($skipped) ? 'none' : implode(', ', $skipped)) . "\n";
echo "Failed: " . (empty($failed) ? 'none' : implode('; ', $failed)) . "\n\n";

echo "Final users columns:\n";
$final = $conn->query("SHOW COLUMNS FROM users");
if ($final !== false) {
    while ($row = $final->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
}

$conn->close();

if (!empty($failed)) {
    exit(1);
}

echo "\nMigration completed successfully.\n";
exit(0);
