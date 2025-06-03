<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// If user not found, redirect to login
if (!$user) {
    header('Location: login.php');
    exit;
}

// Get user's saved properties
$sql = "SELECT p.* FROM properties p 
        INNER JOIN favorites f ON p.id = f.property_id 
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's inquiries
$sql = "SELECT i.*, p.title as property_title, p.image as property_image 
        FROM inquiries i 
        INNER JOIN properties p ON i.property_id = p.id 
        WHERE i.user_id = ? 
        ORDER BY i.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$inquiries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle profile update
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    
    // Check if email is already taken by another user
    $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = 'Email is already taken by another user.';
    } else {
        // Handle profile picture upload
        $profile_picture = $user['profile_picture']; // Keep existing picture by default
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadProfilePicture($_FILES['profile_picture'], $user_id);
            if ($upload_result) {
                $profile_picture = $upload_result;
            } else {
                $error = 'Failed to upload profile picture. Please try again.';
            }
        }
        
        if (empty($error)) {
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $profile_picture, $user_id);
            
            if ($stmt->execute()) {
                $success = 'Profile updated successfully!';
                // Refresh user data
                $sql = "SELECT * FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | ModernEstate</title>
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
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="addproperty.php">Add Property</a>
                    <?php endif; ?>
                    <a href="profile.php" class="active">Profile</a>
                    <a href="logout.php" class="btn-login">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="profile-page">
        <div class="profile-hero">
            <h1>My Profile</h1>
            <p>Manage your account and view your activity</p>
        </div>

        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'assets/images/avatar-placeholder.jpg'; ?>" alt="Profile Avatar">
                    </div>
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <div class="profile-main">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <section class="profile-section">
                    <h2>Account Information</h2>
                    <form method="POST" action="profile.php" class="profile-form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                            <small>Max file size: 5MB. Allowed formats: JPG, PNG, JPEG</small>
                        </div>
                        
                        <button type="submit" class="btn-primary">Update Profile</button>
                    </form>
                </section>

                <section class="profile-section">
                    <h2>Saved Properties</h2>
                    <?php if (empty($saved_properties)): ?>
                        <p class="no-items">You haven't saved any properties yet.</p>
                    <?php else: ?>
                        <div class="saved-properties-grid">
                            <?php foreach ($saved_properties as $property): ?>
                                <div class="property-card">
                                    <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                                    <div class="property-info">
                                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                        <p class="price">₱<?php echo number_format($property['price']); ?></p>
                                        <p class="location"><?php echo htmlspecialchars($property['location']); ?></p>
                                        <a href="property.php?id=<?php echo $property['id']; ?>" class="btn-view">View Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="profile-section">
                    <h2>My Inquiries</h2>
                    <?php if (empty($inquiries)): ?>
                        <p class="no-items">You haven't made any inquiries yet.</p>
                    <?php else: ?>
                        <div class="inquiries-list">
                            <?php foreach ($inquiries as $inquiry): ?>
                                <div class="inquiry-card">
                                    <div class="inquiry-property">
                                        <img src="<?php echo htmlspecialchars($inquiry['property_image']); ?>" alt="<?php echo htmlspecialchars($inquiry['property_title']); ?>">
                                        <div class="inquiry-property-info">
                                            <h3><?php echo htmlspecialchars($inquiry['property_title']); ?></h3>
                                            <p class="inquiry-date"><?php echo date('F j, Y', strtotime($inquiry['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="inquiry-message">
                                        <p><?php echo htmlspecialchars($inquiry['message']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
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