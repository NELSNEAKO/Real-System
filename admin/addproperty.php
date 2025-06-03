<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $price = (int)$_POST['price'];
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    $type = sanitizeInput($_POST['type']);

    // Image handling
    $imageName = $_FILES['image']['name'];
    $tempName = $_FILES['image']['tmp_name'];
    $uploadPath = '../assets/images/properties/' . basename($imageName);

    if (move_uploaded_file($tempName, $uploadPath)) {
        $sql = "INSERT INTO properties (title, price, description, location, type, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissss", $title, $price, $description, $location, $type, $imageName);
        
        if ($stmt->execute()) {
            $success = "Property added successfully!";
        } else {
            $error = "Failed to add property.";
        }
    } else {
        $error = "Failed to upload image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property | ModernEstate</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: var(--accent-color);
            transform: translateX(-5px);
        }

        .back-button i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <main class="auth-container">
        <div class="auth-box">
            <a href="admin_properties.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Properties
            </a>
            
            <h1>Add New Property</h1>
            <p class="auth-subtitle">Fill in the details to add a new property listing</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Property Title</label>
                        <input type="text" id="title" name="title" required placeholder="Enter property title">
                    </div>

                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" required placeholder="Enter property price">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required placeholder="Enter property location">
                    </div>

                    <div class="form-group">
                        <label for="type">Property Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="house">House</option>
                            <option value="apartment">Apartment</option>
                            <option value="villa">Villa</option>
                            <option value="condo">Condo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required placeholder="Enter property description"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Property Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <small class="text-muted">Upload a high-quality image of your property (Max size: 5MB)</small>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Property
                </button>
            </form>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>