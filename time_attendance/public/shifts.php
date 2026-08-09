<?php
// 15. Beginning of shifts.php end-of-file checkpoint will be logged at the end of the file
/**
 * Shift Management Page
 * HR-only page for managing shifts and employee assignments
 */

// Start session and check authentication
// Ensure required app bootstrap is available so runtime variables used
// later (like $db, $shifts, $allAssignments) are defined.
// Debug helper for writing diagnostics to time_attendance/debug_shifts.log
require_once __DIR__ . '/../debug_helpers.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../app/controllers/ShiftController.php';

// 1. First line of PHP execution
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 1 First line of PHP execution');

Session::start();
// 2. After session start
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 2 After Session::start');

// Redirect to login if not authenticated (same pattern as other public pages)
// 3. Authentication check (log result for debugging)
$authOk = AuthController::isAuthenticated();
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 3 After AuthController::isAuthenticated isAuthenticated=' . ($authOk ? '1' : '0'));
if (!$authOk) {
    if (function_exists('shiftDebug')) shiftDebug('[DIAG] 3a Not authenticated: redirecting to login');
    header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
    exit;
}

// Initialize DB and controllers if not already present
$database = $database ?? Database::getInstance();
// 4. Before database initialization
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 4 Before Database::getInstance');
$db = $db ?? $database->getConnection();
// 5. After database initialization
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 5 After Database::getConnection');

// 6. Before ShiftController construction
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 6 Before ShiftController->__construct');
$shiftController = $shiftController ?? new ShiftController($db);
// 7. After ShiftController construction
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 7 After ShiftController->__construct');

// Ensure variables used later are defined to avoid runtime fatal errors
$action = $action ?? $_GET['action'] ?? $_POST['action'] ?? null;
// 8. Before getAllShifts()
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 8 Before getAllShifts()');
$shifts = $shifts ?? $shiftController->getAllShifts();
// 9. After getAllShifts()
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 9 After getAllShifts() returned count=' . count($shifts));
// 10. Before getEmployeesOnShift()
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 10 Before getEmployeesOnShift()');
$allAssignments = $allAssignments ?? $shiftController->getEmployeesOnShift(null);
// 11. After getEmployeesOnShift()
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 11 After getEmployeesOnShift() returned count=' . count($allAssignments));
$message = $message ?? null;
$error = $error ?? null;

?>
    <?php
// Get data based on action

// For edit action, get specific shift
$editShift = null;
if ($action === 'edit' && isset($_GET['shift_id'])) {
    $editShift = $shiftController->getShiftById($_GET['shift_id']);
}

?>
<?php
$current_page = 'shifts.php';
$current_role = $_SESSION['role'] ?? $_SESSION['user']['role'] ?? 'time';
$page_title = 'Shift Management';
$page_subtitle = 'Shift management and assignments';
$page_head_extra = <<<HTML
<link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/dashboard.css">
<link rel="stylesheet" href="../assets/adminlte-overrides.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/hr-template.css">
<style>
        body {
            transition: margin-left 0.3s ease;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .main-content .shift-container {
            margin-left: 0 !important;
            margin-top: 0 !important;
        }

        .shift-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body.dark-mode .shift-tabs {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .shift-tab {
            padding: 13px 24px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .shift-tab i {
            font-size: 17px;
            color: inherit !important;
        }

        body:not(.dark-mode) .shift-tabs {
            background: #ffffff !important;
            border: 1px solid #e8eef7;
        }

        body:not(.dark-mode) .shift-tab {
            background: #f8f9fa !important;
            color: #2c3e50 !important;
        }

        body:not(.dark-mode) .shift-tab:hover {
            background: #e8f1ff !important;
            color: #003d82 !important;
        }

        body:not(.dark-mode) .shift-tab.active {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%) !important;
            color: #ffffff !important;
        }

        .shift-tab:hover {
            background: #e8f1ff;
            color: #003d82;
            border-color: #003d82;
            transform: translateY(-1px);
        }

        .shift-tab.active {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
            transform: translateY(-2px);
        }

        body.dark-mode .shift-tab {
            background: #2a2a2a;
            color: #b0b0b0;
            border-color: rgba(95, 163, 255, 0.1);
        }

        body.dark-mode .shift-tab:hover {
            background: #333;
            color: #5fa3ff;
            border-color: #5fa3ff;
        }

        body.dark-mode .shift-tab.active {
            background: linear-gradient(135deg, #003d82, #005ba8);
            color: white;
            border-color: transparent;
        }

        /* Action Buttons for Modals */
        .shift-action-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body.dark-mode .shift-action-buttons {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .shift-action-buttons .btn {
            padding: 13px 24px;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 61, 130, 0.2);
        }

        .shift-action-buttons .btn i {
            font-size: 17px;
        }

        .shift-action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 61, 130, 0.4);
        }

        .shift-action-buttons .btn:active {
            transform: translateY(0);
        }

        body.dark-mode .shift-action-buttons .btn {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        body.dark-mode .shift-action-buttons .btn:hover {
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.5);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .shifts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
            margin-bottom: 35px;
        }

        .shift-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .shift-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .shift-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.05;
            transition: all 0.3s ease;
        }

        .shift-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 32px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.15);
        }

        .shift-card:hover::before {
            transform: scaleX(1);
        }

        .shift-card:hover::after {
            opacity: 0.08;
        }

        body.dark-mode .shift-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .shift-card:hover {
            box-shadow: 0 12px 32px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.2);
        }

        .shift-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .shift-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #003d82;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-card-title {
            color: #5fa3ff;
        }

        .shift-status {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            background: #d4edda;
            color: #155724;
            font-weight: 500;
        }

        .shift-status.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        body.dark-mode .shift-status {
            background: #1e5631;
            color: #c9f7d5;
        }

        body.dark-mode .shift-status.inactive {
            background: #5c2a2a;
            color: #ffd0d0;
        }

        .shift-time {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-time {
            color: #b0b0b0;
        }

        .shift-details {
            margin-bottom: 12px;
        }

        .shift-details p {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }

        body.dark-mode .shift-details p {
            color: #888;
        }

        .shift-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .shift-card-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .shift-form {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            position: relative;
            overflow: hidden;
        }

        .shift-form::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(0, 61, 130, 0.05), rgba(0, 91, 168, 0.02));
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .shift-form:hover::before {
            opacity: 1;
        }

        body.dark-mode .shift-form {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #003d82;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        body.dark-mode .form-group label {
            color: #5fa3ff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid rgba(0, 61, 130, 0.1);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            color: #333;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background-color: #2a2a2a;
            border-color: #404040;
            color: #e0e0e0;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .form-buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #003d82;
            color: white;
        }

        .btn-primary:hover {
            background: #002a5a;
        }

        .btn-secondary {
            background: #e8eef7;
            color: #003d82;
        }

        .btn-secondary:hover {
            background: #d4dff0;
        }

        body.dark-mode .btn-secondary {
            background: #2a2a2a;
            color: #5fa3ff;
        }

        body.dark-mode .btn-secondary:hover {
            background: #333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        body.dark-mode .alert-success {
            background: #1e5631;
            color: #81d97d;
            border-color: #2a7a38;
        }

        body.dark-mode .alert-danger {
            background: #5c2a2a;
            color: #f08080;
            border-color: #7a3838;
        }

        .shift-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.08;
            transition: all 0.3s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.2);
        }

        .stat-card:hover::before {
            opacity: 0.12;
        }

        .stat-card:hover::after {
            transform: scaleX(1);
        }

        body.dark-mode .stat-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .stat-card:hover {
            box-shadow: 0 15px 40px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.3);
        }

        .stat-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 900;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-number {
            background: linear-gradient(135deg, #5fa3ff, #7bb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 15px;
            color: #555;
            font-weight: 600;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-label {
            color: #a0a0a0;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e8eef7;
            margin-bottom: 30px;
        }

        body.dark-mode .table-container {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            border-color: #404040;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        body.dark-mode thead {
            background: #2a2a2a;
            border-bottom-color: #404040;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        body.dark-mode th {
            color: #e0e0e0;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #555;
        }

        body.dark-mode td {
            border-bottom-color: #404040;
            color: #b0b0b0;
        }

        body.dark-mode #overview table td:first-child,
        body.dark-mode #overview table td:nth-child(2),
        body.dark-mode #overview table td:nth-child(3) {
            color: #f1f5f9;
        }

        body.dark-mode #assignmentTable td:nth-child(2),
        body.dark-mode #assignmentTable td:nth-child(3) {
            color: #f1f5f9;
        }

        body.dark-mode #assignmentTable td:nth-child(2) span {
            background-color: #244b70 !important;
            color: #e6f4ff !important;
        }

        body.dark-mode #assignmentTable td:nth-child(6) span {
            background-color: #24543a !important;
            color: #d8ffe2 !important;
        }

        body.dark-mode #assignmentTable td:nth-child(6) span i {
            color: #b8f5ce !important;
        }

        tbody tr:hover {
            background: #f9fbfd;
        }

        body.dark-mode tbody tr:hover {
            background: #2a2a2a;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .shift-container {
                padding: 20px;
                margin-left: 0;
            }

            .page-title {
                font-size: 24px;
            }

            .shift-tabs {
                gap: 10px;
                padding: 12px;
            }

            .shift-tab {
                padding: 10px 16px;
                font-size: 12px;
            }

            .shift-action-buttons {
                gap: 8px;
                padding: 12px;
                flex-direction: column;
            }

            .shift-action-buttons .btn {
                width: 100%;
                justify-content: center;
                padding: 12px 16px;
                font-size: 14px;
            }

            .shift-form {
                padding: 20px;
            }

            .shifts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .shift-stats {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .form-buttons {
                flex-direction: column;
            }

            .form-buttons button {
                width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }

            .shift-card-actions {
                flex-direction: column;
            }
        }
    </style>
    <script src="../assets/mobile-responsive.js" defer></script>
