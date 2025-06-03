<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle property deletion
if (isset($_POST['delete_property'])) {
    $property_id = (int)$_POST['property_id'];
    if (deleteProperty($conn, $property_id)) {
        $success_message = "Property deleted successfully!";
    } else {
        $error_message = "Failed to delete property.";
    }
}

// Get all properties with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM properties ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$properties = $stmt->get_result();

// Get total count for pagination
$total_result = $conn->query("SELECT FOUND_ROWS()");
$total = $total_result->fetch_row()[0];
$total_pages = ceil($total / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - Admin Panel</title>
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
        .property-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
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
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_properties.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Properties</h2>
                    <a href="addproperty.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Property
                    </a>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($property = $properties->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="<?= htmlspecialchars($property['image']) ?>" 
                                                     alt="<?= htmlspecialchars($property['title']) ?>" 
                                                     class="property-image">
                                            </td>
                                            <td><?= htmlspecialchars($property['title']) ?></td>
                                            <td>₱<?= number_format($property['price']) ?></td>
                                            <td><?= htmlspecialchars($property['location']) ?></td>
                                            <td><?= htmlspecialchars($property['type']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $property['status'] === 'available' ? 'success' : 'danger' ?>">
                                                    <?= htmlspecialchars($property['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_property.php?id=<?= $property['id'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this property?');">
                                                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                                    <button type="submit" name="delete_property" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 