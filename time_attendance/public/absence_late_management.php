<?php
/**
 * Absence & Late Management Interface
 * HR interface for managing absence and late arrival records
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/AbsenceLateMgmt.php";
require_once "../app/models/Employee.php";
require_once "../app/core/Session.php";
require_once "../app/config/Database.php";

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

// Check HR/Time permission
if (!AuthController::hasRole('time') && !AuthController::hasRole('hr')) {
    header("Location: employee_dashboard.php");
    exit;
}

$absenceLateMgmt = new AbsenceLateMgmt();
$employeeModel = new Employee();
$database = new Database();
$conn = $database->getConnection();

// Determine date range from period selector
$period = $_GET['period'] ?? 'today';
$customStartDate = $_GET['custom_start_date'] ?? null;
$customEndDate = $_GET['custom_end_date'] ?? null;

$today = new DateTime();
$startDate = clone $today;
$endDate = clone $today;

switch ($period) {
    case 'today':
        break; // Already set to today
    case 'yesterday':
        $startDate->modify('-1 day');
        $endDate->modify('-1 day');
        break;
    case 'this_week':
        $startDate->modify('Monday this week');
        break;
    case 'last_week':
        $startDate->modify('Monday last week');
        $endDate->modify('Sunday last week');
        break;
    case 'this_month':
        $startDate->modify('first day of this month');
        break;
    case 'last_month':
        $startDate->modify('first day of last month');
        $endDate->modify('last day of last month');
        break;
    case 'last_2_weeks':
        $startDate->modify('-14 days');
        break;
    case 'last_2_months':
        $startDate->modify('-2 months');
        break;
    case 'custom':
        if ($customStartDate && $customEndDate) {
            $startDate = new DateTime($customStartDate);
            $endDate = new DateTime($customEndDate);
        }
        break;
}

$startDateStr = $startDate->format('Y-m-d');
$endDateStr = $endDate->format('Y-m-d');

// Get employee attendance records with status
$attendanceRecords = $absenceLateMgmt->detectAbsentAndLateEmployees($startDateStr, $endDateStr);

// Group records by date for display
$recordsByDate = [];
foreach ($attendanceRecords as $record) {
    $date = $record['check_date'];
    if (!isset($recordsByDate[$date])) {
        $recordsByDate[$date] = [];
    }
    $recordsByDate[$date][] = $record;
}
krsort($recordsByDate); // Sort dates in descending order

// Count statistics
$summaryStats = $absenceLateMgmt->getSummaryStats(['start_date' => $startDateStr, 'end_date' => $endDateStr]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence & Late Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed {
            margin-left: 0;
        }

        .main-content {
            width: calc(100% - 250px);
            margin-left: 250px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            overflow-y: auto;
            transition: width 0.3s ease, margin-left 0.3s ease;
        }

        body.sidebar-collapsed .main-content {
            width: 100%;
            margin-left: 0;
        }

        .content-wrapper {
            width: 100%;
            margin: 0;
            padding: 30px 20px;
        }

        .container, .container-fluid {
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 100%;
        }

        .preloader {
            margin: 0;
            padding: 0;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 35px;
            margin-top: 0;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 0;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
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

        .stat-card::after {
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

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.15);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover::after {
            opacity: 0.08;
        }

        .stat-card h4 {
            color: #666;
            font-size: 13px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #003d82;
            position: relative;
            z-index: 1;
        }

        .stat-card.pending .stat-value {
            color: #ffc107;
        }

        .stat-card.approved .stat-value {
            color: #28a745;
        }

        .stat-card.rejected .stat-value {
            color: #dc3545;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .filter-section input,
        .filter-section select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .filter-section input:focus,
        .filter-section select:focus {
            outline: none;
            border-color: #003d82;
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        /* Records Table */
        .records-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
        }

        .records-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .records-table thead {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
        }

        .records-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .records-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e8eef7;
        }

        .records-table tbody tr {
            transition: all 0.3s ease;
        }

        .records-table tbody tr:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 61, 130, 0.05);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-absent {
            background: #ffe5e5;
            color: #dc3545;
        }

        .badge-late {
            background: #fff3cd;
            color: #ff9800;
        }

        .badge-pending {
            background: #e3f2fd;
            color: #2196f3;
        }

        .badge-approved {
            background: #e8f5e9;
            color: #28a745;
        }

        .badge-rejected {
            background: #ffebee;
            color: #dc3545;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }

        .btn-approve:hover {
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
        }

        .btn-reject:hover {
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }

        .btn-view {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 61, 130, 0.2);
        }

        .btn-view:hover {
            box-shadow: 0 6px 16px rgba(0, 61, 130, 0.4);
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1500;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e8eef7;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: #003d82;
            font-weight: 700;
        }

        .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
        }

        .modal-header .close-btn:hover {
            color: #333;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #003d82;
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e8eef7;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: 16px;
            color: #666;
        }

        @media (max-width: 768px) {
            .filter-section {
                grid-template-columns: 1fr;
            }

            .records-table table {
                font-size: 12px;
            }

            .records-table th,
            .records-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <div class="page-title">
                        <i class="fas fa-calendar-times"></i>
                        <span>Absence & Late Management</span>
                    </div>
                    <div class="page-subtitle">Review and manage employee absence and late arrival records</div>
                </div>
                <button onclick="detectAbsenceAndLate()" style="padding: 12px 20px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; align-self: flex-end;">
                    <i class="fas fa-sync-alt"></i> Detect Now
                </button>
            </div>

            <div class="absence-late-container">

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Records</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_records'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>Total Absences</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_absents'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>Total Late Arrivals</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_lates'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card pending">
                        <h4>Pending Reviews</h4>
                        <div class="stat-value"><?php echo $summaryStats['pending_reviews'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card approved">
                        <h4>Approved Excuses</h4>
                        <div class="stat-value"><?php echo $summaryStats['approved_excuses'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card rejected">
                        <h4>Rejected Excuses</h4>
                        <div class="stat-value"><?php echo $summaryStats['rejected_excuses'] ?? 0; ?></div>
                    </div>
                </div>

                <!-- Diagnostic Info -->
                <div style="background: #e3f2fd; border-left: 4px solid #1976d2; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleDiagnostics()">
                        <strong style="color: #0d47a1;">
                            <i class="fas fa-info-circle"></i> Period: <?php echo date('M d, Y', strtotime($startDateStr)); ?> to <?php echo date('M d, Y', strtotime($endDateStr)); ?>
                        </strong>
                        <span id="diagToggle" style="color: #0d47a1;">▼</span>
                    </div>
                    <div id="diagnostics" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid #bbdefb; font-size: 12px; color: #555;">
                        <p><strong>Records Found:</strong> <?php echo count($recordsByDate); ?> dates | <?php echo count($attendanceRecords); ?> total records</p>
                        <?php 
                        // Check if today is a holiday
                        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ta_holidays WHERE holiday_date = :today");
                        $stmt->bindParam(':today', $startDateStr);
                        $stmt->execute();
                        $holidayCheck = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($holidayCheck['count'] > 0) {
                            echo "<p style='color: orange;'><i class='fas fa-calendar-times'></i> <strong>Note:</strong> " . date('l, M d, Y', strtotime($startDateStr)) . " is a holiday - employees may be excluded</p>";
                        }
                        ?>
                        <p><strong>Detection Method:</strong> Auto-detecting absent/late employees from attendance records</p>
                    </div>
                </div>

                <script>
                function toggleDiagnostics() {
                    const diag = document.getElementById('diagnostics');
                    const toggle = document.getElementById('diagToggle');
                    if (diag.style.display === 'none') {
                        diag.style.display = 'block';
                        toggle.textContent = '▲';
                    } else {
                        diag.style.display = 'none';
                        toggle.textContent = '▼';
                    }
                }
                </script>
                    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-grow: 1; min-width: 250px;">
                            <label for="periodSelector" style="margin: 0; font-weight: 600; white-space: nowrap;">Time Period:</label>
                            <select id="periodSelector" onchange="changePeriod(this.value)" style="flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; cursor: pointer;">
                                <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="yesterday" <?php echo $period === 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                <option value="this_week" <?php echo $period === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="last_week" <?php echo $period === 'last_week' ? 'selected' : ''; ?>>Last Week</option>
                                <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="last_month" <?php echo $period === 'last_month' ? 'selected' : ''; ?>>Last Month</option>
                                <option value="last_2_weeks" <?php echo $period === 'last_2_weeks' ? 'selected' : ''; ?>>Last 2 Weeks</option>
                                <option value="last_2_months" <?php echo $period === 'last_2_months' ? 'selected' : ''; ?>>Last 2 Months</option>
                                <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                        </div>
                        
                        <div id="customDateRange" style="display: <?php echo $period === 'custom' ? 'flex' : 'none'; ?>; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <input type="date" id="customStartDate" value="<?php echo $customStartDate ?? $startDateStr; ?>" style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                            <span style="white-space: nowrap;">to</span>
                            <input type="date" id="customEndDate" value="<?php echo $customEndDate ?? $endDateStr; ?>" style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                            <button class="btn btn-primary" onclick="applyCustomDate()" style="white-space: nowrap;">
                                <i class="fas fa-check"></i> Apply
                            </button>
                        </div>
                        
                        <button class="btn btn-secondary" onclick="generateReport()" style="white-space: nowrap;">
                            <i class="fas fa-file-pdf"></i> Report
                        </button>
                    </div>
                </div>

                <!-- Records Table -->
                <div class="records-table">
                    <?php if (!empty($recordsByDate)): ?>
                    <div style="margin-bottom: 20px;">
                        <p style="color: #666; font-size: 14px; margin: 0;">
                            Showing records from <strong><?php echo date('M d, Y', strtotime($startDateStr)); ?></strong> to <strong><?php echo date('M d, Y', strtotime($endDateStr)); ?></strong>
                        </p>
                    </div>
                    
                    <?php foreach ($recordsByDate as $date => $records): ?>
                    <div style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                        <div style="background: linear-gradient(135deg, #f0f5ff 0%, #e8f0ff 100%); padding: 15px 20px; border-bottom: 2px solid #e0e0e0;">
                            <h3 style="margin: 0; color: #003d82; font-size: 16px; font-weight: 700;">
                                <i class="fas fa-calendar"></i> <?php echo date('l, M d, Y', strtotime($date)); ?>
                            </h3>
                        </div>
                        
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 2px solid #e0e0e0;">
                                    <th style="padding: 12px 15px; text-align: left; color: #333; font-weight: 600; border-right: 1px solid #e0e0e0;">Employee</th>
                                    <th style="padding: 12px 15px; text-align: left; color: #333; font-weight: 600; border-right: 1px solid #e0e0e0;">Department</th>
                                    <th style="padding: 12px 15px; text-align: center; color: #333; font-weight: 600; border-right: 1px solid #e0e0e0; width: 100px;">Status</th>
                                    <th style="padding: 12px 15px; text-align: left; color: #333; font-weight: 600; border-right: 1px solid #e0e0e0;">Time In / Expected</th>
                                    <th style="padding: 12px 15px; text-align: left; color: #333; font-weight: 600; border-right: 1px solid #e0e0e0;">Minutes Late</th>
                                    <th style="padding: 12px 15px; text-align: left; color: #333; font-weight: 600;">Excuse Type / Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): 
                                    $statusClass = '';
                                    $statusIcon = '';
                                    $statusText = '';
                                    
                                    switch ($record['status']) {
                                        case 'ABSENT':
                                            $statusClass = 'background: #ffebee; color: #c62828;';
                                            $statusIcon = 'fas fa-times-circle';
                                            $statusText = 'ABSENT';
                                            break;
                                        case 'LATE':
                                            $statusClass = 'background: #fff3e0; color: #e65100;';
                                            $statusIcon = 'fas fa-clock';
                                            $statusText = 'LATE';
                                            break;
                                        case 'ON_TIME':
                                            $statusClass = 'background: #e8f5e9; color: #2e7d32;';
                                            $statusIcon = 'fas fa-check-circle';
                                            $statusText = 'ON TIME';
                                            break;
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid #f0f0f0;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 12px 15px; border-right: 1px solid #f0f0f0;">
                                        <strong><?php echo htmlspecialchars($record['full_name']); ?></strong>
                                    </td>
                                    <td style="padding: 12px 15px; border-right: 1px solid #f0f0f0; color: #666;">
                                        <?php echo htmlspecialchars($record['department'] ?? 'N/A'); ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-right: 1px solid #f0f0f0; text-align: center;">
                                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; <?php echo $statusClass; ?> display: inline-block;">
                                            <i class="<?php echo $statusIcon; ?>" style="margin-right: 5px;"></i><?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 15px; border-right: 1px solid #f0f0f0; color: #666;">
                                        <?php if ($record['time_in']): ?>
                                            <strong><?php echo date('H:i', strtotime($record['time_in'])); ?></strong> / <?php echo $record['start_time'] ? date('H:i', strtotime($record['start_time'])) : 'N/A'; ?>
                                        <?php else: ?>
                                            <em style="color: #999;">No check-in</em>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px 15px; border-right: 1px solid #f0f0f0; color: #666;">
                                        <?php echo $record['minutes_late'] ? $record['minutes_late'] . ' min' : '-'; ?>
                                    </td>
                                    <td style="padding: 12px 15px; color: #666;">
                                        <?php if ($record['excuse_type']): ?>
                                            <span style="background: #e3f2fd; color: #0d47a1; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; display: inline-block;">
                                                <?php echo htmlspecialchars($record['excuse_type']); ?>
                                            </span>
                                            <?php if ($record['excuse_reason']): ?>
                                                <br><small style="color: #666; margin-top: 4px; display: inline-block;"><?php echo htmlspecialchars(substr($record['excuse_reason'], 0, 50)); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No attendance records found for the selected period</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- View Record Modal -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Absence/Late Details</h2>
                <button class="close-btn" onclick="closeModal('viewModal')">&times;</button>
            </div>
            <div id="recordDetails"></div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal-overlay" id="reviewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Review Excuse</h2>
                <button class="close-btn" onclick="closeModal('reviewModal')">&times;</button>
            </div>
            <form id="reviewForm">
                <div class="form-group">
                    <label>Decision</label>
                    <select id="reviewDecision" required>
                        <option value="">Select decision...</option>
                        <option value="APPROVED">Approve</option>
                        <option value="REJECTED">Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Review Notes</label>
                    <textarea id="reviewNotes" placeholder="Enter your review notes..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('reviewModal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/toastr/toastr.min.js"></script>
    <script>
        let currentRecordId = null;

        function changePeriod(period) {
            if (period === 'custom') {
                document.getElementById('customDateRange').style.display = 'flex';
            } else {
                document.getElementById('customDateRange').style.display = 'none';
                window.location.href = `absence_late_management.php?period=${period}`;
            }
        }

        function applyCustomDate() {
            const startDate = document.getElementById('customStartDate').value;
            const endDate = document.getElementById('customEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            window.location.href = `absence_late_management.php?period=custom&custom_start_date=${startDate}&custom_end_date=${endDate}`;
        }

        function applyFilters() {
            const period = document.getElementById('periodSelector').value;
            window.location.href = `absence_late_management.php?period=${period}`;
        }

        function viewRecord(recordId) {
            fetch(`../app/api/absence_late_management.php?action=get_record&record_id=${recordId}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const record = res.data;
                        let html = `
                            <div class="form-group">
                                <label>Employee</label>
                                <p>${htmlEscape(record.full_name)}</p>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <p>${htmlEscape(record.department || 'N/A')}</p>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <p><span class="badge badge-${record.type.toLowerCase()}">${record.type}</span></p>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <p>${new Date(record.absence_date).toLocaleDateString()}</p>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <p><span class="badge badge-${record.excuse_status.toLowerCase()}">${record.excuse_status}</span></p>
                            </div>
                            <div class="form-group">
                                <label>Reason</label>
                                <p>${htmlEscape(record.reason || 'Not provided')}</p>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <p>${htmlEscape(record.notes || 'No notes')}</p>
                            </div>
                            <div class="form-group">
                                <label>Approval Notes</label>
                                <p>${htmlEscape(record.approval_notes || 'Not reviewed yet')}</p>
                            </div>
                        `;
                        document.getElementById('recordDetails').innerHTML = html;
                        openModal('viewModal');
                    }
                })
                .catch(err => toastr.error('Failed to load record'));
        }

        function approveExcuse(recordId) {
            currentRecordId = recordId;
            document.getElementById('reviewDecision').value = 'APPROVED';
            document.getElementById('reviewNotes').value = '';
            openModal('reviewModal');
        }

        function rejectExcuse(recordId) {
            currentRecordId = recordId;
            document.getElementById('reviewDecision').value = 'REJECTED';
            document.getElementById('reviewNotes').value = '';
            openModal('reviewModal');
        }

        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const status = document.getElementById('reviewDecision').value;
            const notes = document.getElementById('reviewNotes').value;

            fetch('../app/api/absence_late_management.php?action=review_excuse', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    record_id: currentRecordId,
                    status: status,
                    notes: notes
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    toastr.success(res.message);
                    closeModal('reviewModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(res.message);
                }
            })
            .catch(err => toastr.error('Failed to submit review'));
        });

        function generateReport() {
            const startDate = '<?php echo $startDateStr; ?>';
            const endDate = '<?php echo $endDateStr; ?>';

            let url = '../app/api/absence_late_management.php?action=get_report';
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;

            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        downloadReport(res.data);
                    } else {
                        alert('Error generating report: ' + res.message);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Failed to generate report');
                });
        }

        function downloadReport(data) {
            let csv = 'Employee,Department,Date,Status,Time In,Shift Start,Minutes Late,Excuse Type,Reason\n';
            data.forEach(record => {
                const timeIn = record.time_in ? new Date(record.time_in).toLocaleTimeString() : 'N/A';
                const shiftStart = record.start_time ? record.start_time : 'N/A';
                const minutesLate = record.minutes_late ? record.minutes_late : '-';
                const excuseType = record.excuse_type || '-';
                const reason = record.excuse_reason || '-';
                
                csv += `"${record.full_name}","${record.department}","${record.check_date}","${record.status}","${timeIn}","${shiftStart}","${minutesLate}","${excuseType}","${reason}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `absence-late-report-${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function htmlEscape(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Detect Absence and Late function
        function detectAbsenceAndLate() {
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';

            fetch('../app/api/detect_absence_late.php?action=detect_all', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = `✓ Detection Completed!\n\n`;
                    message += `Absences Detected: ${data.absences_detected}\n`;
                    message += `Late Arrivals Detected: ${data.late_detected}`;

                    if (data.absences && data.absences.length > 0) {
                        message += `\n\nAbsences:\n`;
                        data.absences.forEach(emp => {
                            message += `- ${emp.name} (${emp.department})\n`;
                        });
                    }

                    if (data.late_arrivals && data.late_arrivals.length > 0) {
                        message += `\n\nLate Arrivals:\n`;
                        data.late_arrivals.forEach(emp => {
                            message += `- ${emp.name} (${emp.department}) - ${emp.minutes_late} minutes late\n`;
                        });
                    }

                    alert(message);
                    
                    // Reload the page to show newly detected records
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                    
                    // Show success toast
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message, 'Detection Completed');
                    }
                } else {
                    alert('Error: ' + data.message);
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message, 'Detection Failed');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error detecting absences and late arrivals: ' + error.message);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error detecting absences and late arrivals', 'Error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
</body>
</html>
