<?php
// db.php - mysqli connection helper

// Update these constants to match your local MySQL setup
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ajaxdemo');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);

function get_db() {
    static $mysqli = null;
    if ($mysqli) return $mysqli;

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
        exit;
    }

    // Use UTF-8
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
