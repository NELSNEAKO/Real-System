<aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="admin_dashboard.php">Property Builder</a>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="admin_dashboard.php" class="nav-link active"><i class="fas fa-home icon"></i> Dashboard</a></li>
                    <!-- <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user-shield icon"></i> Admin</a></li> -->
                     <!-- <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user-tie icon"></i> Sub Admin</a></li> -->
                    <li class="nav-item"><a href="admin_users.php" class="nav-link"><i class="fas fa-users icon"></i> Users</a></li>
                </ul>
                 <h4 class="nav-section-title">DATA</h4>
                 <ul class="nav-list">
                    <li class="nav-item"><a href="admin_properties.php" class="nav-link"><i class="fas fa-building icon"></i> Properties</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-list icon"></i> List</a></li>
                     <li class="nav-item"><a href="admin_inquiries.php" class="nav-link"><i class="fas fa-envelope icon"></i> Inquiries</a></li>
                 </ul>
                
            </nav>
             <div class="sidebar-footer">
                 <div class="user-info">
                     <img src="#" alt="Admin Photo" class="user-avatar">
                     <div class="user-details">
                         <p class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></p>
                         <p class="user-email">admin@example.com</p>
                     </div>
                 </div>
                 <a href="admin_logout.php" class="logout-link"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
             </div>
        </aside>