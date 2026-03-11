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
$current_role = $_SESSION['role'] ?? 'EMPLOYEE';
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
        position: relative;
        overflow: hidden;
    }

    .brand-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .brand-link:hover {
        background: rgba(0, 0, 0, 0.25);
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .brand-link:hover::before {
        transform: translateX(100%);
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
    }

    .nav-link > p {
        margin: 0;
        flex: 1;
    }

    .nav-header {
        padding: 10px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.6);
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .badge {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
        margin-left: auto;
        flex-shrink: 0;
    }

    .badge-info {
        background: #3498db;
        color: white;
    }

    .main-content {
        margin-left: 250px;
        margin-top: 60px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 60px);
    }

    /* Dark Mode Styles */
    body.dark-mode {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }

    body.dark-mode .main-header.navbar {
        background: linear-gradient(135deg, #0f3a6a 0%, #1a4f8f 100%);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    }

    body.dark-mode .main-sidebar {
        background: linear-gradient(135deg, #0f3a6a 0%, #1a4f8f 100%);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
    }

    body.dark-mode .brand-link {
        background: rgba(0, 0, 0, 0.25);
        border-bottom-color: rgba(0, 0, 0, 0.3);
    }

    body.dark-mode .user-panel {
        border-bottom-color: rgba(0, 0, 0, 0.3);
    }

    body.dark-mode .nav-link {
        color: rgba(255, 255, 255, 0.7);
    }

    body.dark-mode .nav-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }

    body.dark-mode .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        border-left-color: #ffffff;
        color: #ffffff;
    }

    body.dark-mode .nav-sidebar .nav-header {
        color: rgba(255, 255, 255, 0.5);
    }

    body.dark-mode .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }

    body.dark-mode .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
    }

    body.dark-mode .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Sidebar collapse animation */
    .main-sidebar {
        transition: width 0.3s ease, margin-left 0.3s ease;
    }

    .main-sidebar.collapsed {
        width: 0;
        overflow: hidden;
    }

    .main-header.navbar {
        transition: left 0.3s ease;
    }

    .main-header.navbar.sidebar-collapsed {
        left: 0;
    }

    .main-content {
        transition: margin-left 0.3s ease, margin-top 0.3s ease;
    }

    .main-content.sidebar-collapsed {
        margin-left: 0;
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
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo basename($_SERVER['PHP_SELF']) === 'employee_dashboard.php' ? 'employee_dashboard.php' : 'dashboard.php'; ?>" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Live Clock -->
        <li class="nav-item">
            <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <!-- Fullscreen Toggle -->
        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0);" role="button" title="Toggle Fullscreen" style="font-size: 16px;">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

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
    <a href="<?php echo basename($_SERVER['PHP_SELF']) === 'employee_dashboard.php' ? 'employee_dashboard.php' : 'dashboard.php'; ?>" class="brand-link">
        <img src="../bcp-logo2.png" alt="BCP Logo" class="brand-image" />
        <span class="brand-text">BCP Bulacan</span>
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
                    <?php if ($current_role === 'HR_ADMIN' || $current_role === 'SYSTEM_ADMIN'): ?>
                        <a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    <?php else: ?>
                        <a href="employee_dashboard.php" class="nav-link <?php echo $current_page === 'employee_dashboard.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>My Dashboard</p>
                        </a>
                    <?php endif; ?>
                </li>

                <!-- QR & Attendance Section (HR Only) -->
                <?php if ($current_role === 'HR_ADMIN' || $current_role === 'SYSTEM_ADMIN'): ?>
                    <li class="nav-item">
                        <a href="qr_display_kiosk.php" class="nav-link <?php echo $current_page === 'qr_display_kiosk.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-qrcode"></i>
                            <p>QR Kiosk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="approve_attendance.php" class="nav-link <?php echo $current_page === 'approve_attendance.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Approve Manual Time</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Calendar/Attendance -->
                <?php if (!($current_role === 'HR_ADMIN' || $current_role === 'SYSTEM_ADMIN')): ?>
                    <li class="nav-item">
                        <a href="calendar.php" class="nav-link <?php echo $current_page === 'calendar.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Calendar View</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Shift Management (HR Only) -->
                <?php if ($current_role === 'HR_ADMIN' || $current_role === 'SYSTEM_ADMIN'): ?>
                    <li class="nav-item">
                        <a href="shifts.php" class="nav-link <?php echo $current_page === 'shifts.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>Manage Shifts</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Leave Management Section -->
                <?php if ($current_role === 'HR_ADMIN' || $current_role === 'SYSTEM_ADMIN'): ?>
                    <li class="nav-item">
                        <a href="leave_approvals.php" class="nav-link <?php echo $current_page === 'leave_approvals.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Approve Leave Requests</p>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="leave_request.php" class="nav-link <?php echo $current_page === 'leave_request.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Submit Request</p>
                        </a>
                    </li>
                    <?php if ($current_role === 'DEPARTMENT_HEAD'): ?>
                        <li class="nav-item">
                            <a href="leave_approvals.php" class="nav-link <?php echo $current_page === 'leave_approvals.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Approve Requests</p>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Logout -->
                <li class="nav-header">SETTINGS</li>
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
        const navbar = document.querySelector('.main-header.navbar');
        const mainContent = document.querySelector('.main-content');
        
        // For mobile
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
        } else {
            // For desktop - collapse/expand animation
            sidebar.classList.toggle('collapsed');
            navbar.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
        }
    }

    // Close sidebar when clicking on a link (mobile)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && e.target.closest('.nav-link') !== link) {
                return;
            }
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('mainSidebar');
                sidebar.classList.remove('open');
            }
        });
    });

    // Live Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElement = document.getElementById('clock');
        if (clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }

    updateClock();
    setInterval(updateClock, 1000);

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

    // Handle responsive on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('mainSidebar');
            sidebar.classList.remove('open');
        }
    });
</script>
