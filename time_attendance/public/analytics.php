<?php
/**
 * Analytics & Reports Dashboard
 * Department-wide attendance analytics with charts
 */

require_once "../app/config/Database.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

// Check authorization (HR_ADMIN or DEPARTMENT_HEAD)
$role = AuthController::getCurrentRole();
if (!in_array($role, ['HR_ADMIN', 'DEPARTMENT_HEAD', 'SYSTEM_ADMIN'])) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$report_type = $_GET['type'] ?? 'monthly'; // monthly, weekly, daily
$department_filter = $_GET['department'] ?? '';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

// Initialize stats variables
$on_time = 0;
$late = 0;
$absent = 0;
$total_hours = 0;
$daily_data = [];
$weekly_data = [];

// Get all departments
$dept_query = "SELECT DISTINCT department FROM employees ORDER BY department";
$dept_stmt = $conn->prepare($dept_query);
$dept_stmt->execute();
$departments = $dept_stmt->fetchAll(PDO::FETCH_COLUMN);

// Build attendance query based on filter
$where = "WHERE YEAR(time_in) = ?";
$params = [$year];

if ($department_filter) {
    $where .= " AND e.department = ?";
    $params[] = $department_filter;
}

// Monthly Report
if ($report_type === 'monthly') {
    $where .= " AND MONTH(time_in) = ?";
    $params[] = $month;
    
    // Get daily attendance data
    $query = "SELECT DATE(a.time_in) as date, a.status, COUNT(*) as count
              FROM attendance a
              JOIN employees e ON a.employee_id = e.employee_id
              $where
              GROUP BY DATE(a.time_in)
              ORDER BY date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $daily_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Aggregate stats
    $on_time = 0; $late = 0; $absent = 0; $total_hours = 0;
    foreach ($daily_data as $day) {
        if ($day['status'] === 'PRESENT' || $day['status'] === 'EARLY_OUT') {
            $on_time += $day['count'];
        } elseif ($day['status'] === 'LATE') {
            $late += $day['count'];
        } else {
            $absent += $day['count'];
        }
    }
}

// Weekly Report
else if ($report_type === 'weekly') {
    $query = "SELECT YEARWEEK(a.time_in) as week, 
                     COUNT(*) as total_records,
                     SUM(CASE WHEN a.status IN ('PRESENT', 'EARLY_OUT') THEN 1 ELSE 0 END) as on_time_count,
                     SUM(CASE WHEN a.status = 'LATE' THEN 1 ELSE 0 END) as late_count
              FROM attendance a
              JOIN employees e ON a.employee_id = e.employee_id
              $where
              GROUP BY YEARWEEK(a.time_in)
              ORDER BY week DESC
              LIMIT 12";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $weekly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Department Comparison
$dept_query = "SELECT e.department, 
                      COUNT(DISTINCT a.employee_id) as employees,
                      COUNT(*) as total_records,
                      SUM(CASE WHEN a.status IN ('PRESENT', 'EARLY_OUT') THEN 1 ELSE 0 END) as on_time_count,
                      SUM(CASE WHEN a.status = 'LATE' THEN 1 ELSE 0 END) as late_count
               FROM attendance a
               JOIN employees e ON a.employee_id = e.employee_id
               WHERE YEAR(a.time_in) = ?
               GROUP BY e.department
               ORDER BY on_time_count DESC";

$dept_stmt = $conn->prepare($dept_query);
$dept_stmt->execute([$year]);
$dept_comparison = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);

// Employee rankings
$emp_query = "SELECT e.employee_id, e.full_name, e.department,
                     COUNT(*) as total_days,
                     SUM(CASE WHEN a.status IN ('PRESENT', 'EARLY_OUT') THEN 1 ELSE 0 END) as on_time_days,
                     SUM(CASE WHEN a.status = 'LATE' THEN 1 ELSE 0 END) as late_days
              FROM attendance a
              JOIN employees e ON a.employee_id = e.employee_id
              WHERE YEAR(a.time_in) = ?";

if ($department_filter) {
    $emp_query .= " AND e.department = ?";
    $emp_params = [$year, $department_filter];
} else {
    $emp_params = [$year];
}

$emp_query .= " GROUP BY e.employee_id
               ORDER BY on_time_days DESC
               LIMIT 20";

