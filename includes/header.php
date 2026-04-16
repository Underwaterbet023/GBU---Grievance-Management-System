<?php
// includes/header.php
require_once 'auth.php';
requireLogin();

// Safely pull session vars
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'Student';
$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBU Grievance Management System</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Main Style -->
    <link href="css/style.css" rel="stylesheet">
    
    <!-- Core Scripts (Loaded early to support inline scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Apply theme BEFORE render to avoid flash
        (function() {
            const saved = localStorage.getItem('gbu-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved);
        })();

        $(document).ready(function() {
            // Sidebar Toggle for Mobile
            $('#sidebarToggle').on('click', function(e) {
                e.stopPropagation();
                $('.app-sidebar').toggleClass('sidebar-open');
                $('body').toggleClass('sidebar-active');
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($('.app-sidebar').hasClass('sidebar-open') && 
                    !$(e.target).closest('.app-sidebar').length && 
                    !$(e.target).closest('#sidebarToggle').length) {
                    $('.app-sidebar').removeClass('sidebar-open');
                    $('body').removeClass('sidebar-active');
                }
            });

            // Close sidebar on link click (mobile)
            $('.nav-menu a').on('click', function() {
                if (window.innerWidth < 1024) {
                    $('.app-sidebar').removeClass('sidebar-open');
                    $('body').removeClass('sidebar-active');
                }
            });
        });
    </script>
</head>
<body>

<div class="app-container">
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <a href="<?= isAdmin() ? 'admin_dashboard.php' : 'student_dashboard.php' ?>" class="app-sidebar-logo text-decoration-none">
            <img src="https://www.gbu.ac.in/Content/img/logo_gbu.png" alt="GBU Logo" class="gbu-logo-img" style="width: 42px; height: 42px; filter: drop-shadow(0 0 5px rgba(255,255,255,0.2));">
            <div class="logo-text">
                <span class="logo-title">Gautam Buddha</span>
                <span class="logo-subtitle">University</span>
            </div>
        </a>
        
        <nav class="nav-menu flex-grow-1">
            <?php if ($userRole === 'Student'): ?>
                <a href="student_dashboard.php" class="<?= $currentPage == 'student_dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> <span>Home</span>
                </a>
                <a href="submit_complaint.php" class="<?= $currentPage == 'submit_complaint.php' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> <span>Submit Complaint</span>
                </a>
                <a href="my_complaints.php" class="<?= $currentPage == 'my_complaints.php' ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i> <span>My Complaints</span>
                </a>
                <a href="track_complaint.php" class="<?= $currentPage == 'track_complaint.php' ? 'active' : '' ?>">
                    <i class="fas fa-map-marker-alt"></i> <span>Track Complaint</span>
                </a>
            <?php else: ?>
                <a href="admin_dashboard.php" class="<?= $currentPage == 'admin_dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> <span>Home</span>
                </a>
                <a href="manage_complaints.php" class="<?= $currentPage == 'manage_complaints.php' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> <span>Complaints</span>
                </a>
                <a href="users.php" class="<?= $currentPage == 'users.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> <span>Users</span>
                </a>
                <a href="reports.php" class="<?= $currentPage == 'reports.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> <span>Reports</span>
                </a>
                <a href="escalations.php" class="<?= $currentPage == 'escalations.php' ? 'active' : '' ?>">
                    <i class="fas fa-exclamation-triangle"></i> <span>Escalations</span>
                </a>
            <?php endif; ?>
        </nav>
        
        <div class="nav-menu sidebar-bottom">
            <a href="settings.php" class="<?= $currentPage == 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
            <a href="api/logout.php" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <!-- Top Nav -->
        <header class="app-header">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" class="btn btn-link text-white d-lg-none me-3 p-0" title="Toggle Sidebar">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <div>
                    <h1 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Dashboard'); ?></h1>
                    <p class="text-muted x-small tracking-widest text-uppercase mt-1 d-none d-sm-block">Gautam Buddha University &mdash; Grievance Management System</p>
                </div>
            </div>
            
            <div class="top-bar-right">
                <div class="search-bar-container d-none d-lg-block">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="search-bar" placeholder="Search complaint ID or subject...">
                </div>

                <!-- Light/Dark Mode Toggle -->
                <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle Light/Dark Mode">
                    <i class="fas fa-sun" id="themeIcon"></i>
                </button>
                
                <div class="dropdown">
                    <div class="profile-dropdown dropdown-toggle border-0" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=6366f1&color=fff&bold=true" alt="User">
                        <div class="profile-info d-none d-md-block">
                            <h6><?= htmlspecialchars($userName) ?></h6>
                            <span class="text-uppercase x-small font-weight-bold"><?= htmlspecialchars(getDisplayRole($userRole)) ?></span>
                        </div>
                        <i class="fas fa-chevron-down x-small text-muted ms-1"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-white border-opacity-10 mt-2">
                        <li><h6 class="dropdown-header x-small text-uppercase opacity-50">Account Control</h6></li>
                        <li><a class="dropdown-item x-small fw-bold" href="settings.php"><i class="fas fa-user-cog me-2"></i>Edit Profile</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a class="dropdown-item x-small fw-bold" href="users.php"><i class="fas fa-users-cog me-2"></i>Manage IDs</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider border-white border-opacity-10"></li>
                        <li><a class="dropdown-item x-small fw-bold text-danger" href="api/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Content Starts Here -->
