<?php
require_once 'config.php';

echo "Starting database initialization...\n";

// Read the SQL file
$sql = file_get_contents('database.sql');

if ($sql === false) {
    die("Failed to read database.sql\n");
}

// The database name might be different in production
$db_name = getenv('DB_NAME') ?: 'voting_system';

// We'll execute the queries one by one
// Split by semicolon, but be careful with multi-line queries if they contain semicolons in strings (not the case here)
$queries = explode(';', $sql);

$success = 0;
$failed = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;
    
    // Skip CREATE DATABASE IF NOT EXISTS and USE because they might fail if we don't have enough permissions
    // or if we're already connected to the specific database
    if (stripos($query, 'CREATE DATABASE') === 0 || stripos($query, 'USE ') === 0) {
        continue;
    }

    if ($conn->query($query)) {
        $success++;
    } else {
        echo "Error executing query: " . $conn->error . "\n";
        echo "Query: " . substr($query, 0, 100) . "...\n";
        $failed++;
    }
}

echo "Database initialization completed.\n";
echo "Successful queries: $success\n";
echo "Failed queries: $failed\n";

if ($failed === 0) {
    echo "✓ Database is ready!\n";
} else {
    echo "⚠ Some queries failed. Please check the logs.\n";
}

$conn->close();
?>
