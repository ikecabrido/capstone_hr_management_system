<?php
/**
 * Reports Page - Time & Attendance System
 * Generate and view attendance reports
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Employee.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check if user is authenticated
if (!AuthController::isAuthenticated()) {
    header("Location: Login.php");
    exit;
}

// Only HR can access this page
if (!AuthController::hasRole('HR_ADMIN') && !AuthController::hasRole('DEPARTMENT_HEAD')) {
    header("Location: employee_dashboard.php");
    exit;
}

$attendanceModel = new Attendance();
$employeeModel = new Employee();

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$employee_id = $_GET['employee_id'] ?? null;

// Get records based on filters
$records = $attendanceModel->getByDateRange($start_date, $end_date, $employee_id, 500);

// Get all employees for filter dropdown
$employees = $employeeModel->getAll('ACTIVE', 1000);

$current_page = 'reports.php';
$current_role = $_SESSION['role'] ?? 'HR_ADMIN';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
            transition: margin-left 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        body.dark-mode h1,
        body.dark-mode h2 {
            color: #b0c4de;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        body.dark-mode .container {
            background: #1e1e1e;
            color: #e0e0e0;
        }
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-bg);
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-control {
            display: flex;
            flex-direction: column;
        }
        .form-control label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        .form-control input,
        .form-control select {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        .form-control input:focus,
        .form-control select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 5px rgba(39, 174, 96, 0.1);
        }
        .form-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-top: 4px solid var(--secondary-color);
        }
        body.dark-mode .dashboard-card {
            background: #1e1e1e;
            color: #e0e0e0;
        }
        .card-title {
            font-size: 12px;
            color: var(--border-color);
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .card-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        body.dark-mode .card-value {
            color: #e0e0e0;
        }
        .card-subtitle {
            font-size: 12px;
            color: var(--border-color);
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Attendance Reports</h1>
            <p>View and analyze attendance records by date range and employee</p>

            <!-- Filter Form -->
            <div class="container">
                <form method="GET" class="form-section">
                    <div class="form-section-title">Filter Reports</div>

                    <div class="form-row">
                        <div class="form-control">
                            <label for="start_date">Start Date</label>
                            <input 
                                type="date" 
                                id="start_date" 
                                name="start_date" 
                                value="<?php echo htmlspecialchars($start_date); ?>"
                            >
                        </div>

                        <div class="form-control">
                            <label for="end_date">End Date</label>
                            <input 
                                type="date" 
                                id="end_date" 
                                name="end_date" 
                                value="<?php echo htmlspecialchars($end_date); ?>"
                            >
                        </div>

                        <div class="form-control">
                            <label for="employee_id">Employee (Optional)</label>
                            <select id="employee_id" name="employee_id">
                                <option value="">All Employees</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>" 
                                        <?php echo ($employee_id == $emp['employee_id'] ? 'selected' : ''); ?>>
                                        <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>
                        <a href="reports.php" class="btn btn-secondary">
                            Reset
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="window.print()">
                            Print
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Summary -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="card-title">Total Records</div>
                    <div class="card-value"><?php echo count($records); ?></div>
                    <div class="card-subtitle">in selected period</div>
                </div>

                <div class="dashboard-card">
                    <div class="card-title">Date Range</div>
                    <div class="card-value" style="font-size: 16px;">
                        <?php echo date('M d', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?>
                    </div>
                    <div class="card-subtitle"><?php echo count(array_unique(array_map(function($r) { return $r['attendance_date']; }, $records))); ?> working days</div>
                </div>

                <div class="dashboard-card">
                    <div class="card-title">Present Days</div>
                    <div class="card-value" style="color: #27ae60;">
                        <?php echo count(array_filter($records, function($r) { return !empty($r['time_in']); })); ?>
                    </div>
                    <div class="card-subtitle">with time in</div>
                </div>

                <div class="dashboard-card">
                    <div class="card-title">Absent Days</div>
                    <div class="card-value" style="color: #e74c3c;">
                        <?php echo count(array_filter($records, function($r) { return empty($r['time_in']); })); ?>
                    </div>
                    <div class="card-subtitle">no record</div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="container">
                <h2 style="margin-bottom: 20px;">Attendance Records (<?php echo count($records); ?>)</h2>

                <?php if (empty($records)): ?>
                    <p style="color: #999; text-align: center; padding: 40px;">
                        No records found for the selected filters.
                    </p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Duration</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></strong><br>
                                        <small style="color: #999;"><?php echo htmlspecialchars($record['employee_number']); ?></small>
                                    </td>
                                    <td><?php echo Helper::formatDate($record['attendance_date']); ?></td>
                                    <td>
                                        <?php echo (!empty($record['time_in']) ? Helper::formatTime($record['time_in']) : 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php echo (!empty($record['time_out']) ? Helper::formatTime($record['time_out']) : 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo (!empty($record['time_in']) && !empty($record['time_out'])
                                                ? Helper::calculateDuration($record['time_in'], $record['time_out'])
                                                : 'N/A');
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo $record['recorded_by']; ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = Helper::determineStatus($record['time_in']);
                                            $badge_class = 'badge-info';
                                            if ($status === 'PRESENT') {
                                                $badge_class = 'badge-success';
                                            } elseif ($status === 'LATE') {
                                                $badge_class = 'badge-warning';
                                            } elseif ($status === 'ABSENT') {
                                                $badge_class = 'badge-danger';
                                            }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Report Statistics -->
            <div class="container">
                <h2 style="margin-bottom: 20px;">Statistics</h2>
                <div class="form-row">
                    <div style="padding: 15px; background: var(--light-bg); border-radius: 4px;">
                        <p style="margin: 5px 0;"><strong>Total Working Days:</strong> 
                            <?php 
                                $days = array_unique(array_map(function($r) { 
                                    return $r['attendance_date']; 
                                }, $records)); 
                                echo count($days);
                            ?>
                        </p>
                    </div>
                    <div style="padding: 15px; background: var(--light-bg); border-radius: 4px;">
                        <p style="margin: 5px 0;"><strong>Average Time In:</strong> 
                            <?php 
                                $with_times = array_filter($records, function($r) { return !empty($r['time_in']); });
                                echo !empty($with_times) ? count($with_times) . " days" : "N/A";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dark mode preference on page load
        window.addEventListener('load', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>