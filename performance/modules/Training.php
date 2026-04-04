<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$pdo = Database::getInstance()->getConnection();

$employeeId = $_GET['employee'] ?? '';
$department = $_GET['department'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$action = $_POST['action'] ?? '';

// Employees list for filter
$employees = $pdo->query("SELECT id, full_name, role FROM users WHERE full_name IS NOT NULL AND full_name <> '' ORDER BY full_name")->fetchAll();

$criteria = [];
$params = [];

if ($employeeId) {
    $criteria[] = 'pe.employee_id = ?';
    $params[] = (int)$employeeId;
}

if ($department) {
    $criteria[] = 'pe.department = ?';
    $params[] = $department;
}

if ($startDate) {
    $criteria[] = 'pe.review_date >= ?';
    $params[] = $startDate;
}

if ($endDate) {
    $criteria[] = 'pe.review_date <= ?';
    $params[] = $endDate;
}

$where = count($criteria) ? 'WHERE ' . implode(' AND ', $criteria) : '';

$evaluations = $pdo->prepare("SELECT pe.*, u.full_name FROM pm_evaluations pe JOIN users u ON pe.employee_id=u.id $where ORDER BY pe.review_date DESC");
$evaluations->execute($params);
$evaluations = $evaluations->fetchAll();

// Goal KPI evaluation
$gcriteria = [];
$gparams = [];
if ($employeeId) { $gcriteria[] = 'g.employee_id = ?'; $gparams[] = (int)$employeeId; }
if ($department) { $gcriteria[] = 'g.department = ?'; $gparams[] = $department; }
if ($startDate) { $gcriteria[] = 'g.target_date >= ?'; $gparams[] = $startDate; }
if ($endDate) { $gcriteria[] = 'g.target_date <= ?'; $gparams[] = $endDate; }
$gwhere = count($gcriteria) ? 'WHERE ' . implode(' AND ', $gcriteria) : '';
$goalsStmt = $pdo->prepare("SELECT g.*, u.full_name FROM goals g JOIN users u ON g.employee_id=u.id $gwhere ORDER BY g.target_date DESC");
$goalsStmt->execute($gparams);
$goals = $goalsStmt->fetchAll();

$recommendations = [];

foreach ($evaluations as $eval) {
    $gap = [];
    if ($eval['rating_percent'] < 70) {
        $gap = [
            'skill_gap' => 'Low performance score (' . $eval['rating_percent'] . '%)',
            'program' => 'Performance Acceleration Program',
            'provider' => 'In-house L&D',
            'schedule' => 'Next month cohort',
            'outcome' => 'Improve quality and consistency of outputs'
        ];
    } elseif ($eval['rating_percent'] < 85) {
        $gap = [
            'skill_gap' => 'Development needed for competency gaps',
            'program' => 'Skills Enhancement Workshop',
            'provider' => 'Learning Academy',
            'schedule' => '2-week schedule',
            'outcome' => 'Raise KPIs and efficiency'
        ];
    }

    if (!empty($gap)) {
        $recommendations[] = [
            'employee_id' => $eval['employee_id'],
            'employee_name' => $eval['full_name'],
            'position' => $eval['position'],
            'skill_gap' => $gap['skill_gap'],
            'recommended_program' => $gap['program'],
            'training_provider' => $gap['provider'],
            'training_schedule' => $gap['schedule'],
            'expected_outcome' => $gap['outcome']
        ];
    }
}

foreach ($goals as $goal) {
    if ($goal['kpi_target'] > 0) {
        $ratio = ($goal['kpi_current'] / $goal['kpi_target']) * 100;
        if ($ratio < 75) {
            $recommendations[] = [
                'employee_id' => $goal['employee_id'],
                'employee_name' => $goal['full_name'],
                'position' => $goal['position'],
                'skill_gap' => 'Goal KPI progress is ' . round($ratio) . '%',
                'recommended_program' => 'Goal Execution Masterclass',
                'training_provider' => 'Performance Center',
                'training_schedule' => 'Customized 3 sessions',
                'expected_outcome' => 'Accelerate goal completion to 100%'
            ];
        }
    }
}

// Save recommendations into library (optional)
if ($action === 'save_recommendations' && !empty($recommendations)) {
    $insert = $pdo->prepare("INSERT INTO tr_recommendations (employee_id, position, skill_gap, recommended_program, training_provider, training_schedule, expected_outcome, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($recommendations as $rec) {
        $insert->execute([
            $rec['employee_id'],
            $rec['position'],
            $rec['skill_gap'],
            $rec['recommended_program'],
            $rec['training_provider'],
            $rec['training_schedule'],
            $rec['expected_outcome'],
            $_SESSION['user']['id'] ?? null
        ]);
    }
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Training recommendations saved.'];
    header('Location: Training.php');
    exit;
}

$existingRecommendations = $pdo->query("SELECT tr.*, u.full_name FROM tr_recommendations tr JOIN users u ON tr.employee_id=u.id ORDER BY tr.created_at DESC LIMIT 50")->fetchAll();

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Training Recommendations</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
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
        <li class="nav-item"><a class="nav-link" href="#" id="darkToggle"><i class="fas fa-moon" id="themeIcon"></i></a></li>
      </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link"><img src="../../assets/pics/bcpLogo.png" class="brand-image elevation-3" style="opacity: .9" /><span class="brand-text font-weight-light">BCP Bulacan</span></a>
      <div class="sidebar"><div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center"><div class="info"><a href="#" onclick="openGlobalModal('Profile Settings','../../user_profile/profile_form.php')" class="d-block"><?= htmlspecialchars($_SESSION['user']['name']) ?></a></div></div>
        <nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item"><a href="../performance.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="360-degree.php" class="nav-link"><i class="nav-icon fas fa-chart-pie"></i><p>360-Degree Feedback</p></a></li>
          <li class="nav-item"><a href="Appraisals&review.php" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>Appraisals & Review</p></a></li>
          <li class="nav-item"><a href="Goal&KPI.php" class="nav-link"><i class="nav-icon fas fa-tree"></i><p>Goal & KPI</p></a></li>
          <li class="nav-item"><a href="Performancereport.php" class="nav-link"><i class="nav-icon fas fa-table"></i><p>Performance Report</p></a></li>
          <li class="nav-item"><a href="Training.php" class="nav-link active"><i class="nav-icon fas fa-graduation-cap"></i><p>Training</p></a></li>
          <li class="nav-item"><a href="../../logout.php" class="nav-link"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
        </ul></nav>
      </div>
    </aside>

    <div class="content-wrapper">
      <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Training Recommendation System</h1></div></div></div></div>
      <section class="content"><div class="container-fluid">

        <div class="card card-info card-outline"><div class="card-body">
          <form method="get" class="row">
            <div class="form-group col-md-3"><label>Employee</label><select name="employee" class="form-control"><option value="">All</option><?php foreach ($employees as $emp) { ?><option value="<?= $emp['id'] ?>" <?= $employeeId == $emp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($emp['full_name']) ?></option><?php } ?></select></div>
            <div class="form-group col-md-3"><label>Department</label><input name="department" class="form-control" value="<?= htmlspecialchars($department) ?>"></div>
            <div class="form-group col-md-2"><label>From</label><input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>"></div>
            <div class="form-group col-md-2"><label>To</label><input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>"></div>
            <div class="form-group col-md-2 align-self-end"><button class="btn btn-success btn-block" type="submit"><i class="fas fa-filter"></i> Apply</button></div>
          </form>
        </div></div>

        <div class="card card-warning card-outline"><div class="card-header"><h3 class="card-title">Suggested Training Programs</h3></div>
          <div class="card-body">
            <?php if (!$recommendations) { ?><div class="alert alert-info">No gaps detected for selected filters, or performance is already strong.</div><?php } else { ?>
            <p class="mb-2"><strong><?= count($recommendations) ?></strong> recommendation(s) generated.</p>
            <form method="post"><input type="hidden" name="action" value="save_recommendations"><button type="submit" class="btn btn-primary mb-3">Save all as Recommendations</button></form>
            <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Employee</th><th>Position</th><th>Gap</th><th>Program</th><th>Provider</th><th>Schedule</th><th>Outcome</th></tr></thead><tbody>
              <?php foreach ($recommendations as $rec) { ?><tr>
                <td><?= htmlspecialchars($rec['employee_name']) ?></td>
                <td><?= htmlspecialchars($rec['position']) ?></td>
                <td><?= htmlspecialchars($rec['skill_gap']) ?></td>
                <td><?= htmlspecialchars($rec['recommended_program']) ?></td>
                <td><?= htmlspecialchars($rec['training_provider']) ?></td>
                <td><?= htmlspecialchars($rec['training_schedule']) ?></td>
                <td><?= htmlspecialchars($rec['expected_outcome']) ?></td>
              </tr><?php } ?>
            </tbody></table></div>
            <?php } ?>
          </div>
        </div>

        <div class="card card-success card-outline"><div class="card-header"><h3 class="card-title">Saved Training Recommendations</h3></div>
          <div class="card-body table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Date</th><th>Employee</th><th>Program</th><th>Gap</th><th>Provider</th><th>Schedule</th></tr></thead><tbody>
            <?php if (!$existingRecommendations) { ?><tr><td colspan="6" class="text-center">No saved recommendations yet.</td></tr><?php } else { foreach ($existingRecommendations as $r) { ?><tr>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= htmlspecialchars($r['recommended_program']) ?></td>
                <td><?= htmlspecialchars($r['skill_gap']) ?></td>
                <td><?= htmlspecialchars($r['training_provider']) ?></td>
                <td><?= htmlspecialchars($r['training_schedule']) ?></td>
              </tr><?php }} ?>
          </tbody></table></div>
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
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>
</body>
</html>
