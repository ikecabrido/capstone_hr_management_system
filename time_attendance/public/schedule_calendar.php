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
    <link rel="stylesheet" href="../assets/style.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            flex: 1;
            padding: 30px 20px;
            transition: margin-left 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .main-content.sidebar-collapsed {
            margin-left: 0;
        }
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            margin-bottom: 35px;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
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
        .calendar-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            padding: 0;
            margin-bottom: 30px;
            overflow: hidden;
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
    </style>
</head>
<body>
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
                <div class="page-subtitle">Create and manage employee schedules across all dates</div>
                <div class="breadcrumb-nav">
                    <a href="dashboard.php">Dashboard</a> / Calendar
                </div>
            </div>

            <div class="calendar-container">
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
        // Show preloader on navigation
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.querySelector('.preloader');
            
            // Hide preloader on initial load
            setTimeout(() => {
                if (preloader) {
                    preloader.style.display = 'none';
                }
            }, 3000);
            
            const navLinks = document.querySelectorAll('a');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && !href.includes('logout') && !href.startsWith('javascript') && !href.startsWith('#')) {
                        if (preloader) {
                            preloader.style.display = 'flex';
                            setTimeout(() => {
                                preloader.style.display = 'none';
                            }, 3000);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
