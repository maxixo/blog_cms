<?php
// config/database.php

// Database configuration
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'blog_cms');
}

// Get database connection
function db_connect() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

// Execute query with prepared statements
function db_query($sql, $types = '', $params = []) {
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error . " | SQL: " . $sql);
    }
    
    // Bind parameters if provided
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute statement
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    // Get result for SELECT queries
    $result = $stmt->get_result();
    
    return $result;
}

// Execute INSERT/UPDATE/DELETE queries
function db_execute($sql, $types = '', $params = []) {
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    
    $result = [
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