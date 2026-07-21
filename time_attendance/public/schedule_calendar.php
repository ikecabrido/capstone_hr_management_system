<?php
/**
 * Schedule Calendar - Employee Schedule Management
 * Displays employee schedules in calendar format with timeline editor
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Employee.php";
require_once "../app/helpers/Helper.php";
require_once "../app/helpers/AuditLog.php";
require_once "../app/core/Session.php";

Session::start();

// Check if user is authenticated - try global session first, then time_attendance session
$authenticated = false;
$role = null;

// Check global login session
if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $authenticated = true;
    $role = $_SESSION['user']['role'];
} else if (AuthController::isAuthenticated()) {
    // Fallback to time_attendance auth check
    $authenticated = true;
    $role = AuthController::getCurrentRole();
}

if (!$authenticated) {
    header("Location: ../../login_form.php");
    exit;
}

// Only 'time' role can access this page
if ($role !== 'time') {
    header("Location: employee_dashboard.php");
    exit;
}

$current_page = 'schedule_calendar.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Calendar - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../assets/adminlte-overrides.css">
    <link rel="stylesheet" href="../../payroll/custom.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Calendar Schedule CSS -->
    <link rel="stylesheet" href="../app/css/calendar_schedule.css">
<style>
        html, body {
            overflow-x: hidden !important;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .page-title i {
            font-size: 36px;
            opacity: 0.95;
        }
        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .breadcrumb-nav {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }
        .breadcrumb-nav a {
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            margin: 0 5px;
        }
        .breadcrumb-nav a:hover {
            text-decoration: underline;
        }
        .page-header {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 28px;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.15);
        }
        body.dark-mode .page-header {
            background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .calendar-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            padding: 0;
            margin-bottom: 30px;
            overflow: hidden;
        }
        body.dark-mode .calendar-container {
            background: #232323 !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3) !important;
            border-color: rgba(255,255,255,0.08) !important;
        }
        .calendar-header {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            padding: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .calendar-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .calendar-header i {
            font-size: 1.8rem;
        }
        .calendar-body {
            padding: 28px;
        }
        .tab-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            padding: 0;
            background: transparent;
        }
        .tab-btn {
            padding: 13px 24px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tab-btn i {
            font-size: 17px;
        }
        .tab-btn:hover {
            background: #e8f1ff;
            color: #003d82;
            border-color: #003d82;
            transform: translateY(-1px);
            text-decoration: none;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
            transform: translateY(-2px);
        }
        body.dark-mode .tab-btn {
            background: #232323 !important;
            color: #e0e0e0 !important;
            border-color: rgba(255,255,255,0.12) !important;
        }
        body.dark-mode .tab-btn:hover {
            background: #2f2f2f !important;
            color: #e0e0e0 !important;
            border-color: rgba(255,255,255,0.2) !important;
        }
        body.dark-mode .tab-btn.active {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%) !important;
        }
        /* Ensure Day View displays at full width */
        #calendar-section {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        #day-timeline-container {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        #day-timeline-canvas {
            max-width: 100% !important;
            width: auto !important;
        }
        .tab-pane {
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .tab-content {
            width: 100% !important;
            box-sizing: border-box !important;
        }
        /* Schedule Calendar specific layout matching dashboard */
        .main-content {
            background-color: transparent !important;
            width: auto !important;
            margin: 0 0 0 250px !important;
            min-height: calc(100vh - 60px) !important;
            padding: 28px 28px !important;
            box-sizing: border-box !important;
        }
        body.dark-mode .main-content {
            background-color: transparent !important;
        }
        .content-wrapper {
            width: auto !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            background: transparent !important;
        }
        /* Ensure page looks consistent with dashboard */
        .page-header {
            color: #ffffff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>

    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div class="page-title">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule Calendar</span>
                </div>
            </div>

            <div class="calendar-container glass-panel">
                <div class="calendar-body">
                    <!-- Calendar Component -->
                    <?php include '../app/components/calendar_schedule.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <!-- Calendar Schedule JS -->
    <script src="../app/js/calendar_schedule.js"></script>
    <script>
        // Show preloader on navigation and hide it after page load
        function hidePreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.visibility = 'hidden';
            }
        }

        function showPreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'flex';
                preloader.style.visibility = 'visible';
            }
        }

        window.addEventListener('load', hidePreloader);

        document.addEventListener('DOMContentLoaded', function() {
            hidePreloader();
            const navLinks = document.querySelectorAll('a');

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    if (href && !href.includes('logout') && !href.startsWith('javascript') && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                        showPreloader();
                    }
                });
            });
        });
    </script>
</body>
</html>
