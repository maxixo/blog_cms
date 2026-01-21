<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}

function db()
{
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        error_log('Database connection failed: ' . mysqli_connect_error());
        http_response_code(500);
        exit('Database connection error.');
    }

    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}

function db_fail($message)
{
    error_log($message);
    http_response_code(500);
    exit('Database query error.');
}

function db_bind_params($stmt, $types, $params)
{
    $bind = [];
    $bind[] = $types;
    foreach ($params as $key => $value) {
        $bind[] = &$params[$key];
    }

    if (!call_user_func_array('mysqli_stmt_bind_param', $bind)) {
        db_fail('Failed binding parameters: ' . mysqli_stmt_error($stmt));
    }
}

function db_query($sql, $types = '', $params = [])
{
    $conn = db();
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        db_fail('Prepare failed: ' . mysqli_error($conn));
    }

    if ($types !== '' && !empty($params)) {
        db_bind_params($stmt, $types, $params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        db_fail('Execute failed: ' . mysqli_stmt_error($stmt));
    }

    return $stmt;
}

function db_fetch_one($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function db_fetch_all($stmt)
{
    $result = mysqli_stmt_get_result($stmt);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function db_last_id()
{
    return mysqli_insert_id(db());
}

function db_affected_rows($stmt)
{
    return mysqli_stmt_affected_rows($stmt);
}
