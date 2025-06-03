<?php
require_once 'includes/config.php';

// Admin account details
$username = 'admin123';
$email = 'admin123@gmail.com';
$password = 'admin123';
$full_name = 'Admin User';
$phone = '09123456789';
$role = 'admin';

// First, check if admin already exists
$check_stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Admin exists, update the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update_stmt->bind_param("ss", $hashed_password, $email);
    
    if ($update_stmt->execute()) {
        echo "Admin password updated successfully!<br>";
        echo "New password hash: " . $hashed_password . "<br>";
    } else {
        echo "Error updating admin password: " . $update_stmt->error . "<br>";
    }   
    $update_stmt->close();
} else {
    // Create new admin account
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, role, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $insert_stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $role);
    
    if ($insert_stmt->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Password hash: " . $hashed_password . "<br>";
    } else {
        echo "Error creating admin account: " . $insert_stmt->error . "<br>";
    }
    $insert_stmt->close();
}

// Display login credentials
echo "<br>Login credentials:<br>";
echo "Email: " . $email . "<br>";
echo "Password: " . $password . "<br>";

$check_stmt->close();
$conn->close();
?> 