$emp_stmt = $conn->prepare($emp_query);
$emp_stmt->execute($emp_params);
$top_employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Reports - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .filter-group select, .filter-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-apply {
            background: #3498db;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            align-self: flex-end;
            margin-top: 23px;
        }
        .btn-apply:hover {
            background: #2980b9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h4 {
            color: #7f8c8d;
            font-size: 13px;
            margin: 0;
            text-transform: uppercase;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: relative;
            height: 400px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        table th {
            background: #34495e;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        table tr:hover {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.high {
            background: #d5f4e6;
            color: #27ae60;
        }
        .badge.medium {
            background: #fdf2e9;
            color: #e67e22;
        }
        .badge.low {
            background: #fadbd8;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Analytics & Reports</h1>
            
            <!-- Filters -->
            <form method="GET" class="filters">
                <div class="filter-group">
                    <label>Report Type</label>
                    <select name="type" onchange="this.form.submit()">
                        <option value="monthly" <?php echo $report_type === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="weekly" <?php echo $report_type === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="daily" <?php echo $report_type === 'daily' ? 'selected' : ''; ?>>Daily</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Year</label>
                    <input type="number" name="year" value="<?php echo $year; ?>" min="2020" max="2099">
                </div>
                
                <?php if ($report_type === 'monthly'): ?>
                    <div class="filter-group">
                        <label>Month</label>
                        <select name="month">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo sprintf('%02d', $m); ?>" 
                                    <?php echo $month == sprintf('%02d', $m) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="filter-group">
                    <label>Department</label>
                    <select name="department">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>" 
                                <?php echo $department_filter === $dept ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-apply">Apply Filters</button>
            </form>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>On Time</h4>
                    <div class="value"><?php echo $on_time ?? 0; ?></div>
                    <small>employees present on time</small>
                </div>
                <div class="stat-card" style="border-left-color: #f39c12;">
                    <h4>Late</h4>
                    <div class="value"><?php echo $late ?? 0; ?></div>
                    <small>late arrivals</small>
                </div>
                <div class="stat-card" style="border-left-color: #e74c3c;">
                    <h4>Absent</h4>
                    <div class="value"><?php echo $absent ?? 0; ?></div>
                    <small>absences</small>
                </div>
                <div class="stat-card" style="border-left-color: #9b59b6;">
                    <h4>Total Records</h4>
                    <div class="value"><?php echo ($on_time + $late + $absent); ?></div>
                    <small>attendance records</small>
                </div>
            </div>

            <!-- Department Comparison Chart -->
            <div class="chart-container">
                <h3>Department Attendance Comparison</h3>
                <canvas id="deptChart"></canvas>
            </div>

            <!-- Attendance Trend Chart -->
            <div class="chart-container">
                <h3><?php echo ucfirst($report_type); ?> Attendance Trend</h3>
                <canvas id="trendChart"></canvas>
            </div>

            <!-- Top Employees -->
            <h2>Top Performing Employees</h2>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Days Attended</th>
                        <th>On-Time Days</th>
                        <th>Late Days</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_employees as $emp): ?>
                        <?php 
                        $on_time_pct = ($emp['on_time_days'] / $emp['total_days']) * 100;
                        $performance = 'low';
                        if ($on_time_pct >= 95) $performance = 'high';
                        elseif ($on_time_pct >= 85) $performance = 'medium';
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($emp['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($emp['department']); ?></td>
                            <td><?php echo $emp['total_days']; ?></td>
                            <td><?php echo $emp['on_time_days']; ?></td>
                            <td><?php echo $emp['late_days']; ?></td>
                            <td><span class="badge <?php echo $performance; ?>"><?php echo round($on_time_pct, 1); ?>%</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Department Stats -->
            <h2 style="margin-top: 40px;">Department Statistics</h2>
            <table>
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Employees</th>
                        <th>Total Records</th>
                        <th>On-Time Count</th>
                        <th>Late Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dept_comparison as $dept): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($dept['department']); ?></strong></td>
                            <td><?php echo $dept['employees']; ?></td>
                            <td><?php echo $dept['total_records']; ?></td>
                            <td><?php echo $dept['on_time_count']; ?></td>
                            <td><?php echo $dept['late_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <script>
        // Department Comparison Chart
        const deptData = <?php echo json_encode($dept_comparison); ?>;
        const ctx1 = document.getElementById('deptChart').getContext('2d');
        const deptChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: deptData.map(d => d.department),
                datasets: [{
                    label: 'On-Time',
                    data: deptData.map(d => d.on_time_count),
                    backgroundColor: '#27ae60'
                }, {
                    label: 'Late',
                    data: deptData.map(d => d.late_count),
                    backgroundColor: '#f39c12'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: false },
                    y: { stacked: false, beginAtZero: true }
                }
            }
        });

        // Trend Chart
        <?php if ($report_type === 'monthly' && !empty($daily_data)): ?>
            {
                const trendData = <?php echo json_encode($daily_data); ?>;
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: trendData.map(d => new Date(d.date).toLocaleDateString()),
                        datasets: [{
                            label: 'Attendance Records',
                            data: trendData.map(d => d.count),
                            backgroundColor: '#3498db'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        <?php elseif ($report_type === 'weekly' && !empty($weekly_data)): ?>
            {
                const weeklyData = <?php echo json_encode($weekly_data); ?>;
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.map(d => 'Week ' + d.week),
                        datasets: [{
                            label: 'On-Time',
                            data: weeklyData.map(d => d.on_time_count),
                            backgroundColor: '#27ae60'
                        }, {
                            label: 'Late',
                            data: weeklyData.map(d => d.late_count),
                            backgroundColor: '#f39c12'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        <?php else: ?>
            {
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'doughnut',
                    data: {
                        labels: ['On Time', 'Late', 'Absent'],
                        datasets: [{
                            data: [<?php echo $on_time; ?>, <?php echo $late; ?>, <?php echo $absent; ?>],
                            backgroundColor: ['#27ae60', '#f39c12', '#e74c3c']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        <?php endif; ?>
    </script>
</body>
</html>
