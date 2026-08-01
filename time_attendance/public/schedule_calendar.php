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
<?php
$current_page = 'schedule_calendar.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
$page_title = 'Schedule Calendar';
$page_subtitle = 'Employee schedule calendar and timeline';
$page_head_extra = <<<HTML
<link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/dashboard.css">
<link rel="stylesheet" href="../assets/adminlte-overrides.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Calendar Schedule CSS -->
<link rel="stylesheet" href="../app/css/calendar_schedule.css">
<link rel="stylesheet" href="../assets/hr-template.css">
<style>
        html, body {
            overflow-x: hidden !important;
        }

        /* AdminLTE Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 99999;
        }

        .preloader.flex-column {
            flex-direction: column;
        }

        .preloader.justify-content-center {
            justify-content: center;
        }

        .preloader.align-items-center {
            align-items: center;
        }

        .preloader img {
            max-width: 100px;
            height: auto;
            display: block;
        }

        .animation__wobble {
            animation: wobble 2.5s infinite ease-in-out;
        }

        @keyframes wobble {
            0% {
                transform: translateX(0);
            }
            15% {
                transform: translateX(-5px) rotate(-5deg);
            }
            30% {
                transform: translateX(3px) rotate(3deg);
            }
            45% {
                transform: translateX(-3px) rotate(-3deg);
            }
            60% {
                transform: translateX(2px) rotate(2deg);
            }
            75% {
                transform: translateX(-1px) rotate(-1deg);
            }
            100% {
                transform: translateX(0);
            }
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
            margin: 0 0 0 0px !important;
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
    </style>
HTML;
?>
<?php
require_once __DIR__ . '/../layout/page_start.php';
require_once __DIR__ . '/../layout/sidebar.php';
$page_title = 'Schedule Calendar';
$page_subtitle = 'View and manage employee schedules';
$page_icon = 'fa-calendar';
require_once __DIR__ . '/../layout/content_header.php';
?>


    <div class="main-content">
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
    <script>
      window.preloaderHold = true;
      // The page stays in hold until the schedule calendar finishes rendering in calendar_schedule.js
    </script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <!-- Calendar Schedule JS -->
    <script src="../app/js/calendar_schedule.js"></script>

<?php require_once __DIR__ . '/../layout/content_footer.php'; ?>
<?php require_once __DIR__ . '/../layout/page_end.php'; ?>
