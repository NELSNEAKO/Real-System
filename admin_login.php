<?php
session_start();

$host = 'localhost';
$db   = 'modern_estate';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_email'] = $admin['email'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin email not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      width: 350px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: .5rem;
    }
    .form-group input {
      width: 100%;
      padding: .5rem;
    }
    .btn {
      width: 100%;
      padding: .7rem;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
    }
    .error {
      color: red;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Admin Login</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Email:</label>
      <input type="email" name="email" required>
    </div>

    <div class="form-group">
      <label>Password:</label>
      <input type="password" id="password" name="password" required>
      <button type="button" onclick="togglePassword()">👁️</button>
    </div>

    <button class="btn" type="submit">Login</button>
  </form>
</div>

<script>
function togglePassword() {
  const input = document.getElementById("password");
  input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
