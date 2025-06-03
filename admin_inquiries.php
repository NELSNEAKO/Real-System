<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle inquiry status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id']) && isset($_POST['status'])) {
    $inquiry_id = (int)$_POST['inquiry_id'];
    $status = sanitizeInput($_POST['status']);
    
    $sql = "UPDATE inquiries SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $inquiry_id);
    $stmt->execute();
}

// Get all inquiries with property and user details
$sql = "SELECT i.*, p.title as property_title, u.username, u.email 
        FROM inquiries i 
        JOIN properties p ON i.property_id = p.id 
        JOIN users u ON i.user_id = u.id 
        ORDER BY i.created_at DESC";
$result = $conn->query($sql);
$inquiries = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries | ModernEstate</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <a href="index.php">Modern Real Estate</a>
            </div>
            <div class="nav-links">
                <a href="properties.php">Properties</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="addproperty.php">Add Property</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php" class="btn-login">Logout</a>
            </div>
        </nav>
    </header>

    <main class="admin-page">
        <div class="admin-header">
            <h1>Manage Inquiries</h1>
            <p>View and manage property inquiries from potential buyers</p>
        </div>

        <div class="admin-content">
            <div class="inquiries-list">
                <?php if (empty($inquiries)): ?>
                    <p class="no-data">No inquiries found.</p>
                <?php else: ?>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <div class="inquiry-card">
                            <div class="inquiry-header">
                                <h3>Inquiry for: <?php echo htmlspecialchars($inquiry['property_title']); ?></h3>
                                <span class="status-badge <?php echo $inquiry['status']; ?>">
                                    <?php echo ucfirst($inquiry['status']); ?>
                                </span>
                            </div>
                            <div class="inquiry-details">
                                <p><strong>From:</strong> <?php echo htmlspecialchars($inquiry['username']); ?> (<?php echo htmlspecialchars($inquiry['email']); ?>)</p>
                                <p><strong>Message:</strong> <?php echo htmlspecialchars($inquiry['message']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($inquiry['created_at'])); ?></p>
                            </div>
                            <div class="inquiry-actions">
                                <form action="" method="POST" class="status-form">
                                    <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $inquiry['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                        <option value="completed" <?php echo $inquiry['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $inquiry['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                                <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>" class="btn-primary">Reply via Email</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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