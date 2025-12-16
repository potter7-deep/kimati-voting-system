<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db_user = 'root';
$db_password = '0714740470DANmaina';
$db_name = 'voting_system';

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
