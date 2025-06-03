<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | JD RealEstate</title>
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
                <a href="about.php" class="active">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="addproperty.php">Add Property</a>
                    <?php endif; ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php" class="btn-login">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="about-page">
        <div class="about-hero">
            <h1>About Modern Real Estate</h1>
            <p>Your trusted partner in real estate since 2010</p>
        </div>

        <div class="about-content">
            <section class="about-section">
                <h2>Our Story</h2>
                <p>ModernEstate was founded with a vision to revolutionize the real estate industry by providing a seamless, transparent, and user-friendly platform for property transactions. Over the years, we have helped thousands of families find their dream homes and investors make smart property decisions.</p>
            </section>

            <section class="about-section">
                <h2>Our Mission</h2>
                <p>We strive to make property buying, selling, and renting as simple and efficient as possible. Our commitment to excellence, transparency, and customer satisfaction sets us apart in the industry.</p>
            </section>

            <section class="about-section">
                <h2>Why Choose Us</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3>Expert Guidance</h3>
                        <p>Our team of experienced real estate professionals provides personalized guidance throughout your property journey.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Wide Selection</h3>
                        <p>Access to a diverse portfolio of properties across different locations and price ranges.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Transparent Process</h3>
                        <p>Clear communication and honest dealings at every step of the transaction.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Customer Support</h3>
                        <p>Dedicated support team available to assist you with any queries or concerns.</p>
                    </div>
                </div>
            </section>

            
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