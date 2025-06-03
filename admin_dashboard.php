<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

$host = 'localhost';
$db   = 'modern_estate';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$users = $conn->query("SELECT * FROM users");

// Fetch agents
$agents = $conn->query("SELECT * FROM agents");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial;
      padding: 2rem;
      background: #f4f4f4;
    }
    h2 {
      margin-top: 2rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }
    th, td {
      border: 1px solid #ccc;
      padding: .6rem;
      text-align: left;
    }
    th {
      background: #007bff;
      color: white;
    }
    a {
      text-decoration: none;
      margin-right: 10px;
      color: #007bff;
    }
    .logout {
      float: right;
    }
  </style>
</head>
<body>

<h1>Welcome Admin</h1>
<a href="logout.php" class="logout">Logout</a>

<h2>Users</h2>
<table>
  <tr>
    <th>ID</th>
    <th>Email</th>
    <th>Actions</th>
  </tr>
  <?php while ($user = $users->fetch_assoc()): ?>
    <tr>
      <td><?= $user['id'] ?></td>
      <td><?= htmlspecialchars($user['email']) ?></td>
      <td>
        <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
        <a href="user_dashboard.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
       

      </td>
    </tr>
  <?php endwhile; ?>
</table>

<h2>Agents</h2>
<table>
  <tr>
    <th>ID</th>
    <th>Email</th>
    <th>Actions</th>
  </tr>
  <?php while ($agent = $agents->fetch_assoc()): ?>
    <tr>
      <td><?= $agent['id'] ?></td>
      <td><?= htmlspecialchars($agent['email']) ?></td>
      <td>
        <a href="edit_agent.php?id=<?= $agent['id'] ?>">Edit</a>
        <a href="delete_agent.php?id=<?= $agent['id'] ?>" onclick="return confirm('Delete this agent?')">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
