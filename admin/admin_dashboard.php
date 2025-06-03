<?php
session_start();
require_once '../includes/config.php';

// Function to format time elapsed
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return "just now";
            }
            return $diff->i . " minute" . ($diff->i > 1 ? "s" : "") . " ago";
        }
        return $diff->h . " hour" . ($diff->h > 1 ? "s" : "") . " ago";
    }
    if ($diff->d < 7) {
        return $diff->d . " day" . ($diff->d > 1 ? "s" : "") . " ago";
    }
    return date('M d, Y', strtotime($datetime));
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Get total users count
$users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total'];

// Get total properties count
$properties_query = "SELECT COUNT(*) as total FROM properties";
$properties_result = $conn->query($properties_query);
$total_properties = $properties_result->fetch_assoc()['total'];

// Get total inquiries count
$inquiries_query = "SELECT COUNT(*) as total FROM inquiries";
$inquiries_result = $conn->query($inquiries_query);
$total_inquiries = $inquiries_result->fetch_assoc()['total'];

// Get recent properties
$recent_properties_query = "SELECT * FROM properties ORDER BY created_at DESC LIMIT 5";
$recent_properties = $conn->query($recent_properties_query);

// Get recent inquiries
$recent_inquiries_query = "SELECT i.*, p.title as property_title 
                          FROM inquiries i 
                          LEFT JOIN properties p ON i.property_id = p.id 
                          ORDER BY i.created_at DESC LIMIT 5";
$recent_inquiries = $conn->query($recent_inquiries_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Property Builder</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="style.css"> <!-- Link to your main stylesheet -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>


        <main class="admin-main-content">
             <div class="admin-header-controls">
                <h2>Good Morning, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?> <i class="fas fa-sun"></i></h2>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Welcome/Banner Card (Placeholder) -->
                <div class="dashboard-card welcome-card">
                </div>

                <!-- Statistics Cards -->
                <div class="dashboard-card stat-card">
                    <div class="stat-padding">
                        <div class="stat-header">
                            <i class="fas fa-dollar-sign icon"></i>
                            <h4>Total Revenue</h4>
                            <button class="options-button"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                        <p class="stat-value">$<?= number_format(783156) ?></p> <!-- Using dummy data -->
                        <p class="stat-change"><i class="fas fa-arrow-up text-success"></i> +28% <span class="text-muted">From the last week</span></p>
                    </div>
                </div>

                 <div class="dashboard-card stat-card">
                    <div class="stat-padding">
                        <div class="stat-header">
                            <i class="fas fa-tools icon"></i>
                            <h4>Maintenance Cost</h4>
                            <button class="options-button"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                        <p class="stat-value">$<?= number_format(582473) ?></p> <!-- Using dummy data -->
                        <p class="stat-change"><i class="fas fa-arrow-up text-success"></i> +15% <span class="text-muted">From the last week</span></p>
                    </div>
                </div>

                 

                <!-- Recent Properties -->
                <div class="dashboard-card recent-properties-card">
                    <div class="card-header">
                         <h4>Property List</h4>
                         <a href="admin_properties.php">See All Listing</a>
                    </div>
                    <div class="card-body">
                        <div class="property-list">
                            <?php while($property = $recent_properties->fetch_assoc()): ?>
                            <div class="property-item">
                                 <img src="../<?= htmlspecialchars($property['image']) ?>" alt="Property Image" class="property-thumb">
                                 <div class="property-info">
                                     <p class="property-title"><?= htmlspecialchars($property['title']) ?></p>
                                     <p class="property-location"><?= htmlspecialchars($property['location']) ?></p>
                                     <p class="property-price">₱<?= number_format($property['price']) ?></p>
                                 </div>
                            </div>
                             <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Table -->
                <div class="dashboard-card sales-report-card">
                    <div class="card-header">
                        <h4>Recent Activity</h4>
                    </div>
                    <div class="card-body">
                        <table class="sales-table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Property</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get recent activities (properties and inquiries)
                                $recent_activities_query = "
                                    (SELECT 
                                        'property' as type,
                                        p.title as title,
                                        p.created_at as date,
                                        'New Property' as activity,
                                        'Added' as status
                                    FROM properties p)
                                    UNION ALL
                                    (SELECT 
                                        'inquiry' as type,
                                        p.title as title,
                                        i.created_at as date,
                                        'New Inquiry' as activity,
                                        i.status
                                    FROM inquiries i
                                    LEFT JOIN properties p ON i.property_id = p.id)
                                    ORDER BY date DESC
                                    LIMIT 10";
                                
                                $recent_activities = $conn->query($recent_activities_query);
                                
                                while($activity = $recent_activities->fetch_assoc()):
                                    $time_ago = time_elapsed_string($activity['date']);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($activity['activity']) ?></td>
                                    <td><?= htmlspecialchars($activity['title']) ?></td>
                                    <td><?= ucfirst($activity['type']) ?></td>
                                    <td><?= $time_ago ?></td>
                                    <td><span class="status-badge <?= $activity['status'] ?>"><?= ucfirst($activity['status']) ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add your custom admin JS if any -->
</body>
</html>