HTML;
?>
<?php
// 12. Before page_start.php
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 12 Before page_start.php');
require_once __DIR__ . '/../layout/page_start.php';
require_once __DIR__ . '/../layout/sidebar.php';
// 13. After page_start.php
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 13 After page_start.php');
// Before the main Shift Management HTML
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 14 Before main Shift Management HTML');
$page_title = 'Shift Management';
$page_subtitle = 'Create, edit, and assign employee shifts';
$page_icon = 'fa-clock';
require_once __DIR__ . '/../layout/content_header.php';
?>
    <div class="shift-container">
        <div class="container glass-panel">
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <!-- Action Button for Assign Shift -->
        <div class="shift-action-buttons">
            <button class="btn btn-primary" onclick="openModal('generateFixedModal');">
                <i class="fas fa-calendar-plus"></i>
                Assign Shift
            </button>
        </div>

        <script>
            // Shared helpers available early in the page.
            if (typeof window.safeLower !== 'function') {
                window.safeLower = function(value) {
                    return String(value || '').toLowerCase();
                };
            }
            if (typeof window.escapeHtml !== 'function') {
                window.escapeHtml = function(value) {
                    return String(value || '').replace(/[&<>"']/g, function(match) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        }[match] || match;
                    });
                };
            }

            // Defensive minimal modal handlers: ensure buttons work even if later scripts error.
            if (typeof openModal !== 'function') {
                window.openModal = function(modalId) {
                    try {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.style.display = 'flex';
                            document.body.classList.add('modal-open');
                        }
                    } catch (e) { console.error('openModal (defensive) error', e); }
                };
            }

            // ===== Minimal Generate Fixed Schedule Modal logic =====
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                }
            }

            let fixedScheduleEmployees = [];
            let filteredFixedScheduleEmployees = [];

            function submitGenerateFixed() {
                const employeeId = document.getElementById('gf_employee_id').value;
                if (!employeeId) { alert('Please choose an employee before generating the schedule'); return; }
                const startDate = document.getElementById('gf_start_date').value;
                const endDate = document.getElementById('gf_end_date').value;

                if (!startDate || !endDate) { alert('Please provide start and end dates'); return; }

                const days = {};
                ['1','2','3','4','5','6'].forEach(function(d) {
                    const enabledEl = document.getElementById('gf_day_' + d + '_enabled');
                    const enabled = enabledEl ? enabledEl.checked : false;
                    if (!enabled) return;
                    const sEl = document.getElementById('gf_day_' + d + '_start');
                    const eEl = document.getElementById('gf_day_' + d + '_end');
                    if (!sEl || !eEl) return;
                    const s = sEl.value;
                    const e = eEl.value;
                    const bsEl = document.getElementById('gf_day_' + d + '_break_start');
                    const beEl = document.getElementById('gf_day_' + d + '_break_end');
                    const bs = bsEl ? bsEl.value : null;
                    const be = beEl ? beEl.value : null;
                    if (!s || !e) { alert('Please enter start/end for selected days'); throw 'validation'; }
                    days[d] = { start: s, end: e };
                    if (bs || be) { days[d].break_start = bs; days[d].break_end = be; }
                });

                const payload = { employee_id: employeeId, start_date: startDate, end_date: endDate, days: days };

                fetch('../app/api/schedules/generate_fixed_schedule.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        alert('Generated: ' + res.inserted + ' entries');
                        closeModal('generateFixedModal');
                        location.reload();
                    } else {
                        alert('Error: ' + (res.error || 'Unknown'));
                    }
                }).catch(err => { alert('Network error'); console.error(err); });
            }

            function copySelectedFlexToGenerate() {
                const srcId = document.getElementById('flex_employee_id');
                const srcName = document.getElementById('flex_employee_name');
                const display = document.getElementById('gf_selected_employee_display');
                const hid = document.getElementById('gf_employee_id');
                const searchInput = document.getElementById('gf_employee_search');
                if (srcId && srcId.value) {
                    hid.value = srcId.value;
                    display.textContent = (srcName && srcName.value) ? srcName.value : srcId.value;
                    if (searchInput) searchInput.value = (srcName && srcName.value) ? srcName.value : srcId.value;
                    closeModal('flexibleModal');
                } else {
                    alert('No employee selected in the flexible schedule modal');
                }
            }

            function loadFixedScheduleEmployees() {
                fetch('../app/api/get_employees.php')
                    .then(response => response.text())
                    .then(text => {
                        let data;
                        try { data = JSON.parse(text); } catch (err) { console.error('get_employees returned non-JSON:', text); throw err; }
                        if (data.success) {
                            fixedScheduleEmployees = data.employees || [];
                            filteredFixedScheduleEmployees = [...fixedScheduleEmployees];
                        } else {
                            console.error('Error loading employees:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            function renderFixedEmployeeSuggestions(employees) {
                const suggestions = document.getElementById('gf_employee_suggestions');
                const searchInput = document.getElementById('gf_employee_search');
                if (!suggestions || !searchInput) return;

                if (!employees || employees.length === 0) {
                    suggestions.innerHTML = '<div style="padding: 12px; color: #666;">No employees found</div>';
                    suggestions.style.display = 'block';
                    return;
                }

                suggestions.innerHTML = employees.map(emp => {
                    const label = emp.full_name || emp.employee || emp.name || emp.employee_id;
                    const id = emp.employee_id ?? emp.id ?? emp.employeeId;
                    return `<div class="employee-suggestion-item" data-id="${escapeHtml(id)}" data-name="${escapeHtml(label)}" style="padding: 12px 14px; cursor: pointer; border-bottom: 1px solid #eee;">${escapeHtml(label)}<span style="float:right; color:#999;">${escapeHtml(id)}</span></div>`;
                }).join('');
                suggestions.style.display = 'block';
            }

            function filterFixedEmployeeSuggestions(searchTerm) {
                const term = safeLower(searchTerm || '').trim();
                const suggestions = document.getElementById('gf_employee_suggestions');
                if (!suggestions) return;

                if (term.length === 0) {
                    suggestions.style.display = 'none';
                    return;
                }

                filteredFixedScheduleEmployees = fixedScheduleEmployees.filter(emp => {
                    const fullName = safeLower(emp.full_name || emp.employee || emp.name || '');
                    const employeeId = safeLower(String(emp.employee_id || emp.id || ''));
                    return fullName.includes(term) || employeeId.includes(term);
                });

                renderFixedEmployeeSuggestions(filteredFixedScheduleEmployees);
            }

            function searchFixedEmployees() {
                const searchTerm = document.getElementById('gf_employee_search').value;
                if (!searchTerm || String(searchTerm).trim().length === 0) {
                    alert('Enter an employee name or ID to search');
                    return;
                }
                filterFixedEmployeeSuggestions(searchTerm);
            }

            function toggleGfUnassignedEmployees() {
                const list = document.getElementById('gf_unassigned_employees');
                const button = document.getElementById('gf_show_unassigned_button');
                const suggestions = document.getElementById('gf_employee_suggestions');
                if (!list || !button) return;

                if (list.style.display === 'block') {
                    list.style.display = 'none';
                    button.textContent = 'Show unassigned employees';
                    return;
                }

                if (suggestions) suggestions.style.display = 'none';
                button.textContent = 'Loading unassigned...';
                fetch('../api/shift_assignment.php?action=get_unassigned_employees')
                    .then(response => response.text())
                    .then(text => {
                        let data;
                        try { data = JSON.parse(text); } catch (err) { console.error('get_unassigned_employees returned non-JSON:', text); throw err; }
                        if (data.success && Array.isArray(data.data)) {
                            renderUnassignedEmployeeList(data.data);
                            list.style.display = 'block';
                            button.textContent = 'Hide unassigned employees';
                        } else {
                            list.innerHTML = '<div style="padding:12px; color:#666;">No unassigned employees found.</div>';
                            list.style.display = 'block';
                            button.textContent = 'Hide unassigned employees';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        list.innerHTML = '<div style="padding:12px; color:#c00;">Unable to load unassigned employees.</div>';
                        list.style.display = 'block';
                        button.textContent = 'Hide unassigned employees';
                    });
            }

            function renderUnassignedEmployeeList(employees) {
                const list = document.getElementById('gf_unassigned_employees');
                if (!list) return;
                if (!employees || employees.length === 0) {
                    list.innerHTML = '<div style="padding:12px; color:#666;">No unassigned employees found.</div>';
                    return;
                }
                list.innerHTML = employees.map(emp => {
                    const label = emp.full_name || emp.employee || emp.name || emp.employee_id || emp.id || 'Unknown';
                    const id = emp.employee_id ?? emp.id ?? emp.employeeId ?? '';
                    return `<div class="employee-suggestion-item" data-id="${escapeHtml(id)}" data-name="${escapeHtml(label)}" style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #eee;">${escapeHtml(label)}<span style="float:right; color:#999;">${escapeHtml(id)}</span></div>`;
                }).join('');
            }

            function selectFixedEmployee(employeeId, employeeName) {
                const display = document.getElementById('gf_selected_employee_display');
                const hid = document.getElementById('gf_employee_id');
                const searchInput = document.getElementById('gf_employee_search');
                const suggestions = document.getElementById('gf_employee_suggestions');
                if (hid) hid.value = employeeId;
                if (display) display.textContent = employeeName || employeeId;
                if (searchInput) searchInput.value = employeeName || employeeId;
                if (suggestions) suggestions.style.display = 'none';
            }

            function toggleGfDayRow(dayIndex) {
                const controls = document.getElementById('gf_day_' + dayIndex + '_controls');
                const checkbox = document.getElementById('gf_day_' + dayIndex + '_enabled');
                if (!controls || !checkbox) return;
                controls.style.display = checkbox.checked ? 'grid' : 'none';
            }

            document.addEventListener('click', function(event) {
                const suggestions = document.getElementById('gf_employee_suggestions');
                const searchInput = document.getElementById('gf_employee_search');
                if (!suggestions || !searchInput) return;
                const target = event.target;
                if (target.closest && (target.closest('#gf_employee_suggestions') || target.closest('#gf_unassigned_employees'))) {
                    const item = target.closest('.employee-suggestion-item');
                    if (item) {
                        selectFixedEmployee(item.dataset.id, item.dataset.name);
                    }
                    return;
                }
                if (target === searchInput) return;
                suggestions.style.display = 'none';
            });

            if (typeof closeModal !== 'function') {
                window.closeModal = function(modalId) {
                    try {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.style.display = 'none';
                            document.body.classList.remove('modal-open');
                        }
                    } catch (e) { console.error('closeModal (defensive) error', e); }
                };
            }

            // Make sure the Create Shift button always opens a modal
            if (typeof openCreateShiftModal !== 'function') {
                window.openCreateShiftModal = function() { openModal('createShiftModal'); };
            }
        </script>

    <div id="overview" class="tab-content glass-panel" style="display: block;">
        <h2 style="margin-bottom: 30px; font-size: 24px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-chart-bar"></i>
            Shift Management Overview
        </h2>
            
            <div class="shift-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number"><?php echo count($shifts); ?></div>
                    <div class="stat-label">Total Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number">
                        <?php
                        $activeShiftCount = 0;
                        foreach ($shifts as $shift) {
                            if (!empty($shift['is_active'])) {
                                $activeShiftCount++;
                            }
                        }
                        echo $activeShiftCount;
                        ?>
                    </div>
                    <div class="stat-label">Active Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo count($allAssignments ?? []); ?></div>
                    <div class="stat-label">Total Assignments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-number">
                        <?php 
                        try {
                            $flex_count = $db->query("SELECT COUNT(*) as count FROM ta_flexible_schedules")->fetch(PDO::FETCH_ASSOC);
                            echo $flex_count['count'] ?? 0;
                        } catch (Exception $e) {
                            echo 0;
                        }
                        ?>
                    </div>
                    <div class="stat-label">Flexible Schedules</div>
                </div>
            </div>
        </div>

        <!-- Shift Breakdown removed: condensed into Shift Overview -->

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-user-check"></i> Shift Assignments
        </h3>
        
        <!-- Search and Controls -->
        <div style="margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <input type="text" id="assignmentSearch" placeholder="Search by employee, department, or shift..." style="width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px;" autocomplete="off">
                <div id="assignmentSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #e0e0e0; border-top: none; border-radius: 0 0 6px 6px; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
            <select id="assignmentSortBy" style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="employee">Sort by Employee</option>
                <option value="shift">Sort by Shifts</option>
                <option value="active">Sort by Active Count</option>
                <option value="status">Sort by Status</option>
            </select>
            <button type="button" onclick="resetAssignmentFilters()" style="padding: 10px 20px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
        
        <div class="table-container glass-panel">
            <table id="assignmentTable">
                <thead>
                    <tr>
                        <th style="cursor: pointer;" onclick="sortAssignments('employee')"><i class="fas fa-user"></i> Employee <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('shift_count')"><i class="fas fa-briefcase"></i> Shifts <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('active_count')"><i class="fas fa-check-circle"></i> Active <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('status')"><i class="fas fa-info-circle"></i> Status <i class="fas fa-sort"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="assignmentTableBody"></tbody>
            </table>
            <!-- Pagination for Assignments -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: white; border-radius: 10px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span id="assignmentInfo" style="font-size: 14px; color: #666;">Showing 0 of 0 records</span>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" onclick="previousAssignmentPage()" id="prevAssignBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <div id="assignmentPageNumbers" style="display: flex; gap: 5px;"></div>
                    <button type="button" onclick="nextAssignmentPage()" id="nextAssignBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="assignmentPerPage" style="font-size: 14px;">Records per page:</label>
                    <select id="assignmentPerPage" onchange="changeAssignmentPageSize()" style="padding: 6px 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="employeeShiftModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-user-clock"></i> Employee Shift Details</h2>
                    <button class="modal-close" onclick="closeModal('employeeShiftModal')">&times;</button>
                </div>
                <div class="modal-body" id="employeeShiftModalBody" style="padding-bottom: 0;"></div>
                <div class="modal-action-row" style="justify-content: flex-end; gap: 10px; padding: 20px 24px 24px;">
                    <button class="btn btn-secondary" onclick="closeModal('employeeShiftModal')">Close</button>
                    <button class="btn btn-primary" id="employeeShiftModalEditButton" type="button" onclick="openAssignmentModalForEmployee()" style="display: none;">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        </div>

        <div id="viewFlexibleScheduleModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-eye"></i> Flexible Schedule Details</h2>
                    <button class="modal-close" onclick="closeModal('viewFlexibleScheduleModal')">&times;</button>
                </div>
                <div class="modal-body" id="viewFlexibleScheduleModalBody" style="padding-bottom: 0;"></div>
                <div class="modal-action-row" style="justify-content: flex-end; gap: 10px; padding: 20px 24px 24px;">
                    <button class="btn btn-secondary" onclick="closeModal('viewFlexibleScheduleModal')">Cancel</button>
                    <button class="btn btn-primary" id="viewFlexibleScheduleModalEditButton" type="button" onclick="openFlexibleScheduleEditFromViewModal()" style="display: none;">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        </div>

        <script>
        // Assignment Table Data
        let assignmentTableData = <?php echo json_encode($allAssignments ?? [], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;

        // Shifts data exported from server to client to resolve shift_id -> name/time
        const shiftsArray = <?php echo json_encode($shifts ?? [], JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;
        const shiftsMap = {};
        (shiftsArray || []).forEach(s => {
            shiftsMap[String(s.shift_id ?? s.shiftId ?? s.id)] = s;
        });

        function formatTime(hms) {
            if (!hms) return '';
            // Accept 'HH:MM:SS' or 'HH:MM'
            const parts = String(hms).split(':');
            if (parts.length < 2) return hms;
            let hour = parseInt(parts[0], 10);
            const minute = parts[1].padStart(2, '0');
            const ampm = hour >= 12 ? 'PM' : 'AM';
            hour = hour % 12;
            if (hour === 0) hour = 12;
            return `${hour}:${minute} ${ampm}`;
        }

        function formatTimeRange(start, end) {
            if (!start && !end) return '';
            if (!start) return formatTime(end);
            if (!end) return formatTime(start);
            return `${formatTime(start)} - ${formatTime(end)}`;
        }
        let employeeAssignmentData = [];
        let assignmentCurrentPage = 1;
        let assignmentPageSize = 10;
        let assignmentSortField = 'employee';
        let assignmentSortAsc = true;
        let assignmentFilterText = '';
        let selectedEmployeeIdForEdit = null;

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, (match) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[match] || match));
        }

        function safeLower(value) {
            return String(value || '').toLowerCase();
        }

        function normalizeAssignmentData() {
            const employees = {};

            assignmentTableData.forEach(row => {
                const empId = String(row.employee_id ?? row.employee ?? row.employeeId ?? '');
                const employeeName = row.employee || row.full_name || row.name || 'Unknown';
                const departmentName = row.department || row.dept || '';
                const isActive = row.isActive || row.is_active === 1 || row.is_active === '1' || row.is_active === true;
                const rowStatus = row.status || (isActive ? 'Active' : 'Scheduled');

                if (!employees[empId]) {
                    employees[empId] = {
                        employee_id: empId,
                        employee: employeeName,
                        department: departmentName,
                        shift_count: 0,
                        active_count: 0,
                        status: rowStatus,
                        assignments: []
                    };
                }

                employees[empId].shift_count += 1;
                if (isActive) {
                    employees[empId].active_count += 1;
                    employees[empId].status = 'Active';
                }

                // Normalize assignment row and attach shift details when available
                const assign = Object.assign({}, row);
                const sid = String((row.shift_id ?? row.shiftId ?? row.shift) || '');
                const shiftInfo = shiftsMap[sid] || {};
                assign.shift = shiftInfo.shift_name || row.shift || '';
                assign.shift_id = sid || (row.shift_id || row.shiftId || '');
                assign.time = formatTimeRange(shiftInfo.start_time || shiftInfo.start_time, shiftInfo.end_time || shiftInfo.end_time) || row.time || '';
                assign.from = shiftInfo.start_time || row.from || row.start || '';
                assign.to = shiftInfo.end_time || row.to || row.end || '';
                assign.isActive = isActive;
                assign.status = row.status || (isActive ? 'Active' : 'Scheduled');

                employees[empId].assignments.push(assign);
            });

            employeeAssignmentData = Object.values(employees);
        }

        function renderAssignmentTable() {
            const filtered = employeeAssignmentData.filter(row => {
                const searchTerm = safeLower(assignmentFilterText);
                const employeeName = String(row.employee || row.full_name || '');
                const departmentName = String(row.department || '');
                const matchesEmployee = safeLower(employeeName).includes(searchTerm);
                const matchesDepartment = safeLower(departmentName).includes(searchTerm);
                const matchesShift = row.assignments.some(assign => {
                    const shiftText = String(assign.shift || assign.shift_name || assign.shiftName || '');
                    return safeLower(shiftText).includes(searchTerm);
                });
                return matchesEmployee || matchesDepartment || matchesShift;
            });

            const sorted = [...filtered];
            sorted.sort((a, b) => {
                let aVal = a[assignmentSortField];
                let bVal = b[assignmentSortField];

                if (assignmentSortField === 'shift_count' || assignmentSortField === 'active_count') {
                    aVal = parseInt(aVal, 10);
                    bVal = parseInt(bVal, 10);
                }

                if (aVal < bVal) return assignmentSortAsc ? -1 : 1;
                if (aVal > bVal) return assignmentSortAsc ? 1 : -1;
                return 0;
            });

            const totalRecords = sorted.length;
            const totalPages = Math.ceil(totalRecords / assignmentPageSize);
            if (assignmentCurrentPage > totalPages && totalPages > 0) assignmentCurrentPage = totalPages;

            const start = (assignmentCurrentPage - 1) * assignmentPageSize;
            const end = start + assignmentPageSize;
            const pageData = sorted.slice(start, end);

            let html = '';
            if (pageData.length === 0) {
                html = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>No employee assignments found.</td></tr>';
            } else {
                pageData.forEach(row => {
                    const employeeIdSafe = String(row.employee_id || '');
                    const employeeIdJson = JSON.stringify(employeeIdSafe);
                    const employeeIdJsonEscaped = escapeHtml(employeeIdJson);
                    html += `<tr>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-user-circle" style="font-size: 20px; color: #3498db;"></i>
                                    <span>${escapeHtml(row.employee)}</span>
                                </div>
                                <span style="font-size: 12px; color: #777;">${escapeHtml(row.department || 'No department')}</span>
                            </div>
                        </td>
                        <td>${row.shift_count}</td>
                        <td>${row.active_count}</td>
                        <td><span class="shift-status ${row.status === 'Active' ? '' : 'inactive'}">${row.status}</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" onclick='openEmployeeShiftModal(${employeeIdJsonEscaped})' style="padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>`;
                });
            }

            document.getElementById('assignmentTableBody').innerHTML = html;
            updateAssignmentPagination(totalRecords, totalPages);
        }

        function updateAssignmentPagination(total, pages) {
            document.getElementById('assignmentInfo').textContent = total === 0 ? 'Showing 0 of 0 records' : `Showing ${Math.min((assignmentCurrentPage - 1) * assignmentPageSize + 1, total)} to ${Math.min(assignmentCurrentPage * assignmentPageSize, total)} of ${total} records`;
            const pageNumbers = document.getElementById('assignmentPageNumbers');
            pageNumbers.innerHTML = '';
            for (let i = Math.max(1, assignmentCurrentPage - 2); i <= Math.min(pages, assignmentCurrentPage + 2); i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = i;
                btn.style.cssText = `padding: 6px 12px; border: 2px solid ${i === assignmentCurrentPage ? '#003d82' : '#ddd'}; background: ${i === assignmentCurrentPage ? '#003d82' : 'white'}; color: ${i === assignmentCurrentPage ? 'white' : '#333'}; border-radius: 6px; cursor: pointer; font-weight: ${i === assignmentCurrentPage ? '600' : '400'};`;
                btn.onclick = () => { assignmentCurrentPage = i; renderAssignmentTable(); };
                pageNumbers.appendChild(btn);
            }
            document.getElementById('prevAssignBtn').disabled = assignmentCurrentPage === 1;
            document.getElementById('nextAssignBtn').disabled = assignmentCurrentPage === pages || pages === 0;
        }

        function nextAssignmentPage() {
            const pages = Math.ceil(employeeAssignmentData.length / assignmentPageSize);
            if (assignmentCurrentPage < pages) assignmentCurrentPage++;
            renderAssignmentTable();
        }

        function previousAssignmentPage() {
            if (assignmentCurrentPage > 1) assignmentCurrentPage--;
            renderAssignmentTable();
        }

        function changeAssignmentPageSize() {
            assignmentPageSize = parseInt(document.getElementById('assignmentPerPage').value, 10);
            assignmentCurrentPage = 1;
            renderAssignmentTable();
        }

        function sortAssignments(field) {
            if (assignmentSortField === field) {
                assignmentSortAsc = !assignmentSortAsc;
            } else {
                assignmentSortField = field;
                assignmentSortAsc = true;
            }
            assignmentCurrentPage = 1;
            renderAssignmentTable();
        }

        function resetAssignmentFilters() {
            assignmentFilterText = '';
            document.getElementById('assignmentSearch').value = '';
            assignmentCurrentPage = 1;
            assignmentSortField = 'employee';
            assignmentSortAsc = true;
            renderAssignmentTable();
        }

        function openEmployeeShiftModal(employeeId) {
            const employee = employeeAssignmentData.find(row => row.employee_id === employeeId);
            if (!employee) return;

            console.log('openEmployeeShiftModal - employee:', employee);

            const detailsHtmlParts = [];

            detailsHtmlParts.push(
                '<div style="padding-bottom: 20px;">' +
                '<p style="margin: 0 0 10px; color: #555;">Employee: <strong>' + escapeHtml(employee.employee) + '</strong></p>' +
                '<p style="margin: 0; color: #555;">Department: <strong>' + escapeHtml(employee.department || 'N/A') + '</strong></p>' +
                '</div>'
            );

            detailsHtmlParts.push('<div style="margin-bottom: 20px;">');
            detailsHtmlParts.push('<div style="margin-bottom: 12px; font-weight: 700; color: #333;">Shift Assignments (' + (employee.assignments ? employee.assignments.length : 0) + ')</div>');

            if (!employee.assignments || employee.assignments.length === 0) {
                detailsHtmlParts.push('<div style="color: #777;">No shifts assigned yet for this employee.</div>');
            } else {
                detailsHtmlParts.push(
                    '<table style="width:100%; border-collapse: collapse; margin-bottom: 16px;">' +
                    '<thead><tr style="background:#f4f6f8;">' +
                    '<th style="padding: 10px; text-align:left; font-size: 13px; color:#333;">Shift</th>' +
                    '<th style="padding: 10px; text-align:left; font-size: 13px; color:#333;">Time</th>' +
                    '<th style="padding: 10px; text-align:left; font-size: 13px; color:#333;">From</th>' +
                    '<th style="padding: 10px; text-align:left; font-size: 13px; color:#333;">To</th>' +
                    '<th style="padding: 10px; text-align:left; font-size: 13px; color:#333;">Status</th>' +
                    '</tr></thead><tbody>'
                );

                employee.assignments.forEach(row => {
                    const shiftName = row.shift || row.shift_name || row.shiftName || '—';
                    const timeText = row.time || (row.from && row.to ? row.from + ' - ' + row.to : '—');
                    const fromText = row.from || row.start || '—';
                    const toText = row.to || row.end || '—';
                    const isActiveFlag = row.isActive || row.is_active === 1 || row.is_active === '1' || false;
                    const statusText = row.status || (isActiveFlag ? 'Active' : 'Scheduled');

                    detailsHtmlParts.push(
                        '<tr>' +
                        '<td style="padding: 10px; border-bottom: 1px solid #eaeaea;">' + escapeHtml(shiftName) + '</td>' +
                        '<td style="padding: 10px; border-bottom: 1px solid #eaeaea;">' + escapeHtml(timeText) + '</td>' +
                        '<td style="padding: 10px; border-bottom: 1px solid #eaeaea;">' + escapeHtml(fromText) + '</td>' +
                        '<td style="padding: 10px; border-bottom: 1px solid #eaeaea;">' + escapeHtml(toText) + '</td>' +
                        '<td style="padding: 10px; border-bottom: 1px solid #eaeaea;">' +
                        '<span style="display:inline-flex; align-items:center; gap:6px; background:' + (isActiveFlag ? '#d4edda' : '#f8d7da') + '; color:' + (isActiveFlag ? '#155724' : '#721c24') + '; padding: 4px 10px; border-radius: 999px;">' +
                        (isActiveFlag ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-clock"></i>') + ' ' + escapeHtml(statusText) +
                        '</span></td>' +
                        '</tr>'
                    );
                });

                detailsHtmlParts.push('</tbody></table>');
                detailsHtmlParts.push(
                    '<details style="font-size:12px; color:#666;">' +
                    '<summary>Show raw assignment data (debug)</summary>' +
                    '<pre style="white-space:pre-wrap; max-height:200px; overflow:auto;">' + escapeHtml(JSON.stringify(employee.assignments, null, 2)) + '</pre>' +
                    '</details>'
                );
            }

            detailsHtmlParts.push('</div>');

            document.getElementById('employeeShiftModalBody').innerHTML = detailsHtmlParts.join('');
            document.getElementById('employeeShiftModalEditButton').style.display = 'inline-flex';
            selectedEmployeeIdForEdit = employeeId;
            openModal('employeeShiftModal');
        }

        function openAssignmentModalForEmployee() {
            if (!selectedEmployeeIdForEdit) {
                closeModal('employeeShiftModal');
                return;
            }
            // Enter single-employee edit mode
            assignmentMode = 'edit';
            selectedEmployeeForEdit = selectedEmployeeIdForEdit;
            selectedEmployees = new Set([selectedEmployeeForEdit]);
            const selectedCountEl = document.getElementById('selectedCount');
            if (selectedCountEl) selectedCountEl.textContent = '1';

            // Fetch employee details from server to ensure name/assignment are authoritative
            fetch('../app/api/get_employee_details.php', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new URLSearchParams({ employee_id: selectedEmployeeForEdit })
            })
            .then(r => r.text())
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    console.error('get_employee_details returned non-JSON:', text);
                    throw new Error('Server error when fetching employee details');
                }
                if (!data || !data.success) {
                    throw new Error(data && data.message ? data.message : 'Failed to fetch employee details');
                }

                // Fill display
                const emp = data.employee || null;
                const assignments = data.assignments || [];
                const sd = document.getElementById('selectedEmployeeDisplay');
                const summary = document.getElementById('selectedSummary');
                if (sd) {
                    const name = emp ? (emp.full_name || emp.full_name || emp.employee_id) : ('Employee ID: ' + selectedEmployeeForEdit);
                    const dept = emp ? (emp.department || '') : '';
                    sd.innerHTML = '<div style="display:flex; gap:10px; align-items:center;"><i class="fas fa-user-circle" style="font-size:18px;color:#1565c0"></i><div><div style="font-weight:700;color:#233e8b">' + escapeHtml(name) + '</div>' + (dept ? '<div style="font-size:12px;color:#777">' + escapeHtml(dept) + '</div>' : '') + '</div></div>';
                    sd.style.display = 'block';
                }
                if (summary && emp) {
                    summary.innerHTML = '<strong>Selected:</strong> ' + escapeHtml(emp.full_name || emp.employee_id);
                }

                // Prefill assignment fields if we have assignment data
                if (assignments && assignments.length > 0) {
                    const activeAssign = assignments.find(a => a.is_active == 1 || a.isActive) || assignments[0];
                    if (activeAssign) {
                        const shiftSel = document.getElementById('shift_id');
                        if (shiftSel && (activeAssign.shift_id || activeAssign.shiftId || activeAssign.shift)) {
                            shiftSel.value = activeAssign.shift_id || activeAssign.shiftId || activeAssign.shift;
                        }
                        const effFrom = document.getElementById('effective_from');
                        if (effFrom && activeAssign.effective_from) effFrom.value = (activeAssign.effective_from || '').split(' ')[0];
                        const effTo = document.getElementById('effective_to');
                        if (effTo && activeAssign.effective_to) effTo.value = (activeAssign.effective_to || '').split ? activeAssign.effective_to.split(' ')[0] : activeAssign.effective_to;
                        const excl = document.getElementById('exclude_saturday');
                        if (excl) excl.checked = !!(activeAssign.exclude_saturday || activeAssign.excludeSaturday || false);
                    }
                }

                loadEmployeeList();
                openModal('assignmentModal');
                closeModal('employeeShiftModal');
            })
            .catch(err => {
                console.error('Error fetching employee details:', err);
                // Fallback: open modal without prefill
                loadEmployeeList();
                openModal('assignmentModal');
                closeModal('employeeShiftModal');
            });
        }

        const assignmentSearchInput = document.getElementById('assignmentSearch');
        if (assignmentSearchInput) {
            assignmentSearchInput.addEventListener('keyup', function() {
                assignmentFilterText = this.value || '';
                assignmentCurrentPage = 1;
                showAssignmentSuggestions();
                renderAssignmentTable();
            });
        }

        function showAssignmentSuggestions() {
            const searchBox = document.getElementById('assignmentSearch');
            const suggestionsBox = document.getElementById('assignmentSuggestions');
            if (!searchBox || !suggestionsBox) {
                return;
            }
            const query = safeLower(searchBox.value).trim();

            if (query.length === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }

            const suggestions = new Set();
            employeeAssignmentData.forEach(row => {
                const employeeName = String(row.employee || row.full_name || '');
                const departmentName = String(row.department || '');
                if (safeLower(employeeName).includes(query)) {
                    suggestions.add(employeeName);
                }
                if (safeLower(departmentName).includes(query)) {
                    suggestions.add(departmentName);
                }
                row.assignments.forEach(assign => {
                    const shiftText = String(assign.shift || assign.shift_name || assign.shiftName || '');
                    if (safeLower(shiftText).includes(query)) {
                        suggestions.add(assign.shift || assign.shift_name || assign.shiftName || '');
                    }
                });
            });

            if (suggestions.size === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }

            suggestionsBox.innerHTML = '';
            Array.from(suggestions).slice(0, 8).forEach(suggestion => {
                const item = document.createElement('div');
                item.style.cssText = 'padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;';
                item.innerHTML = `<i class="fas fa-search" style="color: #999; margin-right: 8px;"></i>${escapeHtml(suggestion)}`;
                item.onmouseover = () => item.style.background = '#f8f9fa';
                item.onmouseout = () => item.style.background = 'white';
                item.onclick = () => {
                    searchBox.value = suggestion;
                    assignmentFilterText = suggestion;
                    assignmentCurrentPage = 1;
                    renderAssignmentTable();
                    suggestionsBox.style.display = 'none';
                };
                suggestionsBox.appendChild(item);
            });

            suggestionsBox.style.display = 'block';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#assignmentSearch') && !e.target.closest('#assignmentSuggestions')) {
                document.getElementById('assignmentSuggestions').style.display = 'none';
            }
        });

        normalizeAssignmentData();
        renderAssignmentTable();
    </script>

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-calendar-check"></i> Flexible Schedules
        </h3>
        
        <!-- Search and Controls for Flexible -->
        <div style="margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <input type="text" id="flexibleSearch" placeholder="Search by employee name or notes..." style="width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px;" autocomplete="off">
                <div id="flexibleSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #e0e0e0; border-top: none; border-radius: 0 0 6px 6px; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
            <select id="flexibleSortBy" style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="employee">Sort by Employee</option>
                <option value="day">Sort by Day(s)</option>
                <option value="status">Sort by Status</option>
            </select>
            <button type="button" onclick="resetFlexibleFilters()" style="padding: 10px 20px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
        
        <div class="table-container">
            <table id="flexibleTable">
                    <thead>
                        <tr>
                            <th style="cursor: pointer;" onclick="sortFlexible('employee')"><i class="fas fa-user"></i> Employee <i class="fas fa-sort"></i></th>
                            <th><i class="fas fa-calendar-check"></i> Day(s)</th>
                            <th style="cursor: pointer;" onclick="sortFlexible('status')"><i class="fas fa-info-circle"></i> Status <i class="fas fa-sort"></i></th>
                            <th><i class="fas fa-cog"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody id="flexibleTableBody">
                        <?php
                        // Prepare flexible schedule data
                        $flexibleData = [];
                        try {
                            $flex_query = "SELECT fs.id, fs.employee_id, fs.schedule_date, fs.start_time, fs.end_time, 
                                          fs.day_of_week, fs.repeat_until, fs.contract_end_date, fs.notes, fs.created_at,
                                          e.full_name
                                          FROM ta_flexible_schedules fs
                                          LEFT JOIN employees e ON fs.employee_id = e.employee_id
                                          ORDER BY fs.schedule_date DESC, fs.start_time ASC";
                            
                            $flex_stmt = $db->query($flex_query);
                            $flex_schedules = $flex_stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            if (!empty($flex_schedules)):
                                foreach ($flex_schedules as $flex):
                                    $day_text = $flex['day_of_week'] !== null ? $day_names[$flex['day_of_week']] . ' (Weekly)' : date('l', strtotime($flex['schedule_date']));
                                    $today = date('Y-m-d');
                                    $status = 'Inactive';
                                    if ($flex['day_of_week'] !== null) {
                                        $status = empty($flex['repeat_until']) || $flex['repeat_until'] >= $today ? 'Active' : 'Inactive';
                                    } else {
                                        $status = $flex['schedule_date'] >= $today ? 'Active' : 'Inactive';
                                    }
                                    $flexibleData[] = [
                                        'id' => $flex['id'],
                                        'employee_id' => $flex['employee_id'],
                                        'employee' => $flex['full_name'] ?? 'Unknown',
                                        'date' => date('M d, Y', strtotime($flex['schedule_date'])),
                                        'dateSort' => strtotime($flex['schedule_date']),
                                        'day' => $day_text,
                                        'status' => $status,
                                        'time' => date('g:i A', strtotime($flex['start_time'])) . ' - ' . date('g:i A', strtotime($flex['end_time'])),
                                        'timeSort' => $flex['start_time'],
                                        'repeat' => $flex['repeat_until'] ? date('M d, Y', strtotime($flex['repeat_until'])) : '—',
                                        'contract' => $flex['contract_end_date'] ? date('M d, Y', strtotime($flex['contract_end_date'])) : '—',
                                        'notes' => $flex['notes'] ?? '',
                                        'day_of_week' => $flex['day_of_week'],
                                        'schedule_date' => $flex['schedule_date'],
                                        'start_time' => $flex['start_time'],
                                        'end_time' => $flex['end_time'],
                                        'repeat_until' => $flex['repeat_until'],
                                        'contract_end_date' => $flex['contract_end_date']
                                    ];
                                endforeach;
                            endif;
                        } catch (Exception $e) {
                            error_log("ERROR: Flexible schedules query failed: " . $e->getMessage());
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Pagination for Flexible Schedules -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.8); border-radius: 10px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span id="flexibleInfo" style="font-size: 14px; color: #666;">Showing 0 of 0 records</span>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" onclick="previousFlexiblePage()" id="prevFlexBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <div id="flexiblePageNumbers" style="display: flex; gap: 5px;"></div>
                        <button type="button" onclick="nextFlexiblePage()" id="nextFlexBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="flexiblePerPage" style="font-size: 14px;">Records per page:</label>
                        <select id="flexiblePerPage" onchange="changeFlexiblePageSize()" style="padding: 6px 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <script>
            // Flexible Schedules Table Data
            const rawFlexibleTableData = <?php echo json_encode($flexibleData, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;
            let flexibleTableData = [];
            let flexibleCurrentPage = 1;
            let flexiblePageSize = 10;
            let flexibleSortField = 'employee';
            let flexibleSortAsc = true;
            let flexibleFilterText = '';

            function escapeFlexibleHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, character => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[character]));
            }

            function groupFlexibleTableData() {
                const groups = {};
                rawFlexibleTableData.forEach(row => {
                    const key = String(row.employee_id ?? row.employee ?? row.id ?? 'unknown');
                    if (!groups[key]) {
                        groups[key] = {
                            employee_id: row.employee_id,
                            employee: row.employee || 'Unknown',
                            schedules: [],
                            daySet: new Set(),
                            status: 'Inactive',
                            notes: ''
                        };
                    }

                    const group = groups[key];
                    group.schedules.push(row);
                    group.notes = group.notes || row.notes || '';
                    if (row.day) {
                        group.daySet.add(row.day);
                    }
                    if (String(row.status || '').toLowerCase() === 'active') {
                        group.status = 'Active';
                    }
                });

                flexibleTableData = Object.values(groups).map(group => ({
                    employee_id: group.employee_id,
                    employee: group.employee,
                    day: Array.from(group.daySet).join(', '),
                    status: group.status,
                    schedules: group.schedules,
                    notes: group.notes
                }));
            }

            function renderFlexibleTable() {
                groupFlexibleTableData();
                const filtered = flexibleTableData.filter(row => {
                    const searchTerm = safeLower(flexibleFilterText);
                    return safeLower(row.employee).includes(searchTerm) ||
                        safeLower(row.day).includes(searchTerm) ||
                        safeLower(row.status).includes(searchTerm) ||
                        safeLower(row.notes).includes(searchTerm);
                });

                const sorted = [...filtered];
                sorted.sort((a, b) => {
                    let aVal = a[flexibleSortField] ?? '';
                    let bVal = b[flexibleSortField] ?? '';

                    if (typeof aVal === 'number' && typeof bVal === 'number') {
                        return flexibleSortAsc ? aVal - bVal : bVal - aVal;
                    }

                    aVal = String(aVal).toLowerCase();
                    bVal = String(bVal).toLowerCase();
                    if (aVal < bVal) return flexibleSortAsc ? -1 : 1;
                    if (aVal > bVal) return flexibleSortAsc ? 1 : -1;
                    return 0;
                });

                const totalRecords = sorted.length;
                const totalPages = Math.ceil(totalRecords / flexiblePageSize);

                if (flexibleCurrentPage > totalPages && totalPages > 0) {
                    flexibleCurrentPage = totalPages;
                }

                const start = (flexibleCurrentPage - 1) * flexiblePageSize;
                const end = start + flexiblePageSize;
                const pageData = sorted.slice(start, end);

                let html = '';
                if (pageData.length === 0) {
                    html = '<tr><td colspan="4" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>No flexible schedules found.</td></tr>';
                } else {
                    pageData.forEach(row => {
                        const viewData = JSON.stringify(row);
                        html += `<tr>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user-circle" style="font-size: 20px; color: #3498db;"></i>
                                        <span>${escapeFlexibleHtml(row.employee)}</span>
                                    </div>
                                </div>
                            </td>
                            <td>${escapeFlexibleHtml(row.day)}</td>
                            <td><span class="shift-status ${row.status === 'Active' ? '' : 'inactive'}">${escapeFlexibleHtml(row.status)}</span></td>
                            <td style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button type="button" class="btn btn-sm btn-primary" data-flexible-view="${escapeFlexibleHtml(viewData)}" onclick="openFlexibleScheduleViewFromButton(this);" style="padding: 6px 12px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>`;
                    });
                }

                document.getElementById('flexibleTableBody').innerHTML = html;
                updateFlexiblePagination(totalRecords, totalPages);
            }

            let currentFlexibleViewData = null;

            function openFlexibleScheduleViewFromButton(button) {
                try {
                    const viewData = JSON.parse(button.dataset.flexibleView);
                    currentFlexibleViewData = viewData;
                    openFlexibleScheduleView(viewData);
                } catch (error) {
                    console.error('Error reading flexible schedule view data:', error);
                }
            }

            function openFlexibleScheduleView(data) {
                const scheduleRows = Array.isArray(data.schedules) ? data.schedules : [];
                const scheduleDetails = scheduleRows.map((item, index) => {
                    return `
                        <div style="margin-bottom: 16px; padding: 14px; background: #f7f9fc; border-radius: 8px; border: 1px solid #e3e8ef;">
                            <div style="display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                                <strong style="font-size: 14px; color: #2c3e50;">Shift ${index + 1}</strong>
                                <span style="font-size: 13px; color: #555;">Status: <strong>${escapeFlexibleHtml(item.status || 'Unknown')}</strong></span>
                            </div>
                            <p style="margin: 8px 0 4px; color: #555;">Date: <strong>${escapeFlexibleHtml(item.date || '')}</strong></p>
                            <p style="margin: 4px 0; color: #555;">Day(s): <strong>${escapeFlexibleHtml(item.day || '')}</strong></p>
                            <p style="margin: 4px 0; color: #555;">Time: <strong>${escapeFlexibleHtml(item.time || '')}</strong></p>
                            <p style="margin: 4px 0; color: #555;">Repeat Until: <strong>${escapeFlexibleHtml(item.repeat || '—')}</strong></p>
                            <p style="margin: 4px 0; color: #555;">Contract End Date: <strong>${escapeFlexibleHtml(item.contract || '—')}</strong></p>
                            <p style="margin: 4px 0; color: #555;">Notes: <strong>${escapeFlexibleHtml(item.notes || '—')}</strong></p>
                        </div>`;
                }).join('');

                const detailsHtml = [
                    '<div style="padding-bottom: 20px;">',
                    '<p style="margin: 0 0 10px; color: #555;">Employee: <strong>' + escapeFlexibleHtml(data.employee || 'Unknown') + '</strong></p>',
                    '<p style="margin: 0 0 10px; color: #555;">Day(s): <strong>' + escapeFlexibleHtml(data.day || '') + '</strong></p>',
                    '<p style="margin: 0 0 10px; color: #555;">Status: <strong>' + escapeFlexibleHtml(data.status || 'Unknown') + '</strong></p>',
                    '<p style="margin: 0 0 10px; color: #555;">Total Shifts: <strong>' + scheduleRows.length + '</strong></p>',
                    '<div style="margin-top: 10px;">' + scheduleDetails + '</div>',
                    '</div>'
                ].join('');

                const editButton = document.getElementById('viewFlexibleScheduleModalEditButton');
                if (editButton) {
                    editButton.style.display = data && Array.isArray(data.schedules) && data.schedules.length > 0 ? 'inline-flex' : 'none';
                }

                document.getElementById('viewFlexibleScheduleModalBody').innerHTML = detailsHtml;
                openModal('viewFlexibleScheduleModal');
            }

            function openFlexibleScheduleEditFromViewModal() {
                if (!currentFlexibleViewData || !Array.isArray(currentFlexibleViewData.schedules) || currentFlexibleViewData.schedules.length === 0) {
                    return;
                }

                // Prefer the first weekly schedule entry by day_of_week, otherwise fall back to the first schedule row.
                let schedule = currentFlexibleViewData.schedules[0];
                const weeklySchedules = currentFlexibleViewData.schedules
                    .filter(item => item.day_of_week !== undefined && item.day_of_week !== null && item.day_of_week !== '')
                    .sort((a, b) => Number(a.day_of_week) - Number(b.day_of_week));
                if (weeklySchedules.length > 0) {
                    schedule = weeklySchedules[0];
                }

                closeModal('viewFlexibleScheduleModal');
                openFlexibleScheduleEdit(
                    schedule.id,
                    schedule.employee_id,
                    currentFlexibleViewData.employee || '',
                    schedule.day_of_week ?? schedule.dayOfWeek ?? '',
                    schedule.schedule_date || schedule.date,
                    schedule.start_time || (schedule.time ? schedule.time.split(' - ')[0] : ''),
                    schedule.end_time || (schedule.time ? schedule.time.split(' - ')[1] : ''),
                    schedule.notes || '',
                    schedule.repeat_until || schedule.repeat || '',
                    schedule.contract_end_date || schedule.contract || ''
                );
            }

            function updateFlexiblePagination(total, pages) {
                document.getElementById('flexibleInfo').textContent = `Showing ${Math.min((flexibleCurrentPage - 1) * flexiblePageSize + 1, total)} to ${Math.min(flexibleCurrentPage * flexiblePageSize, total)} of ${total} records`;
                
                const pageNumbers = document.getElementById('flexiblePageNumbers');
                pageNumbers.innerHTML = '';
                for (let i = Math.max(1, flexibleCurrentPage - 2); i <= Math.min(pages, flexibleCurrentPage + 2); i++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = i;
                    btn.style.cssText = `padding: 6px 12px; border: 2px solid ${i === flexibleCurrentPage ? '#003d82' : '#ddd'}; background: ${i === flexibleCurrentPage ? '#003d82' : 'white'}; color: ${i === flexibleCurrentPage ? 'white' : '#333'}; border-radius: 6px; cursor: pointer; font-weight: ${i === flexibleCurrentPage ? '600' : '400'};`;
                    btn.onclick = () => { flexibleCurrentPage = i; renderFlexibleTable(); };
                    pageNumbers.appendChild(btn);
                }

                document.getElementById('prevFlexBtn').disabled = flexibleCurrentPage === 1;
                document.getElementById('nextFlexBtn').disabled = flexibleCurrentPage === pages || pages === 0;
            }

            function nextFlexiblePage() {
                const pages = Math.ceil(flexibleTableData.length / flexiblePageSize);
                if (flexibleCurrentPage < pages) flexibleCurrentPage++;
                renderFlexibleTable();
            }

            function previousFlexiblePage() {
                if (flexibleCurrentPage > 1) flexibleCurrentPage--;
                renderFlexibleTable();
            }

            function changeFlexiblePageSize() {
                flexiblePageSize = parseInt(document.getElementById('flexiblePerPage').value);
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            }

            function sortFlexible(field) {
                if (flexibleSortField === field) {
                    flexibleSortAsc = !flexibleSortAsc;
                } else {
                    flexibleSortField = field;
                    flexibleSortAsc = true;
                }
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            }

            function resetFlexibleFilters() {
                flexibleFilterText = '';
                document.getElementById('flexibleSearch').value = '';
                flexibleCurrentPage = 1;
                flexibleSortField = 'employee';
                flexibleSortAsc = true;
                renderFlexibleTable();
            }

            // Search live filtering
            const flexibleSearchInput = document.getElementById('flexibleSearch');
            if (flexibleSearchInput) {
                flexibleSearchInput.addEventListener('keyup', function() {
                    flexibleFilterText = this.value || '';
                    flexibleCurrentPage = 1;
                    showFlexibleSuggestions();
                    renderFlexibleTable();
                });
            }

            // Show suggestions for flexible search
            function showFlexibleSuggestions() {
                const searchBox = document.getElementById('flexibleSearch');
                const suggestionsBox = document.getElementById('flexibleSuggestions');
                const query = safeLower(searchBox.value).trim();

                if (query.length === 0) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                const suggestions = new Set();
                flexibleTableData.forEach(row => {
                    const emp = String(row.employee || '');
                    const notes = String(row.notes || '');
                    if (safeLower(emp).includes(query)) {
                        suggestions.add(emp);
                    }
                    if (safeLower(notes).includes(query)) {
                        suggestions.add(notes.substring(0, 50));
                    }
                });

                if (suggestions.size === 0) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                suggestionsBox.innerHTML = '';
                Array.from(suggestions).slice(0, 8).forEach(suggestion => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;';
                    item.innerHTML = `<i class="fas fa-search" style="color: #999; margin-right: 8px;"></i>${suggestion}`;
                    item.onmouseover = () => item.style.background = '#f8f9fa';
                    item.onmouseout = () => item.style.background = 'white';
                    item.onclick = () => {
                        searchBox.value = suggestion;
                        flexibleFilterText = suggestion;
                        flexibleCurrentPage = 1;
                        renderFlexibleTable();
                        suggestionsBox.style.display = 'none';
                    };
                    suggestionsBox.appendChild(item);
                });

                suggestionsBox.style.display = 'block';
            }

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#flexibleSearch') && !e.target.closest('#flexibleSuggestions')) {
                    document.getElementById('flexibleSuggestions').style.display = 'none';
                }
            });

            // Sort dropdown
            document.getElementById('assignmentSortBy').addEventListener('change', function() {
                const mapping = {
                    'employee': 'employee',
                    'shift': 'shift_count',
                    'active': 'active_count',
                    'status': 'status'
                };
                assignmentSortField = mapping[this.value] || 'employee';
                assignmentCurrentPage = 1;
                renderAssignmentTable();
            });

            // Sort dropdown
            document.getElementById('flexibleSortBy').addEventListener('change', function() {
                const mapping = {
                    'employee': 'employee',
                    'day': 'day',
                    'status': 'status'
                };
                flexibleSortField = mapping[this.value] || 'employee';
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            });

            // Initial render
            renderFlexibleTable();
            </script>

        <!-- Flexible Tab -->
        <div id="flexible" class="tab-content">
            <h2 style="margin-bottom: 30px; font-size: 24px; font-weight: 700; color: #2c3e50;">
                <i class="fas fa-calendar-day"></i> Flexible Schedules
            </h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Day(s)</th>
                            <th>Time</th>
                            <th>Repeat Until</th>
                            <th>Contract End Date</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $flex_query = "SELECT fs.id, fs.employee_id, fs.schedule_date, fs.start_time, fs.end_time, 
                                          fs.day_of_week, fs.repeat_until, fs.contract_end_date, fs.notes, fs.created_at,
                                          e.full_name
                                          FROM ta_flexible_schedules fs
                                          LEFT JOIN employees e ON fs.employee_id = e.employee_id
                                          ORDER BY fs.schedule_date DESC, fs.start_time ASC";
                            
                            $flex_stmt = $db->query($flex_query);
                            $flex_schedules = $flex_stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($flex_schedules)):
                                foreach ($flex_schedules as $flex):
                                    $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $day_text = $flex['day_of_week'] !== null ? $day_names[$flex['day_of_week']] . ' (Weekly)' : date('l', strtotime($flex['schedule_date']));
                        ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($flex['full_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($flex['schedule_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($day_text); ?></td>
                                        <td><?php echo date('g:i A', strtotime($flex['start_time'])); ?> - <?php echo date('g:i A', strtotime($flex['end_time'])); ?></td>
                                        <td><?php echo $flex['repeat_until'] ? date('M d, Y', strtotime($flex['repeat_until'])) : '—'; ?></td>
                                        <td><?php echo $flex['contract_end_date'] ? date('M d, Y', strtotime($flex['contract_end_date'])) : '—'; ?></td>
                                        <td><?php echo $flex['notes'] ? htmlspecialchars(substr($flex['notes'], 0, 50)) . (strlen($flex['notes']) > 50 ? '...' : '') : '—'; ?></td>
                                        <td style="display: flex; gap: 8px;">
                                            <button type="button" class="btn btn-sm btn-primary" data-flexible-edit="<?php echo htmlspecialchars(json_encode([
                                                $flex['id'],
                                                (string)$flex['employee_id'],
                                                $flex['full_name'],
                                                $flex['day_of_week'] !== null ? (string)$flex['day_of_week'] : '',
                                                $flex['schedule_date'],
                                                $flex['start_time'],
                                                $flex['end_time'],
                                                $flex['notes'] ?? '',
                                                $flex['repeat_until'] ?? '',
                                                $flex['contract_end_date'] ?? ''
                                            ]), ENT_QUOTES, 'UTF-8'); ?>" onclick="openFlexibleScheduleEditFromButton(this);">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="delete_flex_id" value="<?php echo $flex['id']; ?>">
                                                <button type="submit" name="delete_flexible" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-inbox"></i> No flexible schedules created yet.
                                    </td>
                                </tr>
                        <?php
                            endif;
                        } catch (Exception $e) {
                        ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-inbox"></i> No flexible schedules created yet.
                                    </td>
                                </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>


    <!-- MODALS -->
    <!-- Create Shift Modal -->
    <div id="createShiftModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Create New Shift</h2>
                <button class="modal-close" onclick="closeModal('createShiftModal')">&times;</button>
            </div>
            <form id="createShiftForm" method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shift_name"><i class="fas fa-briefcase"></i> Shift Name *</label>
                        <input type="text" id="shift_name" name="shift_name" required placeholder="e.g., Morning Shift">
                    </div>
                    <div class="form-group">
                        <label for="start_time"><i class="fas fa-sign-in-alt"></i> Start Time *</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time"><i class="fas fa-sign-out-alt"></i> End Time *</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="break_duration"><i class="fas fa-hourglass-half"></i> Break Duration (minutes)</label>
                        <input type="number" id="break_duration" name="break_duration" min="0" max="480" value="60">
                    </div>
                    <div class="form-group">
                        <label for="description"><i class="fas fa-file-alt"></i> Description</label>
                        <textarea id="description" name="description" placeholder="Enter shift description (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span><i class="fas fa-check"></i> Active</span>
                        </label>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 12px; background: #f0f8ff; padding: 15px; border-radius: 6px; border-left: 4px solid #2196F3;">
                        <input type="checkbox" id="create_exclude_saturday" name="exclude_saturday" style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="create_exclude_saturday" style="margin: 0; cursor: pointer; flex: 1;">
                            <strong style="color: #1565c0;">Exclude Saturdays?</strong>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">Check this if the shift does not operate on Saturdays</p>
                        </label>
                    </div>

                    <div class="modal-action-row">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('createShiftModal')">Cancel</button>
                        <button type="submit" form="createShiftForm" name="create_shift" class="btn btn-primary"><i class="fas fa-save"></i> Create Shift</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Shift Modal -->
    <div id="editShiftModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header edit-modal-header">
                <div>
                    <span class="edit-modal-eyebrow">SHIFT MANAGEMENT</span>
                    <h2><i class="fas fa-edit"></i> Edit Shift</h2>
                    <p>Update the shift details and availability.</p>
                </div>
                <button class="modal-close" onclick="closeModal('editShiftModal')">&times;</button>
            </div>
            <form id="editShiftForm" method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <input type="hidden" id="edit_shift_id" name="shift_id">
                    <div class="edit-form-section">
                        <div class="edit-section-title"><i class="fas fa-sliders-h"></i><span>Shift details</span></div>
                    <div class="edit-form-grid">
                    <div class="form-group edit-form-full">
                        <label for="edit_shift_name"><i class="fas fa-briefcase"></i> Shift Name *</label>
                        <input type="text" id="edit_shift_name" name="shift_name" required placeholder="e.g., Morning Shift">
                    </div>
                    <div class="form-group">
                        <label for="edit_start_time"><i class="fas fa-sign-in-alt"></i> Start Time *</label>
                        <input type="time" id="edit_start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_end_time"><i class="fas fa-sign-out-alt"></i> End Time *</label>
                        <input type="time" id="edit_end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_break_duration"><i class="fas fa-hourglass-half"></i> Break Duration (minutes)</label>
                        <input type="number" id="edit_break_duration" name="break_duration" min="0" max="480">
                    </div>
                    <div class="form-group edit-form-full">
                        <label for="edit_description"><i class="fas fa-file-alt"></i> Description</label>
                        <textarea id="edit_description" name="description" placeholder="Enter shift description (optional)"></textarea>
                    </div>
                    </div>
                    </div>
                    <div class="edit-form-section edit-options-section">
                        <div class="edit-section-title"><i class="fas fa-toggle-on"></i><span>Availability</span></div>
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" id="edit_is_active" name="is_active">
                            <span><i class="fas fa-check"></i> Active</span>
                        </label>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 12px; background: #f0f8ff; padding: 15px; border-radius: 6px; border-left: 4px solid #2196F3;">
                        <input type="checkbox" id="edit_exclude_saturday" name="exclude_saturday" style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="edit_exclude_saturday" style="margin: 0; cursor: pointer; flex: 1;">
                            <strong style="color: #1565c0;">Exclude Saturdays?</strong>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">Check this if the shift does not operate on Saturdays</p>
                        </label>
                    </div>
                    </div>

                    <div class="modal-action-row">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editShiftModal')">Cancel</button>
                        <button type="submit" form="editShiftForm" name="update_shift" class="btn btn-primary"><i class="fas fa-save"></i> Update Shift</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignment Modal -->
    <div id="assignmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-check"></i> Assign Shift to Employees</h2>
                <button class="modal-close" onclick="closeModal('assignmentModal')">&times;</button>
            </div>
            <form method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <!-- Search and Filter Section -->
                    <div id="employeeSearchFilterContainer" style="display: flex; gap: 10px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px; position: relative;">
                            <input 
                                type="text" 
                                id="employeeSearchInput" 
                                placeholder="Search employees..." 
                                style="width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px;"
                                oninput="filterEmployeeList(this.value)"
                            >
                            <div id="selectedEmployeeDisplay" style="display:none; padding: 10px 12px; border: 2px solid #e0e0e0; border-radius:6px; background:#fff; font-weight:600;">
                                <!-- Filled dynamically in edit mode -->
                            </div>
                        </div>
                        <button id="employeeSearchButton" type="button" class="btn btn-info" onclick="searchEmployees()" title="Search" style="padding: 10px 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-search"></i>
                        </button>
                        <select id="employeeFilterStatus" style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; cursor: pointer;" onchange="filterEmployeeByStatus(this.value)">
                            <option value="">All Employees</option>
                            <option value="assigned">Assigned</option>
                            <option value="unassigned">Unassigned</option>
                        </select>
                    </div>

                    <!-- Multi-Select Employee List -->
                    <div class="form-group">
                        <label id="employeeMultiLabel"><i class="fas fa-users"></i> Select Employees (Multi-select) *</label>
                        <div id="employeeListContainer" style="border: 2px solid #e0e0e0; border-radius: 6px; max-height: 300px; overflow-y: auto; background: #f9f9f9; display: none;">
                            <div id="employeeCheckboxList" style="padding: 10px;">
                                <!-- Checkboxes will be populated by JavaScript -->
                            </div>
                        </div>
                        <div id="selectedSummary" style="color: #666; margin-top: 8px; display: block;">
                            <strong>Selected:</strong> <span id="selectedCount">0</span> employee(s)
                        </div>
                    </div>

                    <!-- Shift Selection -->
                    <div class="form-group">
                        <label for="shift_id"><i class="fas fa-briefcase"></i> Shift *</label>
                        <select id="shift_id" name="shift_id" required>
                            <option value="">Select a shift...</option>
                            <?php foreach ($shifts as $shift): ?>
                                <option value="<?php echo $shift['shift_id']; ?>">
                                    <?php echo htmlspecialchars($shift['shift_name']); ?> 
                                    (<?php echo date('g:i A', strtotime($shift['start_time'])); ?> - 
                                    <?php echo date('g:i A', strtotime($shift['end_time'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date Selection -->
                    <div class="form-group">
                        <label for="effective_from"><i class="fas fa-calendar-check"></i> Effective From *</label>
                        <input type="date" id="effective_from" name="effective_from" required value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="effective_to"><i class="fas fa-calendar-times"></i> Effective To (Optional)</label>
                        <input type="date" id="effective_to" name="effective_to">
                    </div>

                    <!-- No Saturday Checkbox -->
                    <div class="form-group" style="display: flex; align-items: center; gap: 12px; background: #f0f8ff; padding: 15px; border-radius: 6px; border-left: 4px solid #2196F3;">
                        <input 
                            type="checkbox" 
                            id="exclude_saturday" 
                            name="exclude_saturday"
                            style="width: 20px; height: 20px; cursor: pointer;"
                        >
                        <label for="exclude_saturday" style="margin: 0; cursor: pointer; flex: 1;">
                            <strong style="color: #1565c0;">No Saturday?</strong>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
                                Check this to exclude Saturdays from the shift assignment
                            </p>
                        </label>
                    </div>

                    <!-- Hidden field to store selected employees -->
                    <input type="hidden" id="selected_employees" name="selected_employees" value="">

                    <div class="modal-action-row">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('assignmentModal')">Cancel</button>
                        <button type="button" id="assignmentModalActionButton" class="btn btn-primary" onclick="handleAssignmentModalAction()" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-check"></i> Assign to Selected
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Generate Fixed Schedule Modal -->
    <div id="generateFixedModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-alt"></i> Generate Fixed Schedule</h2>
                <button class="modal-close" onclick="closeModal('generateFixedModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="gf_employee_search"><i class="fas fa-user"></i> Employee</label>
                    <input type="text" id="gf_employee_search" placeholder="Search employee name or ID" oninput="filterFixedEmployeeSuggestions(this.value)" autocomplete="off" style="width: 100%;">
                    <div id="gf_employee_suggestions" style="display: none; position: static; width: 100%; margin-top: 8px; max-height: 220px; overflow-y: auto; border: 1px solid #d1d5db; background: #fff; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                    </div>
                    <div id="gf_selected_employee_display" style="margin-top: 10px; color: #444; font-weight: 600;">No employee selected</div>
                    <input type="hidden" id="gf_employee_id" name="gf_employee_id">
                </div>

                <div class="form-group" style="margin: 8px 0 14px;">
                    <button type="button" id="gf_show_unassigned_button" class="btn btn-secondary" onclick="toggleGfUnassignedEmployees()" style="width: 100%;">Show unassigned employees</button>
                </div>

                <div id="gf_unassigned_employees" style="display: none; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; max-height: 220px; overflow-y: auto; background: #fff; margin-bottom: 14px;"></div>

                <div class="form-group">
                    <label for="gf_start_date"><i class="fas fa-calendar-day"></i> Schedule Start</label>
                    <input type="date" id="gf_start_date" name="gf_start_date">
                </div>

                <div class="form-group">
                    <label for="gf_end_date"><i class="fas fa-calendar-day"></i> Schedule End</label>
                    <input type="date" id="gf_end_date" name="gf_end_date">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar-week"></i> Days of Week</label>
                    <div style="display: grid; gap: 10px;">
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_1_enabled" onchange="toggleGfDayRow(1)" style="width: 16px; height: 16px;"> Monday</label>
                            <div id="gf_day_1_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_1_start">Start</label>
                                    <input type="time" id="gf_day_1_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_1_end">End</label>
                                    <input type="time" id="gf_day_1_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_1_break_start">Break Start</label>
                                    <input type="time" id="gf_day_1_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_1_break_end">Break End</label>
                                    <input type="time" id="gf_day_1_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_2_enabled" onchange="toggleGfDayRow(2)" style="width: 16px; height: 16px;"> Tuesday</label>
                            <div id="gf_day_2_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_2_start">Start</label>
                                    <input type="time" id="gf_day_2_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_2_end">End</label>
                                    <input type="time" id="gf_day_2_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_2_break_start">Break Start</label>
                                    <input type="time" id="gf_day_2_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_2_break_end">Break End</label>
                                    <input type="time" id="gf_day_2_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_3_enabled" onchange="toggleGfDayRow(3)" style="width: 16px; height: 16px;"> Wednesday</label>
                            <div id="gf_day_3_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_3_start">Start</label>
                                    <input type="time" id="gf_day_3_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_3_end">End</label>
                                    <input type="time" id="gf_day_3_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_3_break_start">Break Start</label>
                                    <input type="time" id="gf_day_3_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_3_break_end">Break End</label>
                                    <input type="time" id="gf_day_3_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_4_enabled" onchange="toggleGfDayRow(4)" style="width: 16px; height: 16px;"> Thursday</label>
                            <div id="gf_day_4_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_4_start">Start</label>
                                    <input type="time" id="gf_day_4_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_4_end">End</label>
                                    <input type="time" id="gf_day_4_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_4_break_start">Break Start</label>
                                    <input type="time" id="gf_day_4_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_4_break_end">Break End</label>
                                    <input type="time" id="gf_day_4_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_5_enabled" onchange="toggleGfDayRow(5)" style="width: 16px; height: 16px;"> Friday</label>
                            <div id="gf_day_5_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_5_start">Start</label>
                                    <input type="time" id="gf_day_5_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_5_end">End</label>
                                    <input type="time" id="gf_day_5_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_5_break_start">Break Start</label>
                                    <input type="time" id="gf_day_5_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_5_break_end">Break End</label>
                                    <input type="time" id="gf_day_5_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                        <div class="gf-day-row">
                            <label><input type="checkbox" id="gf_day_6_enabled" onchange="toggleGfDayRow(6)" style="width: 16px; height: 16px;"> Saturday</label>
                            <div id="gf_day_6_controls" class="gf-day-controls">
                                <div class="gf-time-field">
                                    <label for="gf_day_6_start">Start</label>
                                    <input type="time" id="gf_day_6_start" placeholder="Start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_6_end">End</label>
                                    <input type="time" id="gf_day_6_end" placeholder="End">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_6_break_start">Break Start</label>
                                    <input type="time" id="gf_day_6_break_start" placeholder="Break start">
                                </div>
                                <div class="gf-time-field">
                                    <label for="gf_day_6_break_end">Break End</label>
                                    <input type="time" id="gf_day_6_break_end" placeholder="Break end">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-action-row">
                <button type="button" class="btn btn-secondary" onclick="closeModal('generateFixedModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitGenerateFixed()"><i class="fas fa-check"></i> Generate Schedule</button>
            </div>
        </div>
    </div>

    <!-- Flexible schedule UI deprecated in this view. -->
            </div>
        </div>
    </div>

    <style>
        /* Keep modal constrained to the content area (not covering the sidebar)
           Make the nearest content wrapper a positioned container and
           render modals absolutely inside it so the overlay only covers
           the main content region. */
        .content-wrapper { position: relative; }

        .modal {
            position: absolute; /* cover only the content wrapper */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 680px;
            width: min(95vw, 680px);
            max-height: calc(100vh - 16px);
            overflow: hidden;
            animation: slideIn 0.3s ease;
            display: flex;
            flex-direction: column;
            /* Keep the dialog fixed and centered in viewport so it doesn't move while scrolling */
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 100000; /* on top of overlay */
        }

        .modal-body {
            padding: 28px;
            overflow-y: auto;
            overscroll-behavior: contain;
            flex: 1 1 auto;
            min-height: 0;
            max-height: calc(100vh - 220px);
        }

        #generateFixedModal .form-group input,
        #generateFixedModal .form-group select,
        #generateFixedModal .form-group textarea {
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: none;
            background: #fff;
        }

        body.modal-open {
            overflow: hidden;
            height: 100%;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 28px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
        }

        .edit-modal-header {
            padding: 24px 28px 22px;
            align-items: flex-start;
        }

        .edit-modal-header h2 {
            margin-top: 4px;
        }

        .edit-modal-header p {
            margin: 8px 0 0 36px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 12px;
        }

        .edit-modal-eyebrow {
            display: block;
            margin-left: 36px;
            color: #9ed1ff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.4px;
        }

        .edit-form-section {
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #e5edf6;
            border-radius: 12px;
            background: #fbfdff;
        }

        .edit-options-section {
            background: #f7fbff;
        }

        .edit-options-section .edit-option-toggle {
            margin-bottom: 8px;
        }

        .edit-options-section .edit-option-toggle + .form-group {
            margin-top: -2px;
        }

        .edit-options-section .edit-option-toggle label {
            padding: 9px 12px;
            border: 1px solid #d8e8f7;
            border-radius: 8px;
            background: #ffffff;
        }

        .edit-options-section .edit-option-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #0066cc;
        }

        .modal .checkbox-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            margin: 0;
            padding: 9px 12px;
            border: 1px solid #d8e8f7;
            border-radius: 8px;
            background: #ffffff;
            cursor: pointer;
        }

        .modal .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #0066cc;
        }

        .modal .checkbox-group span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 0;
        }

        #generateFixedModal .gf-day-row {
            display: grid;
            gap: 4px;
            padding: 4px 0;
        }

        #generateFixedModal .gf-day-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            margin: 0;
        }

        #generateFixedModal .gf-day-controls {
            display: none;
            gap: 8px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-flow: row;
            align-items: flex-start;
        }

        #generateFixedModal .gf-time-field {
            display: grid;
            gap: 4px;
            min-width: 144px;
        }

        #generateFixedModal .gf-time-field label {
            font-size: 12px;
            color: #5f6c7b;
            margin-bottom: 2px;
        }

        #generateFixedModal .gf-day-controls input[type="time"] {
            width: 100%;
        }

        body.dark-mode .modal .checkbox-group {
            border-color: #3b4b5d;
            background: #263442;
        }

        body.dark-mode .edit-options-section .edit-option-toggle label {
            border-color: #3b4b5d;
            background: #263442;
        }

        .edit-section-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 16px;
            color: #164b80;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .edit-section-title i {
            display: grid;
            place-items: center;
            width: 27px;
            height: 27px;
            border-radius: 8px;
            background: #e4f1ff;
            color: #0066cc;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 16px;
        }

        .edit-form-grid .form-group {
            margin-bottom: 16px;
        }

        .edit-form-full {
            grid-column: 1 / -1;
        }

        .employee-picker-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto 170px;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .employee-picker-toolbar input,
        .employee-picker-toolbar select {
            width: 100%;
            min-width: 0;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 7px;
            font: inherit;
        }

        .employee-picker-toolbar .btn-info {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 10px 14px;
            background: #138496;
            color: #fff;
        }

        .employee-picker-list {
            display: none;
            max-height: 190px;
            overflow-y: auto;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .employee-picker-selected {
            display: block;
            margin-top: 8px;
            color: #666;
        }

        body.dark-mode .employee-picker-toolbar input,
        body.dark-mode .employee-picker-toolbar select,
        body.dark-mode .employee-picker-list {
            background: #2a2a2a;
            border-color: #444;
            color: #e8e8e8;
        }

        .edit-form-section .form-group:last-child {
            margin-bottom: 0;
        }

        .edit-form-section .checkbox-group,
        .edit-form-section > .form-group[style*="background"] {
            border-radius: 10px !important;
        }

        body.dark-mode .edit-form-section {
            border-color: #3b4b5d;
            background: #202b36;
        }

        body.dark-mode .edit-options-section {
            background: #1d2a38;
        }

        body.dark-mode .edit-section-title {
            color: #a9d5ff;
        }

        body.dark-mode .edit-section-title i {
            background: #263f59;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: white;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            opacity: 0.8;
        }

        .modal-footer {
            display: none;
        }

        .modal-action-row {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 16px;
            margin-top: 10px;
            border-top: 1px solid #e0e0e0;
        }

        body.dark-mode .modal-content {
            background: #1e1e1e;
            color: #ffffff;
        }

        body.dark-mode .modal-header {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            border-bottom-color: #333;
        }

        body.dark-mode .modal-action-row {
            border-top-color: #333;
        }

        body.dark-mode .modal-close {
            color: white;
        }

        /* Form styling within modals */
        .modal-body .form-group {
            margin-bottom: 20px;
        }

        .modal-body .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #2c3e50;
        }

        body.dark-mode .modal-body .form-group label {
            color: #e8e8e8;
        }

        .modal-body .form-group input[type="text"],
        .modal-body .form-group input[type="email"],
        .modal-body .form-group input[type="date"],
        .modal-body .form-group input[type="time"],
        .modal-body .form-group input[type="number"],
        .modal-body .form-group select,
        .modal-body .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .modal-body .form-group input:focus,
        .modal-body .form-group select:focus,
        .modal-body .form-group textarea:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        body.dark-mode .modal-body .form-group input,
        body.dark-mode .modal-body .form-group select,
        body.dark-mode .modal-body .form-group textarea {
            background: #2a2a2a;
            border-color: #444;
            color: #e8e8e8;
        }

        .modal-body .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal .btn {
            padding: 10px 24px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal .btn-primary {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
        }

        .modal .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
        }

        .modal .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .modal .btn-secondary:hover {
            background: #d0d0d0;
        }

        body.dark-mode .modal .btn-secondary {
            background: #444;
            color: #e8e8e8;
        }

        body.dark-mode .modal .btn-secondary:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .modal {
                padding: 12px 0;
                align-items: flex-start;
            }

            .modal-content {
                max-width: 95vw;
                max-height: 95vh;
                width: 100%;
            }

            .modal-header {
                padding: 16px;
            }

            .modal-header h2 {
                font-size: 16px;
            }

            .edit-modal-header {
                padding: 18px 16px;
            }

            .edit-modal-header p,
            .edit-modal-eyebrow {
                margin-left: 30px;
            }

            .modal-body {
                padding: 16px;
                max-height: calc(100vh - 230px);
            }

            .modal-footer {
                padding: 12px 16px;
                flex-direction: column;
                gap: 10px;
            }

            .modal .btn {
                width: 100%;
            }

            .edit-form-grid {
                grid-template-columns: 1fr;
            }

            .edit-form-full {
                grid-column: auto;
            }

            .edit-form-section {
                padding: 14px;
            }

            .employee-picker-toolbar {
                grid-template-columns: 1fr auto;
            }

            .employee-picker-toolbar select {
                grid-column: 1 / -1;
            }
        }
    </style>

    <script>
        function openModal(modalId) {
            try {
                const modal = document.getElementById(modalId);
                console.log('openModal called for', modalId, 'exists:', !!modal);
                if (!modal) return;

                // Ensure modal is appended to the document (so fixed positioning covers viewport)
                const contentWrapper = document.querySelector('.content-wrapper') || document.body;
                if (modal.parentElement !== document.body) {
                    try { document.body.appendChild(modal); } catch (e) { /* ignore */ }
                }

                // Compute the area of the content wrapper so the overlay only covers main content
                const rect = contentWrapper.getBoundingClientRect();

                // Apply fixed positioning to keep modal in place while allowing page scroll
                modal.style.position = 'fixed';
                modal.style.left = rect.left + 'px';
                modal.style.top = rect.top + 'px';
                modal.style.width = rect.width + 'px';
                modal.style.height = rect.height + 'px';
                modal.style.display = 'flex';
                // Ensure visible stacking
                modal.style.zIndex = '99999';
                document.body.classList.add('modal-open');

                // Update handler to reposition overlay on resize/scroll
                const handler = () => {
                    const r = contentWrapper.getBoundingClientRect();
                    modal.style.left = r.left + 'px';
                    modal.style.top = r.top + 'px';
                    modal.style.width = r.width + 'px';
                    modal.style.height = r.height + 'px';
                };
                // store handler so we can remove later
                modal.__overlayHandler = handler;
                window.addEventListener('resize', handler);
                // use capture on scroll to respond when containers scroll
                window.addEventListener('scroll', handler, true);
            } catch (err) {
                console.error('openModal error:', err);
            }
        }

        function closeModal(modalId) {
            try {
                const modal = document.getElementById(modalId);
                console.log('closeModal called for', modalId, 'exists:', !!modal);
                if (!modal) return;

                modal.style.display = 'none';
                document.body.classList.remove('modal-open');

                // remove overlay handlers if present
                if (modal.__overlayHandler) {
                    window.removeEventListener('resize', modal.__overlayHandler);
                    window.removeEventListener('scroll', modal.__overlayHandler, true);
                    delete modal.__overlayHandler;
                }

                // Reset any forms inside the modal
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => form.reset());

                // Reset inline positioning so modal returns to default layout if reopened differently
                modal.style.position = 'absolute';
                modal.style.left = '0';
                modal.style.top = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.zIndex = '';
                // If closing assignment modal, reset edit mode
                if (modalId === 'assignmentModal') {
                    assignmentMode = 'create';
                    selectedEmployeeForEdit = null;
                    const actionBtn = document.getElementById('assignmentModalActionButton');
                    const headerTitle = document.querySelector('#assignmentModal .modal-header h2');
                    const container = document.getElementById('employeeListContainer');
                    if (headerTitle) headerTitle.innerHTML = '<i class="fas fa-user-check"></i> Assign Shift to Employees';
                    if (actionBtn) { actionBtn.innerHTML = '<i class="fas fa-check"></i> Assign to Selected'; actionBtn.dataset.action = 'assign'; }
                    if (container) container.style.display = 'none';
                    const searchFilterContainer = document.getElementById('employeeSearchFilterContainer');
                    const selectedDisplay = document.getElementById('selectedEmployeeDisplay');
                    if (searchFilterContainer) searchFilterContainer.style.display = '';
                    if (selectedDisplay) selectedDisplay.style.display = 'none';
                }
            } catch (err) {
                console.error('closeModal error:', err);
            }
        }

        // ============ MULTI-SELECT EMPLOYEE FUNCTIONS ============
        let allEmployees = [];
        let filteredEmployees = [];
        let selectedEmployees = new Set();
        // assignmentMode: 'create' = multi-assign flow, 'edit' = editing a single employee
        let assignmentMode = 'create';
        let selectedEmployeeForEdit = null;

        // Load employees when modal opens
        function loadEmployeeList() {
            fetch('../app/api/get_employees_for_shift.php')
                .then(response => response.text())
                .then(text => {
                    let data;
                    try { data = JSON.parse(text); } catch (err) { console.error('get_employees_for_shift returned non-JSON:', text); throw err; }
                    if (data.success) {
                        allEmployees = data.employees;
                        filteredEmployees = [...allEmployees];
                        renderEmployeeCheckboxes(filteredEmployees);
                    }
                })
                .catch(error => console.error('Error loading employees:', error));
        }

        // Render employee checkboxes
        function renderEmployeeCheckboxes(employees) {
            const container = document.getElementById('employeeCheckboxList');
            container.innerHTML = '';

            employees.forEach(emp => {
                const isChecked = selectedEmployees.has(emp.employee_id);
                const checkboxHTML = `
                    <div style="padding: 12px 10px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px;">
                        <input 
                            type="checkbox" 
                            id="emp_${emp.employee_id}" 
                            value="${emp.employee_id}" 
                            class="employee-checkbox"
                            ${isChecked ? 'checked' : ''}
                            onchange="updateSelectedEmployees()"
                            style="width: 18px; height: 18px; cursor: pointer;"
                        >
                        <label for="emp_${emp.employee_id}" style="flex: 1; cursor: pointer; margin: 0; display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 600; color: #333;">${escapeHtml(emp.full_name)}</span>
                            <span style="color: #999; font-size: 12px;">${escapeHtml(emp.department || 'N/A')}</span>
                            ${emp.has_shift ? '<span style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">ASSIGNED</span>' : '<span style="background: #FF9800; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">UNASSIGNED</span>'}
                        </label>
                    </div>
                `;
                container.innerHTML += checkboxHTML;
            });

            if (employees.length === 0) {
                container.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No employees found</div>';
            }
        }

        // Update selected employees and count
        function updateSelectedEmployees() {
            selectedEmployees.clear();
            document.querySelectorAll('.employee-checkbox:checked').forEach(checkbox => {
                selectedEmployees.add(checkbox.value);
            });
            const selectedCountEl = document.getElementById('selectedCount');
            if (selectedCountEl) selectedCountEl.textContent = selectedEmployees.size;
        }

        // Filter employees by search term
        function filterEmployeeList(searchTerm) {
            const term = safeLower(searchTerm || '').trim();
            const container = document.getElementById('employeeListContainer');
            if (!container) return;

            filteredEmployees = allEmployees.filter(emp => 
                safeLower(emp.full_name).includes(term) || 
                safeLower(emp.department).includes(term)
            );

            // Show container only if there's search text
            if (term.length > 0) {
                container.style.display = 'block';
                renderEmployeeCheckboxes(filteredEmployees);
            } else {
                container.style.display = 'none';
            }
            
            // Re-check previously selected employees
            selectedEmployees.forEach(empId => {
                const checkbox = document.getElementById('emp_' + empId);
                if (checkbox) checkbox.checked = true;
            });
        }

        // Filter employees by assignment status
        function filterEmployeeByStatus(status) {
            const container = document.getElementById('employeeListContainer');
            
            if (status === '') {
                container.style.display = 'none';
                filteredEmployees = [...allEmployees];
            } else if (status === 'assigned') {
                container.style.display = 'block';
                filteredEmployees = allEmployees.filter(emp => emp.has_shift);
            } else if (status === 'unassigned') {
                container.style.display = 'block';
                filteredEmployees = allEmployees.filter(emp => !emp.has_shift);
            }
            renderEmployeeCheckboxes(filteredEmployees);
            // Re-check previously selected employees
            selectedEmployees.forEach(empId => {
                const checkbox = document.getElementById('emp_' + empId);
                if (checkbox) checkbox.checked = true;
            });
        }

        // Search employees
        function searchEmployees() {
            const searchTerm = document.getElementById('employeeSearchInput').value;
            const container = document.getElementById('employeeListContainer');
            
            if (searchTerm.length > 0) {
                container.style.display = 'block';
                filterEmployeeList(searchTerm);
            } else {
                container.style.display = 'none';
            }
        }

        // Assign shift to multiple employees
        function assignMultipleEmployees() {
            if (selectedEmployees.size === 0) {
                alert('Please select at least one employee');
                return;
            }

            const shiftId = document.getElementById('shift_id').value;
            const effectiveFrom = document.getElementById('effective_from').value;
            const effectiveTo = document.getElementById('effective_to').value;
            const excludeSaturday = document.getElementById('exclude_saturday').checked;

            if (!shiftId) {
                alert('Please select a shift');
                return;
            }

            if (!effectiveFrom) {
                alert('Please select an effective from date');
                return;
            }

            // Send request to assign shift to multiple employees
            const formData = new FormData();
            formData.append('action', 'assign_multiple');
            formData.append('employee_ids', JSON.stringify(Array.from(selectedEmployees)));
            formData.append('shift_id', shiftId);
            formData.append('effective_from', effectiveFrom);
            formData.append('effective_to', effectiveTo || null);
            formData.append('exclude_saturday', excludeSaturday ? '1' : '0');

            fetch('../app/api/assign_shift_multiple.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                let data;
                try { data = JSON.parse(text); } catch (err) { console.error('assign_shift_multiple returned non-JSON:', text); throw err; }
                if (data.success) {
                    alert('Shifts assigned successfully to ' + selectedEmployees.size + ' employee(s)');
                    closeModal('assignmentModal');
                    location.reload(); // Reload to see updates
                } else {
                    alert('Error: ' + (data.message || 'Failed to assign shifts'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning shifts: ' + error.message);
            });
        }

        // Generic handler for assignment modal primary action
        function handleAssignmentModalAction() {
            const actionBtn = document.getElementById('assignmentModalActionButton');
            const mode = actionBtn && actionBtn.dataset && actionBtn.dataset.action ? actionBtn.dataset.action : (assignmentMode === 'edit' ? 'update' : 'assign');
            if (mode === 'assign') {
                assignMultipleEmployees();
            } else {
                updateEmployeeAssignment();
            }
        }

        // Update assignment for a single employee (frontend-only; backend endpoint may need to support this action)
        function updateEmployeeAssignment() {
            if (!selectedEmployeeForEdit) {
                alert('No employee selected for update');
                return;
            }

            const shiftId = document.getElementById('shift_id').value;
            const effectiveFrom = document.getElementById('effective_from').value;
            const effectiveTo = document.getElementById('effective_to').value;
            const excludeSaturday = document.getElementById('exclude_saturday').checked;

            if (!shiftId) {
                alert('Please select a shift');
                return;
            }

            if (!effectiveFrom) {
                alert('Please select an effective from date');
                return;
            }

            const formData = new FormData();
            formData.append('employee_id', selectedEmployeeForEdit);
            formData.append('shift_id', shiftId);
            formData.append('effective_from', effectiveFrom);
            formData.append('effective_to', effectiveTo || null);
            formData.append('exclude_saturday', excludeSaturday ? '1' : '0');

            fetch('../app/api/update_employee_assignment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                let data;
                try { data = JSON.parse(text); } catch (err) { console.error('assign_shift_multiple(update) returned non-JSON:', text); throw err; }
                if (data.success) {
                    alert('Assignment updated successfully');
                    closeModal('assignmentModal');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update assignment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating assignment: ' + error.message);
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text || '').replace(/[&<>"']/g, m => map[m] || m);
        }

        // Initialize employee list when modal opens
        const originalOpenModal = openModal;
        openModal = function(modalId) {
            if (modalId === 'assignmentModal') {
                loadEmployeeList();
                const actionBtn = document.getElementById('assignmentModalActionButton');
                const headerTitle = document.querySelector('#assignmentModal .modal-header h2');
                const container = document.getElementById('employeeListContainer');
                const searchFilterContainer = document.getElementById('employeeSearchFilterContainer');

                const selectedDisplay = document.getElementById('selectedEmployeeDisplay');
                const searchInput = document.getElementById('employeeSearchInput');
                const multiLabel = document.getElementById('employeeMultiLabel');
                const selectedSummaryEl = document.getElementById('selectedSummary');
                if (assignmentMode === 'create') {
                    selectedEmployees.clear();
                    const selectedCountEl = document.getElementById('selectedCount');
                    if (selectedCountEl) selectedCountEl.textContent = '0';
                    if (container) container.style.display = 'none';
                    if (searchFilterContainer) searchFilterContainer.style.display = '';
                    if (searchInput) searchInput.style.display = '';
                    if (selectedDisplay) selectedDisplay.style.display = 'none';
                    if (multiLabel) multiLabel.style.display = '';
                    if (selectedSummaryEl) selectedSummaryEl.innerHTML = '<strong>Selected:</strong> <span id="selectedCount">0</span> employee(s)';
                    if (headerTitle) headerTitle.innerHTML = '<i class="fas fa-user-check"></i> Assign Shift to Employees';
                    if (actionBtn) { actionBtn.innerHTML = '<i class="fas fa-check"></i> Assign to Selected'; actionBtn.dataset.action = 'assign'; }
                } else if (assignmentMode === 'edit') {
                    // preselect single employee and hide the multi-select list
                    selectedEmployees = new Set([selectedEmployeeForEdit]);
                    const selectedCountEl = document.getElementById('selectedCount');
                    if (selectedCountEl) selectedCountEl.textContent = '1';
                    if (container) container.style.display = 'none';
                    if (searchFilterContainer) searchFilterContainer.style.display = 'none';
                    if (searchInput) searchInput.style.display = 'none';
                    if (selectedDisplay) selectedDisplay.style.display = 'block';
                    if (multiLabel) multiLabel.style.display = 'none';
                    if (headerTitle) headerTitle.innerHTML = '<i class="fas fa-user-edit"></i> Edit Employee Assignment';
                    if (actionBtn) { actionBtn.innerHTML = '<i class="fas fa-save"></i> Update Assignment'; actionBtn.dataset.action = 'update'; }
                    const selField = document.getElementById('selected_employees');
                    if (selField) selField.value = JSON.stringify([selectedEmployeeForEdit]);
                    // Prefill form fields from normalized assignment data if available and show selected employee
                    try {
                        const emp = (employeeAssignmentData || []).find(e => String(e.employee_id) === String(selectedEmployeeForEdit));
                        const selectedDisplay = document.getElementById('selectedEmployeeDisplay');
                        if (selectedDisplay) {
                            selectedDisplay.textContent = emp ? (emp.employee || emp.full_name || emp.employee_id) : ('Employee ID: ' + selectedEmployeeForEdit);
                        }
                        if (emp && emp.assignments && emp.assignments.length > 0) {
                            const activeAssign = emp.assignments.find(a => a.isActive) || emp.assignments[0];
                            if (activeAssign) {
                                const shiftSel = document.getElementById('shift_id');
                                if (shiftSel && activeAssign.shift_id) shiftSel.value = activeAssign.shift_id;
                                const effFrom = document.getElementById('effective_from');
                                if (effFrom && activeAssign.effective_from) effFrom.value = (activeAssign.effective_from || '').split(' ')[0];
                                const effTo = document.getElementById('effective_to');
                                if (effTo) effTo.value = activeAssign.effective_to ? (activeAssign.effective_to.split ? activeAssign.effective_to.split(' ')[0] : activeAssign.effective_to) : '';
                                const excl = document.getElementById('exclude_saturday');
                                if (excl) {
                                    excl.checked = !!activeAssign.exclude_saturday || false;
                                }
                            }
                        }
                    } catch (err) {
                        console.error('Error pre-filling assignment edit form:', err);
                    }
                }
            } else if (modalId === 'generateFixedModal') {
                loadFixedScheduleEmployees();
                const suggestions = document.getElementById('gf_employee_suggestions');
                if (suggestions) suggestions.style.display = 'none';
            }
            originalOpenModal(modalId);
        };

        function openCreateShiftModal() {
            // Reset the form completely
            const form = document.querySelector('#createShiftModal .shift-form');
            if (form) {
                // Clear all input fields explicitly
                document.getElementById('shift_name').value = '';
                document.getElementById('start_time').value = '';
                document.getElementById('end_time').value = '';
                document.getElementById('break_duration').value = '60';
                document.getElementById('description').value = '';
                
                // Reset checkboxes
                document.querySelector('#createShiftModal input[name="is_active"]').checked = true;
                document.getElementById('create_exclude_saturday').checked = false;
            }
            // Open the modal
            openModal('createShiftModal');
        }

        function openEditShiftModalSafe(shiftId, shiftName, startTime, endTime, breakDuration, description, isActive, excludeSaturday) {
            try {
                // Ensure values are the correct type
                const params = {
                    shiftId: parseInt(shiftId),
                    shiftName: String(shiftName),
                    startTime: String(startTime),
                    endTime: String(endTime),
                    breakDuration: parseInt(breakDuration) || 0,
                    description: String(description),
                    isActive: Boolean(isActive && isActive !== 'false'),
                    excludeSaturday: Boolean(excludeSaturday && excludeSaturday !== 'false')
                };
                
                // Populate the edit modal with current values
                const editForm = document.getElementById('editShiftModal');
                if (!editForm) {
                    console.error('Edit shift modal not found');
                    return;
                }
                
                document.getElementById('edit_shift_id').value = params.shiftId;
                document.getElementById('edit_shift_name').value = params.shiftName;
                document.getElementById('edit_start_time').value = params.startTime;
                document.getElementById('edit_end_time').value = params.endTime;
                document.getElementById('edit_break_duration').value = params.breakDuration;
                document.getElementById('edit_description').value = params.description;
                document.getElementById('edit_is_active').checked = params.isActive;
                document.getElementById('edit_exclude_saturday').checked = params.excludeSaturday;
                
                console.log('Edit modal populated:', params);
                
                // Open the modal
                openModal('editShiftModal');
            } catch (error) {
                console.error('Error opening edit modal:', error);
            }
        }

        function openEditShiftModalFromButton(button) {
            try {
                const editData = JSON.parse(button.dataset.shiftEdit);
                openEditShiftModalSafe(...editData);
            } catch (error) {
                console.error('Error reading shift data:', error);
            }
        }

        // Flexible schedule edit functions removed from this view.

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.shift-tab');
            buttons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }

            // Add active class to clicked button (only if event exists and has a target)
            if (event && event.target && event.target.classList) {
                event.target.classList.add('active');
            }
        }

        // Flexible Schedule: Toggle repeat end date field
        document.addEventListener('DOMContentLoaded', function() {
            const repeatUntilCheckbox = document.getElementById('flex_repeat_until');
            const repeatUntilContainer = document.getElementById('flex_repeat_until_container');
            const editRepeatUntilCheckbox = document.getElementById('edit_flex_repeat_until');
            const editRepeatUntilContainer = document.getElementById('edit_flex_repeat_until_container');
            
            const contractEndCheckbox = document.getElementById('flex_contract_end');
            const contractEndContainer = document.getElementById('flex_contract_end_container');
            const editContractEndCheckbox = document.getElementById('edit_flex_contract_end');
            const editContractEndContainer = document.getElementById('edit_flex_contract_end_container');

            if (repeatUntilCheckbox) {
                repeatUntilCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        repeatUntilContainer.style.display = 'block';
                        document.getElementById('flex_repeat_end_date').focus();
                    } else {
                        repeatUntilContainer.style.display = 'none';
                        document.getElementById('flex_repeat_end_date').value = '';
                    }
                });
            }

            if (editRepeatUntilCheckbox) {
                editRepeatUntilCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        editRepeatUntilContainer.style.display = 'block';
                        document.getElementById('edit_flex_repeat_end_date').focus();
                    } else {
                        editRepeatUntilContainer.style.display = 'none';
                        document.getElementById('edit_flex_repeat_end_date').value = '';
                    }
                });
            }
            
            if (contractEndCheckbox) {
                contractEndCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        contractEndContainer.style.display = 'block';
                        document.getElementById('flex_contract_end_date').focus();
                    } else {
                        contractEndContainer.style.display = 'none';
                        document.getElementById('flex_contract_end_date').value = '';
                    }
                });
            }

            if (editContractEndCheckbox) {
                editContractEndCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        editContractEndContainer.style.display = 'block';
                        document.getElementById('edit_flex_contract_end_date').focus();
                    } else {
                        editContractEndContainer.style.display = 'none';
                        document.getElementById('edit_flex_contract_end_date').value = '';
                    }
                });
            }

            // Set minimum date to today
            const dateInput = document.getElementById('flex_date');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
                dateInput.value = today;
            }

            const editDateInput = document.getElementById('edit_flex_date');
            if (editDateInput) {
                const today = new Date().toISOString().split('T')[0];
                editDateInput.setAttribute('min', today);
            }

            // Auto-switch to flexible tab if action is flexible
            const currentAction = '<?php echo $action; ?>';
            if (currentAction === 'flexible') {
                switchTab('flexible');
                const flexTab = document.querySelector('[onclick="switchTab(\'flexible\')"]');
                if (flexTab) {
                    flexTab.classList.add('active');
                }
            }
        });

        // Flexible schedule client-side features removed from this view.
    </script>

<?php
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 15 At end of page');
require_once __DIR__ . '/../layout/content_footer.php';
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 15a After content_footer.php');
require_once __DIR__ . '/../layout/page_end.php';
if (function_exists('shiftDebug')) shiftDebug('[DIAG] 15b After page_end.php');
?>
