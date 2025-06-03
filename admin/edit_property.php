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
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: 2rem;
        }

        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border-radius: 15px;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .property-image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #2c3e50;
            border-color: #2c3e50;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1.5rem !important;
        }

        .text-muted {
            font-size: 0.85rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin: 1rem;
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            

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
                                        <img src="../assets/images/properties/<?= htmlspecialchars($property['image']) ?>" alt="Property Image" class="property-image-preview mb-2">
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