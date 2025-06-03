<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';


// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
        


$property_id = (int)$_GET['id'];
$property = getPropertyById($conn, $property_id);

if (!$property) {
    header('Location: properties.php');
    exit;
}

// Get additional property images
$sql = "SELECT * FROM property_images WHERE property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property_images = $result->fetch_all(MYSQLI_ASSOC);

// Handle inquiry submission
$inquiry_success = '';
$inquiry_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $message = sanitizeInput($_POST['message']);
    
    $sql = "INSERT INTO inquiries (property_id, user_id, name, email, phone, message) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt->bind_param("iissss", $property_id, $user_id, $name, $email, $phone, $message);
    
    if ($stmt->execute()) {
        $inquiry_success = 'Your inquiry has been sent successfully!';
    } else {
        $inquiry_error = 'Failed to send inquiry. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> | ModernEstate</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <a href="index.php">ModernEstate</a>
            </div>
            <div class="nav-links">
                <a href="properties.php">Properties</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php" class="btn-login">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="property-detail">
        <div class="property-gallery">
            <div class="main-image">
                <img src="assets/images/properties/<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
            </div>
            <?php if (!empty($property_images)): ?>
                <div class="thumbnail-grid">
                    <?php foreach ($property_images as $image): ?>
                        <div class="thumbnail">
                            <img src="assets/images/properties/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Property image">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="property-content">
            <div class="property-header">
                <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                <p class="price">₱<?php echo number_format($property['price']); ?></p>
                <p class="location"><?php echo htmlspecialchars($property['location']); ?></p>
            </div>

            <div class="property-features">
                <div class="feature">
                    <span class="feature-label">Bedrooms</span>
                    <span class="feature-value"><?php echo $property['bedrooms']; ?></span>
                </div>
                <div class="feature">
                    <span class="feature-label">Bathrooms</span>
                    <span class="feature-value"><?php echo $property['bathrooms']; ?></span>
                </div>
                <div class="feature">
                    <span class="feature-label">Area</span>
                    <span class="feature-value"><?php echo $property['area']; ?> sqft</span>
                </div>
                <div class="feature">
                    <span class="feature-label">Type</span>
                    <span class="feature-value"><?php echo ucfirst($property['type']); ?></span>
                </div>
            </div>

            <div class="property-description">
                <h2>Description</h2>
                <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
            </div>

            <div class="inquiry-section">
                <h2>Interested in this property?</h2>
                <?php if ($inquiry_success): ?>
                    <div class="alert alert-success"><?php echo $inquiry_success; ?></div>
                <?php endif; ?>
                <?php if ($inquiry_error): ?>
                    <div class="alert alert-error"><?php echo $inquiry_error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="property.php?id=<?php echo $property_id; ?>" class="inquiry-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Inquiry</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>ModernEstate</h3>
                <p>Your trusted partner in finding the perfect property.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="properties.php">Properties</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: info@modernestate.com</p>
                <p>Phone: (555) 123-4567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ModernEstate. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 