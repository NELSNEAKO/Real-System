<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Get property ID from URL
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get property details
$property = getPropertyById($conn, $property_id);

// If property doesn't exist, redirect to properties list
if (!$property) {
    header("Location: admin_properties.php");
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => sanitizeInput($_POST['title']),
        'description' => sanitizeInput($_POST['description']),
        'price' => (float)$_POST['price'],
        'location' => sanitizeInput($_POST['location']),
        'type' => sanitizeInput($_POST['type']),
        'bedrooms' => (int)$_POST['bedrooms'],
        'bathrooms' => (int)$_POST['bathrooms'],
        'area' => (float)$_POST['area'],
        'status' => sanitizeInput($_POST['status']),
        'image' => $property['image'] // Keep existing image by default
    ];

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadPropertyImage($_FILES['image'], $property_id);
        if ($upload_result) {
            $data['image'] = $upload_result;
        } else {
            $error_message = "Failed to upload image. Please try again.";
        }
    }

    if (empty($error_message)) {
        if (updateProperty($conn, $property_id, $data)) {
            $success_message = "Property updated successfully!";
            // Refresh property data
            $property = getPropertyById($conn, $property_id);
        } else {
            $error_message = "Failed to update property. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - Admin Panel</title>
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
        .property-image-preview {
            max-width: 300px;
            max-height: 200px;
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
                    <h2>Edit Property</h2>
                    <a href="admin_properties.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Properties
                    </a>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= htmlspecialchars($property['title']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="4" required><?= htmlspecialchars($property['description']) ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price (₱)</label>
                                                <input type="number" class="form-control" id="price" name="price" 
                                                       value="<?= $property['price'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="location" class="form-label">Location</label>
                                                <input type="text" class="form-control" id="location" name="location" 
                                                       value="<?= htmlspecialchars($property['location']) ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Type</label>
                                                <select class="form-select" id="type" name="type" required>
                                                    <option value="house" <?= $property['type'] === 'house' ? 'selected' : '' ?>>House</option>
                                                    <option value="apartment" <?= $property['type'] === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                                                    <option value="villa" <?= $property['type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                                                    <option value="condo" <?= $property['type'] === 'condo' ? 'selected' : '' ?>>Condo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="bedrooms" class="form-label">Bedrooms</label>
                                                <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                                       value="<?= $property['bedrooms'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                                <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                                       value="<?= $property['bathrooms'] ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="area" class="form-label">Area (sq ft)</label>
                                        <input type="number" class="form-control" id="area" name="area" 
                                               value="<?= $property['area'] ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="available" <?= $property['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                            <option value="sold" <?= $property['status'] === 'sold' ? 'selected' : '' ?>>Sold</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Current Image</label>
                                        <img src="../<?= htmlspecialchars($property['image']) ?>" 
                                             alt="Property Image" class="property-image-preview mb-2">
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="admin_properties.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 