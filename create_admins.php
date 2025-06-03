<?php
$host = 'localhost';
$db = 'modern_estate';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin data
$admins = [
    ['admin1@gmail.com', password_hash('123', PASSWORD_DEFAULT)],
    ['admin2@gmail.com', password_hash('456', PASSWORD_DEFAULT)],
    ['admin@gmail.com',  password_hash('789', PASSWORD_DEFAULT)],
];

// Insert admins
foreach ($admins as $admin) {
    $stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $admin[0], $admin[1]);
    $stmt->execute();
}

echo "Admins inserted successfully.";

$conn->close();
?>
