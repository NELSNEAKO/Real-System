<?php
// Get current page filename to determine active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <a href="admin_dashboard.php">Modern Real Estate</a>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link <?= $current_page === 'admin_dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-home icon"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_users.php" class="nav-link <?= $current_page === 'admin_users.php' ? 'active' : '' ?>">
                    <i class="fas fa-users icon"></i> Users
                </a>
            </li>
        </ul>
        <h4 class="nav-section-title">DATA</h4>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="admin_properties.php" class="nav-link <?= $current_page === 'admin_properties.php' ? 'active' : '' ?>">
                    <i class="fas fa-building icon"></i> Properties
                </a>
            </li>
            <li class="nav-item">
                <a href="admin_inquiries.php" class="nav-link <?= $current_page === 'admin_inquiries.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope icon"></i> Inquiries
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <p class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></p>
                <p class="user-email"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@gmail.com') ?></p>
            </div>
        </div>
        <a href="admin_logout.php" class="logout-link"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
    </div>
</aside> 