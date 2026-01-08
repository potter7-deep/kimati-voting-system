<?php
session_start();
// Error reporting configuration
if (getenv('RENDER')) {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Support both local development and Render.com production
$host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '0714740470DANmaina';
$db_name = getenv('DB_NAME') ?: 'voting_system';

try {
    $conn = new mysqli($host, $db_user, $db_password, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

$conn->set_charset("utf8");
?>
