<?php
session_start();
require_once 'includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Get total users count
$users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total'];

// Get total properties count
$properties_query = "SELECT COUNT(*) as total FROM properties";
$properties_result = $conn->query($properties_query);
$total_properties = $properties_result->fetch_assoc()['total'];

// Get total inquiries count
$inquiries_query = "SELECT COUNT(*) as total FROM inquiries";
$inquiries_result = $conn->query($inquiries_query);
$total_inquiries = $inquiries_result->fetch_assoc()['total'];

// Get recent properties
$recent_properties_query = "SELECT * FROM properties ORDER BY created_at DESC LIMIT 5";
$recent_properties = $conn->query($recent_properties_query);

// Get recent inquiries
$recent_inquiries_query = "SELECT i.*, p.title as property_title 
                          FROM inquiries i 
                          LEFT JOIN properties p ON i.property_id = p.id 
                          ORDER BY i.created_at DESC LIMIT 5";
$recent_inquiries = $conn->query($recent_inquiries_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Modern Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stat-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h3 class="mb-4">Admin Panel</h3>
                <nav>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_properties.php">
                                <i class="fas fa-building"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_inquiries.php">
                                <i class="fas fa-envelope"></i> Inquiries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-users text-primary"></i>
                            <h3><?= $total_users ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-building text-success"></i>
                            <h3><?= $total_properties ?></h3>
                            <p>Total Properties</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="fas fa-envelope text-warning"></i>
                            <h3><?= $total_inquiries ?></h3>
                            <p>Total Inquiries</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Properties</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Price</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($property = $recent_properties->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($property['title']) ?></td>
                                                <td>$<?= number_format($property['price'], 2) ?></td>
                                                <td><?= htmlspecialchars($property['type']) ?></td>
                                                <td><?= htmlspecialchars($property['status']) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Inquiries -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Inquiries</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Property</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($inquiry = $recent_inquiries->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($inquiry['property_title']) ?></td>
                                                <td><?= htmlspecialchars($inquiry['name']) ?></td>
                                                <td><?= htmlspecialchars($inquiry['status']) ?></td>
                                                <td><?= date('M d, Y', strtotime($inquiry['created_at'])) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
