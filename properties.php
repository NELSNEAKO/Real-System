<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get filter parameters
$location = isset($_GET['location']) ? sanitizeInput($_GET['location']) : '';
$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;

// Get properties based on filters
$result = searchProperties($conn, $location, $type, $min_price, $max_price, $page, $per_page);
$properties = $result['properties'];
$total_pages = $result['total_pages'];
$current_page = $result['current_page'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties | ModernEstate</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <a href="index.php">Modern Real Estate</a>
            </div>
            <div class="nav-links">
                <a href="properties.php" class="active">Properties</a>
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

    <main class="properties-page">
        <div class="properties-header">
            <h1>Find Your Dream Property</h1>
            <p>Browse through our extensive collection of properties</p>
        </div>

        <div class="properties-container">
            <aside class="filters-sidebar">
                <form action="properties.php" method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter location...">
                    </div>

                    <div class="filter-group">
                        <label for="type">Property Type</label>
                        <select id="type" name="type">
                            <option value="">All Types</option>
                            <option value="house" <?php echo $type === 'house' ? 'selected' : ''; ?>>House</option>
                            <option value="apartment" <?php echo $type === 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="villa" <?php echo $type === 'villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="condo" <?php echo $type === 'condo' ? 'selected' : ''; ?>>Condo</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="min_price">Min Price</label>
                        <input type="number" id="min_price" name="min_price" value="<?php echo $min_price; ?>" placeholder="Min price">
                    </div>

                    <div class="filter-group">
                        <label for="max_price">Max Price</label>
                        <input type="number" id="max_price" name="max_price" value="<?php echo $max_price; ?>" placeholder="Max price">
                    </div>

                    <button type="submit" class="btn-primary">Apply Filters</button>
                </form>
            </aside>

            <div class="properties-grid">
                <?php if (empty($properties)): ?>
                    <div class="no-results">
                        <p>No properties found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card">
                            <img src="<?php echo htmlspecialchars($property['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($property['title']); ?>"
                                 loading="lazy"
                                 width="400"
                                 height="300"
                                 decoding="async">
                            <div class="property-info">
                                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                <p class="price">₱<?php echo number_format($property['price']); ?></p>
                                <p class="location"><?php echo htmlspecialchars($property['location']); ?></p>
                                <div class="property-features">
                                    <span><?php echo $property['bedrooms']; ?> beds</span>
                                    <span><?php echo $property['bathrooms']; ?> baths</span>
                                    <span><?php echo $property['area']; ?> sqft</span>
                                </div>
                                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn-view">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&location=<?php echo urlencode($location); ?>&type=<?php echo urlencode($type); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" class="page-link">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&location=<?php echo urlencode($location); ?>&type=<?php echo urlencode($type); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                       class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&location=<?php echo urlencode($location); ?>&type=<?php echo urlencode($type); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" class="page-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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