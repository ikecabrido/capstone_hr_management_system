<?php
/**
 * Sidebar Navigation Component
 * Displays role-based navigation menu with AdminLTE-inspired design
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page and role
$current_page = $current_page ?? basename($_SERVER['PHP_SELF']);
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'EMPLOYEE';
?>

<style>
    /* AdminLTE Sidebar Styling - Clean and Professional */
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .main-header.navbar {
        background: linear-gradient(135deg, #1e5ba8 0%, #2575d0 100%);
        color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        position: fixed;
        top: 0;
        left: 250px;
        right: 0;
        z-index: 1030;
        height: 60px;
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        padding-left: 20px;
    }

    .navbar-nav {
        display: flex;
        flex-direction: row;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
        gap: 10px;
    }

    .navbar-nav .nav-item {
        margin: 0;
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        padding: 8px 15px;
        transition: color 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .navbar-nav .nav-link:hover {
        color: #ffffff;
    }

    .navbar-nav.ml-auto {
        margin-left: auto !important;
        padding-right: 20px;
    }

    .main-sidebar {
        width: 250px;
        background: linear-gradient(135deg, #1e5ba8 0%, #2575d0 100%);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        z-index: 900;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
    }

    .brand-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px 15px;
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 3px solid rgba(255, 255, 255, 0.2);
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
        gap: 12px;
    }

    .brand-link:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    .brand-image {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 4px;
    }

    .brand-text {
        font-size: 16px;
        font-weight: 600;
        color: #ecf0f1;
        line-height: 1.2;
    }

    .sidebar {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .user-panel {
        padding: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-panel .image {
        width: 40px;
        height: 40px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: bold;
        flex-shrink: 0;
    }

    .user-panel .info {
        flex: 1;
        min-width: 0;
    }

    .user-panel .info a {
        color: #ecf0f1;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: block;
        word-break: break-word;
        transition: color 0.3s ease;
    }

    .user-panel .info a:hover {
        color: #3498db;
    }

    .nav-sidebar {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-sidebar .nav-item {
        display: block;
    }

    .nav-sidebar .nav-link {
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-size: 13px;
    }

    .nav-sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #ffffff;
        color: #ffffff;
    }

    .nav-sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        border-left-color: #ffffff;
        color: #ffffff;
        font-weight: 600;
    }

    .nav-icon {
        font-size: 14px;
        width: 18px;
        text-align: center;
        flex-shrink: 0;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 10 !important;
    }

    .nav-link > p {
        margin: 0;
        flex: 1;
    }

    .main-content {
        margin-left: 250px;
        margin-top: 60px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 60px);
    }

    @media (max-width: 768px) {
        .main-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            top: 60px;
            height: calc(100vh - 60px);
        }

        .main-sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }
    }
</style>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0);" onclick="toggleSidebar()" role="button" title="Toggle Menu" style="font-size: 20px;">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Dark Mode Toggle -->
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0);" id="darkToggle" role="button" title="Toggle Dark Mode" style="font-size: 16px;">
                <i class="fas fa-moon" id="themeIcon"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar" id="mainSidebar">
    <!-- Brand Logo -->
    <a href="employee_dashboard.php" class="brand-link">
        <img src="../bcp-logo2.png" alt="BCP Logo" class="brand-image" />
        <span class="brand-text">BCP HR System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="image">
                <i class="fas fa-user-circle" style="font-size: 20px;"></i>
            </div>
            <div class="info">
                <a href="#">
                    <?= htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['email'] ?? 'User'); ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav>
            <ul class="nav-sidebar">
                <!-- Dashboard Section -->
                <li class="nav-item">
                    <a href="employee_dashboard.php" class="nav-link <?php echo $current_page === 'employee_dashboard.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>My Dashboard</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-header" style="margin-top: auto; padding-bottom: 10px;">SETTINGS</li>
                <li class="nav-item">
                    <a href="../../logout.php" class="nav-link" style="color: #e74c3c;">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    // Toggle Sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('mainSidebar');
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
        }
    }

    // Dark Mode Toggle
    const darkToggle = document.getElementById('darkToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    if (darkToggle) {
        darkToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            
            if (isDarkMode) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });
    }

    // Load dark mode preference
    window.addEventListener('load', function() {
        const darkMode = localStorage.getItem('darkMode') === 'true';
        if (darkMode) {
            document.body.classList.add('dark-mode');
            if (themeIcon) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        }
    });
</script>
