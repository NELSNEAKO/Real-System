<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: properties.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Estate | Find Your Dream Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <a href="index.php">Modern Real Estate</a>
            </div>
            <div class="nav-links">
                <a href="admin_login.php">Admin</a>
                <a href="about.php">About</a>
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

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Find Your Perfect Home</h1>
                <p>Discover the best properties in your desired location</p>
                <div class="search-container">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="text" name="location" placeholder="Enter location...">
                        <select name="type">
                            <option value="">Property Type</option>
                            <option value="house">House</option>
                            <option value="apartment">Apartment</option>
                            <option value="villa">Villa</option>
                        </select>
                        <button type="submit" class="btn-search">Search</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="featured-properties">
            <h2>Featured Properties</h2>
            <div class="property-grid">
                <?php
                require_once 'includes/config.php';
                require_once 'includes/functions.php';
                
                $featured_properties = getFeaturedProperties($conn, 6);
                
                foreach ($featured_properties as $property) {
                    echo '<div class="property-card">';
                    echo '<img src="' . htmlspecialchars($property['image']) . '" alt="' . htmlspecialchars($property['title']) . '">';
                    echo '<div class="property-info">';
                    echo '<h3>' . htmlspecialchars($property['title']) . '</h3>';
                    echo '<p class="price">₱' . number_format($property['price']) . '</p>';
                    echo '<p class="location">' . htmlspecialchars($property['location']) . '</p>';
                    echo '<a href="property.php?id=' . $property['id'] . '" class="btn-view">View Details</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Modern Estate</h3>
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