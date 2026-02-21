<?php
// config/database.php

// Database configuration
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'blog_cms');
}

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

function db_log_error($message, $context = [])
{
    $line = $message;
    if (!empty($context)) {
        $line .= ' | ' . json_encode($context, JSON_UNESCAPED_SLASHES);
    }

    error_log($line);
}

// Get database connection
function db_connect() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } catch (Throwable $e) {
            db_log_error('Database connection exception', ['error' => $e->getMessage()]);
            $conn = null;
            return null;
        }

        if (!$conn || $conn->connect_error) {
            db_log_error('Database connection failed', [
                'error' => $conn ? $conn->connect_error : 'Unknown connection error',
                'host' => DB_HOST,
                'database' => DB_NAME
            ]);
            $conn = null;
            return null;
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

// Execute query with prepared statements
function db_query($sql, $types = '', $params = []) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        db_log_error('Database prepare failed', ['error' => $conn->error, 'sql' => $sql]);
        return false;
    }
    
    // Bind parameters if provided
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute statement
    if (!$stmt->execute()) {
        db_log_error('Database execute failed', ['error' => $stmt->error, 'sql' => $sql]);
        $stmt->close();
        return false;
    }
    
    // Get result for SELECT queries
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

// Execute INSERT/UPDATE/DELETE queries
function db_execute($sql, $types = '', $params = []) {
    $conn = db_connect();
    if (!$conn) {
        return [
            'success' => false,
            'affected_rows' => 0,
            'insert_id' => 0,
            'error' => 'Database connection unavailable',
            'errno' => 0
        ];
    }

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        db_log_error('Database prepare failed', ['error' => $conn->error, 'errno' => $conn->errno, 'sql' => $sql]);
        return [
            'success' => false,
            'affected_rows' => 0,
            'insert_id' => 0,
            'error' => $conn->error,
            'errno' => (int) $conn->errno
        ];
    }
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        db_log_error('Database execute failed', ['error' => $stmt->error, 'errno' => $stmt->errno, 'sql' => $sql]);
        $stmt->close();
        return [
            'success' => false,
            'affected_rows' => 0,
            'insert_id' => 0,
            'error' => $stmt->error,
            'errno' => (int) $stmt->errno
        ];
    }
    
    $result = [
        'success' => true,
        'affected_rows' => $stmt->affected_rows,
        'insert_id' => $stmt->insert_id
    ];
    
    $stmt->close();
    
    return $result;
}

// Get single row
function db_fetch($sql, $types = '', $params = []) {
    $result = db_query($sql, $types, $params);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get all rows
function db_fetch_all($sql, $types = '', $params = []) {
    $result = db_query($sql, $types, $params);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return [];
}
