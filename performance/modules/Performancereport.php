<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$pdo = Database::getInstance()->getConnection();

$employeeFilter = $_GET['employee'] ?? '';
$departmentFilter = $_GET['department'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$employees = $pdo->query("SELECT id, full_name FROM users WHERE full_name IS NOT NULL AND full_name <> '' ORDER BY full_name")->fetchAll();
$departments = $pdo->query("SELECT department FROM pm_evaluations WHERE department IS NOT NULL AND department <> '' UNION SELECT department FROM goals WHERE department IS NOT NULL AND department <> '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);

$query = "SELECT r.*, u.full_name, COALESCE(e.department, g.department, '') AS department
FROM pm_reports r
JOIN users u ON r.employee_id = u.id
LEFT JOIN pm_evaluations e ON e.employee_id = r.employee_id AND e.review_date BETWEEN r.period_start AND r.period_end
LEFT JOIN goals g ON g.employee_id = r.employee_id AND g.target_date BETWEEN r.period_start AND r.period_end";
$conditions = [];
$params = [];

if ($employeeFilter) {
    $conditions[] = 'r.employee_id = ?';
    $params[] = (int)$employeeFilter;
}

if ($departmentFilter) {
    $conditions[] = "COALESCE(e.department, g.department, '') = ?";
    $params[] = $departmentFilter;
}

if ($startDate) {
    $conditions[] = 'r.period_start >= ?';
    $params[] = $startDate;
}

if ($endDate) {
    $conditions[] = 'r.period_end <= ?';
    $params[] = $endDate;
}

if (count($conditions)) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}

$query .= ' GROUP BY r.report_id ORDER BY r.period_start DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$reports = $stmt->fetchAll();

$summaryTotal = count($reports);
$sumKpi = 0;
$sumFinal = 0;
$gradeCounts = [];
$trendLabels = [];
$trendKpis = [];
$trendFinal = [];

foreach ($reports as $report) {
    $sumKpi += (float)$report['kpi_score'];
    $sumFinal += (float)$report['final_rating_percent'];
    $gradeKey = $report['final_grade'] ?: 'Unknown';
    $gradeCounts[$gradeKey] = ($gradeCounts[$gradeKey] ?? 0) + 1;

    $label = sprintf('%s - %s', $report['period_start'], $report['period_end']);
    $trendLabels[] = $label;
    $trendKpis[] = (float)$report['kpi_score'];
    $trendFinal[] = (float)$report['final_rating_percent'];
}

$avgKpi = $summaryTotal ? round($sumKpi / $summaryTotal, 2) : 0;
$avgFinalRating = $summaryTotal ? round($sumFinal / $summaryTotal, 2) : 0;

$goalQuery = "SELECT COUNT(*) as total_goals,
SUM(CASE WHEN kpi_target > 0 AND kpi_current >= kpi_target THEN 1 ELSE 0 END) as completed_goals
FROM goals g
JOIN users u ON g.employee_id = u.id";
$goalConditions = [];
$goalParams = [];

if ($employeeFilter) {
    $goalConditions[] = 'g.employee_id = ?';
    $goalParams[] = (int)$employeeFilter;
}

if ($departmentFilter) {
    $goalConditions[] = 'g.department = ?';
    $goalParams[] = $departmentFilter;
}

if ($startDate) {
    $goalConditions[] = 'g.target_date >= ?';
    $goalParams[] = $startDate;
}

if ($endDate) {
    $goalConditions[] = 'g.target_date <= ?';
    $goalParams[] = $endDate;
}

if (count($goalConditions)) {
    $goalQuery .= ' WHERE ' . implode(' AND ', $goalConditions);
}

$goalStmt = $pdo->prepare($goalQuery);
$goalStmt->execute($goalParams);
$goalStats = $goalStmt->fetch();

$totalGoals = $goalStats['total_goals'] ?? 0;
$completedGoals = $goalStats['completed_goals'] ?? 0;
$kpiCompletionRate = $totalGoals ? round($completedGoals * 100 / $totalGoals, 2) : 0;

$gradeLabels = array_keys($gradeCounts);
$gradeData = array_values($gradeCounts);
$kpiPending = max(0, $totalGoals - $completedGoals);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Performance Reports</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  <style>
    .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 4px 14px rgba(0,0,0,.12); }
    .chart-box { min-height: 320px; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        <li class="nav-item d-none d-sm-inline-block"><a href="../performance.php" class="nav-link">Home</a></li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><div class="nav-link" id="clock">--:--:--</div></li>
        <li class="nav-item"><a class="nav-link" data-widget="fullscreen" href="#" role="button"><i class="fas fa-expand-arrows-alt"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode"><i class="fas fa-moon" id="themeIcon"></i></a></li>
      </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity:.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
      </a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="info"><a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block"><?= htmlspecialchars($_SESSION['user']['name']) ?></a></div>
        </div>
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"><a href="../performance.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
            <li class="nav-item"><a href="360-degree.php" class="nav-link"><i class="nav-icon fas fa-chart-pie"></i><p>360-Degree Feedback</p></a></li>
            <li class="nav-item"><a href="Appraisals&review.php" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>Appraisals & Review</p></a></li>
            <li class="nav-item"><a href="Goal&KPI.php" class="nav-link"><i class="nav-icon fas fa-tree"></i><p>Goal & KPI</p></a></li>
            <li class="nav-item"><a href="Performancereport.php" class="nav-link active"><i class="nav-icon fas fa-table"></i><p>Performance Reports</p></a></li>
            <li class="nav-item"><a href="../../logout.php" class="nav-link"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
          </ul>
        </nav>
      </div>
    </aside>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Performance Report</h1></div></div>
        </div>
      </div>

      <section class="content"><div class="container-fluid">

        <div class="card card-primary card-outline">
          <div class="card-body">
            <form method="get" class="form-row" id="filterForm">
              <div class="form-group col-md-3"><label>Employee</label><select name="employee" class="form-control"><option value="">All</option><?php foreach ($employees as $emp) { ?><option value="<?= $emp['id'] ?>" <?= $employeeFilter == $emp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($emp['full_name']) ?></option><?php } ?></select></div>
              <div class="form-group col-md-3"><label>Department</label><select name="department" class="form-control"><option value="">All</option><?php foreach ($departments as $dep) { ?><option value="<?= htmlspecialchars($dep) ?>" <?= $departmentFilter === $dep ? 'selected' : '' ?>><?= htmlspecialchars($dep) ?></option><?php } ?></select></div>
              <div class="form-group col-md-2"><label>From</label><input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>"></div>
              <div class="form-group col-md-2"><label>To</label><input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>"></div>
              <div class="form-group col-md-2 align-self-end"><button type="submit" class="btn btn-success btn-block"><i class="fas fa-filter"></i> Filter</button></div>
            </form>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 col-sm-6"><div class="card stat-card text-white bg-info mb-3"><div class="card-body"><h5 class="card-title">Total Reports</h5><p class="card-text h2"><?= $summaryTotal ?></p></div></div></div>
          <div class="col-lg-3 col-sm-6"><div class="card stat-card text-white bg-success mb-3"><div class="card-body"><h5 class="card-title">Avg KPI Score</h5><p class="card-text h2"><?= $avgKpi ?></p></div></div></div>
          <div class="col-lg-3 col-sm-6"><div class="card stat-card text-white bg-primary mb-3"><div class="card-body"><h5 class="card-title">Avg Final Rating</h5><p class="card-text h2"><?= $avgFinalRating ?>%</p></div></div></div>
          <div class="col-lg-3 col-sm-6"><div class="card stat-card text-white bg-warning mb-3"><div class="card-body"><h5 class="card-title">KPI Completion</h5><p class="card-text h2"><?= $kpiCompletionRate ?>%</p><small><?= $completedGoals ?>/<?= $totalGoals ?> goals</small></div></div></div>
        </div>

        <div class="row">
          <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">Performance Trend</h3></div><div class="card-body chart-box"><canvas id="performanceTrendChart"></canvas></div></div></div>
          <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">KPI Completion Status</h3></div><div class="card-body chart-box"><canvas id="kpiCompletionChart"></canvas></div></div></div>
        </div>

        <div class="row">
          <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">Appraisal Grade Distribution</h3></div><div class="card-body chart-box"><canvas id="gradeDistributionChart"></canvas></div></div></div>
          <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">Report Details</h3></div><div class="card-body table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>Employee</th><th>Period</th><th>KPI Score</th><th>Final Rating %</th><th>Final Grade</th><th>Department</th></tr></thead><tbody><?php if ($summaryTotal === 0) { echo '<tr><td colspan="6" class="text-center">No data available</td></tr>'; } else { foreach ($reports as $r) { ?><tr><td><?= htmlspecialchars($r['full_name'] ?? 'Unknown') ?></td><td><?= htmlspecialchars($r['period_start']) ?> to <?= htmlspecialchars($r['period_end']) ?></td><td><?= htmlspecialchars($r['kpi_score']) ?></td><td><?= htmlspecialchars($r['final_rating_percent']) ?>%</td><td><?= htmlspecialchars($r['final_grade']) ?></td><td><?= htmlspecialchars($r['department']) ?></td></tr><?php } } ?></tbody></table></div></div></div>
        </div>

      </div></section>
    </div>

    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>

  <?php include "../../layout/global_modal.php"; ?>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

  <script>
    const trendLabels = <?= json_encode($trendLabels) ?>;
    const trendKpis = <?= json_encode($trendKpis) ?>;
    const trendFinal = <?= json_encode($trendFinal) ?>;
    const gradeLabels = <?= json_encode($gradeLabels) ?>;
    const gradeData = <?= json_encode($gradeData) ?>;
    const kpiDegreeData = [<?= $completedGoals ?>, <?= $kpiPending ?>];

    new Chart(document.getElementById('performanceTrendChart'), {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [
          { label: 'KPI Score', data: trendKpis, borderColor: '#007bff', backgroundColor: 'rgba(0,123,255,0.35)', fill: true, tension: 0.3 },
          { label: 'Final Rating %', data: trendFinal, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.35)', fill: true, tension: 0.3 }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('kpiCompletionChart'), {
      type: 'doughnut',
      data: {
        labels: ['Completed KPIs', 'Pending KPIs'],
        datasets: [{ data: kpiDegreeData, backgroundColor: ['#28a745', '#ffc107'] }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('gradeDistributionChart'), {
      type: 'pie',
      data: {
        labels: gradeLabels,
        datasets: [{ data: gradeData, backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'] }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
  </script>
</body>
</html>
