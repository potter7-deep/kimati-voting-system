<?php
require_once 'config.php';

// First, let's check if the admin user exists
$check_query = "SELECT * FROM users WHERE email = 'admin@university.edu'";
$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    echo "Admin user already exists. Deleting old record...\n";
    $delete_query = "DELETE FROM users WHERE email = 'admin@university.edu'";
    $conn->query($delete_query);
}

// Generate the password hash
$password = 'admin';
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

echo "Hashed password: " . $hashed_password . "\n";

// Insert the admin user
$insert_query = "INSERT INTO users (name, email, password, registration_number, year, role) 
                 VALUES ('Admin User', 'admin@university.edu', ?, 'ADMIN001', 1, 'admin')";

$stmt = $conn->prepare($insert_query);
if (!$stmt) {
    echo "Prepare failed: " . $conn->error . "\n";
    exit;
}

$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "✓ Admin user created successfully!\n";
    echo "Email: admin@university.edu\n";
    echo "Password: admin\n";
} else {
    echo "✗ Failed to create admin user: " . $stmt->error . "\n";
}

// Verify it was created
$verify_query = "SELECT id, name, email, role FROM users WHERE email = 'admin@university.edu'";
$verify_result = $conn->query($verify_query);

if ($verify_result->num_rows > 0) {
    $user = $verify_result->fetch_assoc();
    echo "\nAdmin user verified in database:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Name: " . $user['name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "\n✓ Ready to login!\n";
} else {
    echo "✗ Failed to verify admin user\n";
}

$conn->close();
?>
