<?php
session_start();    
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle inquiry status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $inquiry_id = (int)$_POST['inquiry_id'];
        $status = sanitizeInput($_POST['status']);
        
        $sql = "UPDATE inquiries SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $inquiry_id);
        
        if ($stmt->execute()) {
            $success_message = "Inquiry status updated successfully!";
        } else {
            $error_message = "Failed to update inquiry status.";
        }
    } elseif (isset($_POST['delete_inquiry'])) {
        $inquiry_id = (int)$_POST['inquiry_id'];
        
        $sql = "DELETE FROM inquiries WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $inquiry_id);
        
        if ($stmt->execute()) {
            $success_message = "Inquiry deleted successfully!";
        } else {
            $error_message = "Failed to delete inquiry.";
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$date_filter = isset($_GET['date']) ? sanitizeInput($_GET['date']) : 'all';

// Build the query with filters
$sql = "SELECT i.*, p.title as property_title, u.username as user_name, u.email as user_email 
        FROM inquiries i 
        LEFT JOIN properties p ON i.property_id = p.id 
        LEFT JOIN users u ON i.user_id = u.id 
        WHERE 1=1";

if ($status_filter !== 'all') {
    $sql .= " AND i.status = ?";
}
if ($date_filter !== 'all') {
    switch ($date_filter) {
        case 'today':
            $sql .= " AND DATE(i.created_at) = CURDATE()";
            break;
        case 'week':
            $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            break;
        case 'month':
            $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
    }
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $conn->prepare($sql);
if ($status_filter !== 'all') {
    $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$inquiries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get counts for each status
$status_counts = [
    'new' => 0,
    'read' => 0,
    'replied' => 0
];

$count_sql = "SELECT status, COUNT(*) as count FROM inquiries GROUP BY status";
$count_result = $conn->query($count_sql);
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="admin-main-content">
            <div class="admin-header-controls">
                <h2>Manage Inquiries</h2>
                <div class="controls-right">
                    <div class="filter-controls">
                        <select id="statusFilter" class="form-select" onchange="applyFilters()">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                            <option value="new" <?= $status_filter === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Read</option>
                            <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Replied</option>
                        </select>
                        <select id="dateFilter" class="form-select" onchange="applyFilters()">
                            <option value="all" <?= $date_filter === 'all' ? 'selected' : '' ?>>All Time</option>
                            <option value="today" <?= $date_filter === 'today' ? 'selected' : '' ?>>Today</option>
                            <option value="week" <?= $date_filter === 'week' ? 'selected' : '' ?>>This Week</option>
                            <option value="month" <?= $date_filter === 'month' ? 'selected' : '' ?>>This Month</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <div class="dashboard-grid">
                <!-- Status Summary Cards -->
                <div class="dashboard-card stat-card">
                    <div class="stat-padding">
                        <div class="stat-header">
                            <i class="fas fa-envelope icon"></i>
                            <h4>New Inquiries</h4>
                        </div>
                        <p class="stat-value"><?= $status_counts['new'] ?></p>
                        <p class="stat-change">Requires attention</p>
                    </div>
                </div>

                <div class="dashboard-card stat-card">
                    <div class="stat-padding">
                        <div class="stat-header">
                            <i class="fas fa-eye icon"></i>
                            <h4>Read Inquiries</h4>
                        </div>
                        <p class="stat-value"><?= $status_counts['read'] ?></p>
                        <p class="stat-change">In progress</p>
                    </div>
                </div>

                <div class="dashboard-card stat-card">
                    <div class="stat-padding">
                        <div class="stat-header">
                            <i class="fas fa-reply icon"></i>
                            <h4>Replied Inquiries</h4>
                        </div>
                        <p class="stat-value"><?= $status_counts['replied'] ?></p>
                        <p class="stat-change">Completed</p>
                    </div>
                </div>

                <!-- Inquiries List -->
                <div class="dashboard-card inquiries-card">
                    <div class="card-header">
                        <h4>Inquiries List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Property</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inquiries as $inquiry): ?>
                                        <tr>
                                            <td><?= $inquiry['id'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($inquiry['user_name'] ?? 'Guest') ?>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($inquiry['user_email'] ?? $inquiry['email']) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($inquiry['property_title']) ?></td>
                                            <td>
                                                <div class="message-preview">
                                                    <?= htmlspecialchars(substr($inquiry['message'], 0, 50)) ?>...
                                                </div>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($inquiry['created_at'])) ?></td>
                                            <td>
                                                <span class="status-badge <?= $inquiry['status'] ?>">
                                                    <?= ucfirst($inquiry['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn btn-sm btn-info view-inquiry" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#inquiryModal"
                                                            data-inquiry-id="<?= $inquiry['id'] ?>"
                                                            data-inquiry-user="<?= htmlspecialchars($inquiry['user_name'] ?? 'Guest') ?>"
                                                            data-inquiry-email="<?= htmlspecialchars($inquiry['user_email'] ?? $inquiry['email']) ?>"
                                                            data-inquiry-phone="<?= htmlspecialchars($inquiry['phone'] ?? 'N/A') ?>"
                                                            data-inquiry-property="<?= htmlspecialchars($inquiry['property_title']) ?>"
                                                            data-inquiry-message="<?= htmlspecialchars($inquiry['message']) ?>"
                                                            data-inquiry-date="<?= date('M d, Y H:i', strtotime($inquiry['created_at'])) ?>"
                                                            data-inquiry-status="<?= $inquiry['status'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                                        <input type="hidden" name="inquiry_id" value="<?= $inquiry['id'] ?>">
                                                        <button type="submit" name="delete_inquiry" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Single Modal for All Inquiries -->
    <div class="modal fade" id="inquiryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inquiry Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="inquiry-details">
                        <p><strong>User:</strong> <span id="modal-user"></span></p>
                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                        <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
                        <p><strong>Property:</strong> <span id="modal-property"></span></p>
                        <p><strong>Message:</strong></p>
                        <div class="message-content" id="modal-message"></div>
                        <p><strong>Date:</strong> <span id="modal-date"></span></p>
                    </div>
                    <form method="POST" class="mt-3" id="status-form">
                        <input type="hidden" name="inquiry_id" id="modal-inquiry-id">
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select" id="modal-status">
                                <option value="new">New</option>
                                <option value="read">Read</option>
                                <option value="replied">Replied</option>
                            </select>
                        </div>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            window.location.href = `admin_inquiries.php?status=${status}&date=${date}`;
        }

        // Handle modal data population
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-inquiry');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const inquiryId = this.getAttribute('data-inquiry-id');
                    const user = this.getAttribute('data-inquiry-user');
                    const email = this.getAttribute('data-inquiry-email');
                    const phone = this.getAttribute('data-inquiry-phone');
                    const property = this.getAttribute('data-inquiry-property');
                    const message = this.getAttribute('data-inquiry-message');
                    const date = this.getAttribute('data-inquiry-date');
                    const status = this.getAttribute('data-inquiry-status');

                    // Populate modal fields
                    document.getElementById('modal-inquiry-id').value = inquiryId;
                    document.getElementById('modal-user').textContent = user;
                    document.getElementById('modal-email').textContent = email;
                    document.getElementById('modal-phone').textContent = phone;
                    document.getElementById('modal-property').textContent = property;
                    document.getElementById('modal-message').textContent = message;
                    document.getElementById('modal-date').textContent = date;
                    document.getElementById('modal-status').value = status;
                });
            });

            // Handle status form submission
            document.getElementById('status-form').addEventListener('submit', function(e) {
                e.preventDefault();
                this.submit();
            });

            // Add change event listener to status select
            document.getElementById('modal-status').addEventListener('change', function() {
                document.getElementById('status-form').submit();
            });
        });
    </script>
</body>
</html> 