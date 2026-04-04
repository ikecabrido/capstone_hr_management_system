<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();

$message = '';
$success_message = '';
$error_message = '';

// Enhanced validation function
function validateAppraisalData($data) {
    $errors = [];

    if (empty($data['employee_id'])) {
        $errors[] = "Employee selection is required";
    }

    if (empty($data['review_period'])) {
        $errors[] = "Review period is required";
    }

    if (empty($data['review_date'])) {
        $errors[] = "Review date is required";
    }

    if (empty($data['goals_kpis'])) {
        $errors[] = "Goals & KPIs are required";
    }

    if (empty($data['manager_evaluation'])) {
        $errors[] = "Manager's evaluation is required";
    }

    if (!isset($data['overall_score']) || $data['overall_score'] < 0 || $data['overall_score'] > 5) {
        $errors[] = "Overall score must be between 0 and 5";
    }

    // Validate ratings
    if (isset($data['ratings']) && is_array($data['ratings'])) {
        foreach ($data['ratings'] as $rating) {
            if (!empty($rating['name']) && (!isset($rating['score']) || $rating['score'] < 1 || $rating['score'] > 5)) {
                $errors[] = "Rating scores must be between 1 and 5";
            }
        }
    }

    return $errors;
}

// Handle bulk operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bulk delete
    if (isset($_POST['bulk_delete']) && isset($_POST['selected_appraisals'])) {
        $selected_ids = $_POST['selected_appraisals'];
        if (!empty($selected_ids)) {
            try {
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $sql = "DELETE FROM pm_appraisals WHERE appraisal_id IN ($placeholders)";
                $stmt = $db->prepare($sql);
                $stmt->execute($selected_ids);
                $success_message = count($selected_ids) . " appraisal(s) deleted successfully!";
            } catch (PDOException $e) {
                $error_message = "Error deleting appraisals: " . $e->getMessage();
            }
        }
    }

    // Add Appraisal with enhanced validation
    if (isset($_POST['add_appraisal'])) {
        $data = [
            'employee_id' => $_POST['employee_id'],
            'review_period' => $_POST['review_period'],
            'goals_kpis' => $_POST['goals_kpis'],
            'ratings' => $_POST['ratings'] ?? [],
            'manager_evaluation' => $_POST['manager_evaluation'],
            'overall_score' => $_POST['overall_score'],
            'comments' => $_POST['comments'],
            'review_date' => $_POST['review_date']
        ];

        $validation_errors = validateAppraisalData($data);
        if (empty($validation_errors)) {
            try {
                $sql = "INSERT INTO pm_appraisals (employee_id, review_period, goals_kpis, performance_ratings, manager_evaluation, overall_score, comments, review_date)
                        VALUES (:employee_id, :review_period, :goals_kpis, :ratings, :manager_evaluation, :overall_score, :comments, :review_date)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'employee_id' => $data['employee_id'],
                    'review_period' => $data['review_period'],
                    'goals_kpis' => $data['goals_kpis'],
                    'ratings' => json_encode($data['ratings']),
                    'manager_evaluation' => $data['manager_evaluation'],
                    'overall_score' => $data['overall_score'],
                    'comments' => $data['comments'],
                    'review_date' => $data['review_date']
                ]);

                // Log audit trail
                $appraisal_id = $db->lastInsertId();
                logAuditTrail($db, 'CREATE', 'pm_appraisals', $appraisal_id, $_SESSION['user']['id'] ?? null, 'Appraisal created');

                $success_message = 'Appraisal added successfully!';
            } catch (PDOException $e) {
                $error_message = 'Error: ' . $e->getMessage();
            }
        } else {
            $error_message = 'Validation errors: ' . implode(', ', $validation_errors);
        }
    }

    // Update Appraisal with enhanced validation
    if (isset($_POST['update_appraisal'])) {
        $data = [
            'appraisal_id' => $_POST['appraisal_id'],
            'employee_id' => $_POST['employee_id'],
            'review_period' => $_POST['review_period'],
            'goals_kpis' => $_POST['goals_kpis'],
            'ratings' => $_POST['ratings'] ?? [],
            'manager_evaluation' => $_POST['manager_evaluation'],
            'overall_score' => $_POST['overall_score'],
            'comments' => $_POST['comments'],
            'review_date' => $_POST['review_date']
        ];

        $validation_errors = validateAppraisalData($data);
        if (empty($validation_errors)) {
            try {
                $sql = "UPDATE pm_appraisals SET
                        employee_id = :employee_id,
                        review_period = :review_period,
                        goals_kpis = :goals_kpis,
                        performance_ratings = :ratings,
                        manager_evaluation = :manager_evaluation,
                        overall_score = :overall_score,
                        comments = :comments,
                        review_date = :review_date
                        WHERE appraisal_id = :appraisal_id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'employee_id' => $data['employee_id'],
                    'review_period' => $data['review_period'],
                    'goals_kpis' => $data['goals_kpis'],
                    'ratings' => json_encode($data['ratings']),
                    'manager_evaluation' => $data['manager_evaluation'],
                    'overall_score' => $data['overall_score'],
                    'comments' => $data['comments'],
                    'review_date' => $data['review_date'],
                    'appraisal_id' => $data['appraisal_id']
                ]);

                // Log audit trail
                logAuditTrail($db, 'UPDATE', 'pm_appraisals', $data['appraisal_id'], $_SESSION['user']['id'] ?? null, 'Appraisal updated');

                $success_message = 'Appraisal updated successfully!';
            } catch (PDOException $e) {
                $error_message = 'Error: ' . $e->getMessage();
            }
        } else {
            $error_message = 'Validation errors: ' . implode(', ', $validation_errors);
        }
    }

    // Delete Appraisal
    if (isset($_POST['delete_appraisal'])) {
        $appraisal_id = $_POST['appraisal_id'];
        try {
            $sql = "DELETE FROM pm_appraisals WHERE appraisal_id = :appraisal_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['appraisal_id' => $appraisal_id]);

            // Log audit trail
            logAuditTrail($db, 'DELETE', 'pm_appraisals', $appraisal_id, $_SESSION['user']['id'] ?? null, 'Appraisal deleted');

            $success_message = 'Appraisal deleted successfully!';
        } catch (PDOException $e) {
            $error_message = 'Error: ' . $e->getMessage();
        }
    }
}

// Audit trail function
function logAuditTrail($db, $action, $table_name, $record_id, $user_id, $description) {
    try {
        $sql = "INSERT INTO audit_log (action, table_name, record_id, user_id, description, created_at)
                VALUES (:action, :table_name, :record_id, :user_id, :description, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'action' => $action,
            'table_name' => $table_name,
            'record_id' => $record_id,
            'user_id' => $user_id,
            'description' => $description
        ]);
    } catch (PDOException $e) {
        // Log to file if database logging fails
        error_log("Audit trail error: " . $e->getMessage());
    }
}

// Handle export functionality
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="appraisals_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Employee', 'Review Period', 'Overall Score', 'Review Date', 'Goals & KPIs', 'Manager Evaluation', 'Comments']);

    $stmt = $db->query("SELECT a.*, e.full_name FROM pm_appraisals a JOIN employees e ON a.employee_id = e.employee_id ORDER BY a.review_date DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['full_name'],
            $row['review_period'],
            $row['overall_score'],
            $row['review_date'],
            $row['goals_kpis'],
            $row['manager_evaluation'],
            $row['comments']
        ]);
    }
    fclose($output);
    exit;
}

// Fetch employees for dropdown
$stmt = $db->query("SELECT employee_id as id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch review templates
$stmt = $db->query("SELECT * FROM pm_review_templates WHERE is_active = 1 ORDER BY template_name");
$review_templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch appraisals with search and filters
$search = $_GET['search'] ?? '';
$filter_period = $_GET['filter_period'] ?? '';
$filter_score_min = $_GET['score_min'] ?? '';
$filter_score_max = $_GET['score_max'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'review_date';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Validate sort parameters
$valid_sort_by = ['review_date', 'overall_score', 'full_name'];
$valid_sort_order = ['ASC', 'DESC'];

if (!in_array($sort_by, $valid_sort_by)) {
    $sort_by = 'review_date';
}
if (!in_array($sort_order, $valid_sort_order)) {
    $sort_order = 'DESC';
}

$query = "
    SELECT a.*, e.full_name, e.department
    FROM pm_appraisals a 
    JOIN employees e ON a.employee_id = e.employee_id 
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND e.full_name LIKE :search";
    $params['search'] = "%$search%";
}

if (!empty($filter_period)) {
    $query .= " AND a.review_period = :period";
    $params['period'] = $filter_period;
}

if (!empty($filter_score_min)) {
    $query .= " AND a.overall_score >= :score_min";
    $params['score_min'] = $filter_score_min;
}

if (!empty($filter_score_max)) {
    $query .= " AND a.overall_score <= :score_max";
    $params['score_max'] = $filter_score_max;
}

// Build ORDER BY clause with proper table aliases
$sort_column_map = [
    'review_date' => 'a.review_date',
    'overall_score' => 'a.overall_score',
    'full_name' => 'e.full_name'
];

$sort_column = $sort_column_map[$sort_by];
$query .= " ORDER BY $sort_column $sort_order";

$stmt = $db->prepare($query);
$stmt->execute($params);
$appraisals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate performance metrics
$metrics = [];
if (!empty($appraisals)) {
    $total_appraisals = count($appraisals);
    $avg_score = array_sum(array_column($appraisals, 'overall_score')) / $total_appraisals;
    $high_performers = count(array_filter($appraisals, function($a) { return $a['overall_score'] >= 4.0; }));
    $needs_improvement = count(array_filter($appraisals, function($a) { return $a['overall_score'] < 3.0; }));

    $metrics = [
        'total_appraisals' => $total_appraisals,
        'average_score' => number_format($avg_score, 2),
        'high_performers' => $high_performers,
        'needs_improvement' => $needs_improvement,
        'high_performer_percentage' => $total_appraisals > 0 ? round(($high_performers / $total_appraisals) * 100, 1) : 0,
        'improvement_percentage' => $total_appraisals > 0 ? round(($needs_improvement / $total_appraisals) * 100, 1) : 0
    ];
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Appraisals & Review | Performance Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="../../assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <!-- jQuery UI -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="../custom.css" />
  <style>
    .ui-autocomplete {
      z-index: 1050;
      max-height: 200px;
      overflow-y: auto;
      overflow-x: hidden;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../performance.php" class="nav-link">Home</a>
        </li>
      </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
              <a href="../performance.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="360-degree.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>360-Degree Feedback</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Appraisals&review.php" class="nav-link active">
                <i class="nav-icon fas fa-edit"></i>
                <p>Appraisals & Review</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Goal&KPI.php" class="nav-link">
                <i class="nav-icon fas fa-tree"></i>
                <p>Goal & KPI</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Performancereport.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Performance Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="Training.php" class="nav-link">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Training</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Performance Evaluation Management</h1>
            </div>
            <div class="col-sm-6 text-right">
              <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addAppraisalModal">
                <i class="fas fa-plus"></i> Add New Appraisal
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?= $message ?>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Success/Error Messages -->
          <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle"></i> <?= $success_message ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-exclamation-triangle"></i> <?= $error_message ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
          <?php endif; ?>

          <!-- Performance Metrics Dashboard -->
          <?php if (!empty($metrics)): ?>
          <div class="row mb-4">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $metrics['total_appraisals'] ?></h3>
                  <p>Total Appraisals</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clipboard-list"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $metrics['average_score'] ?></h3>
                  <p>Average Score</p>
                </div>
                <div class="icon">
                  <i class="fas fa-chart-line"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $metrics['high_performers'] ?><sup style="font-size: 20px">%</sup></h3>
                  <p>High Performers</p>
                </div>
                <div class="icon">
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $metrics['needs_improvement'] ?></h3>
                  <p>Needs Improvement</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Enhanced Search and Filter Form -->
          <div class="card card-outline card-primary mb-3">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-search"></i> Search & Filter Appraisals</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <form action="" method="GET" id="filterForm">
                <div class="row align-items-end">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="search">Search Employee</label>
                      <input type="text" name="search" id="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="filter_period">Review Period</label>
                      <select name="filter_period" id="filter_period" class="form-control">
                        <option value="">All Periods</option>
                        <option value="Quarterly" <?= $filter_period == 'Quarterly' ? 'selected' : '' ?>>Quarterly</option>
                        <option value="Annual" <?= $filter_period == 'Annual' ? 'selected' : '' ?>>Annual</option>
                        <option value="Mid-Year" <?= $filter_period == 'Mid-Year' ? 'selected' : '' ?>>Mid-Year</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="score_min">Min Score</label>
                      <input type="number" name="score_min" id="score_min" class="form-control" step="0.1" min="0" max="5" value="<?= htmlspecialchars($filter_score_min) ?>" placeholder="0.0">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="score_max">Max Score</label>
                      <input type="number" name="score_max" id="score_max" class="form-control" step="0.1" min="0" max="5" value="<?= htmlspecialchars($filter_score_max) ?>" placeholder="5.0">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="sort_by">Sort By</label>
                      <select name="sort_by" id="sort_by" class="form-control">
                        <option value="review_date" <?= $sort_by == 'review_date' ? 'selected' : '' ?>>Review Date</option>
                        <option value="overall_score" <?= $sort_by == 'overall_score' ? 'selected' : '' ?>>Score</option>
                        <option value="full_name" <?= $sort_by == 'full_name' ? 'selected' : '' ?>>Employee Name</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-block">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-12">
                    <a href="?export=csv" class="btn btn-success btn-sm">
                      <i class="fas fa-download"></i> Export to CSV
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearFilters()">
                      <i class="fas fa-times"></i> Clear Filters
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Appraisals List Table with Bulk Operations -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-list"></i> Appraisals & Reviews (<?= count($appraisals) ?>)</h3>
              <div class="card-tools">
                <form action="" method="POST" id="bulkForm" style="display: inline;">
                  <input type="hidden" name="selected_appraisals" id="selectedIds">
                  <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display: none;" onclick="return confirm('Are you sure you want to delete selected appraisals?')">
                    <i class="fas fa-trash"></i> Delete Selected
                  </button>
                </form>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap" id="appraisalsTable">
                <thead>
                  <tr>
                    <th width="30">
                      <input type="checkbox" id="selectAll">
                    </th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Review Period</th>
                    <th>Overall Score</th>
                    <th>Review Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($appraisals)): ?>
                    <tr>
                      <td colspan="8" class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No appraisals found matching your criteria.</p>
                        <a href="#" data-toggle="modal" data-target="#addAppraisalModal" class="btn btn-primary btn-sm">
                          <i class="fas fa-plus"></i> Add First Appraisal
                        </a>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($appraisals as $appraisal): ?>
                      <tr>
                        <td>
                          <input type="checkbox" class="appraisal-checkbox" value="<?= $appraisal['appraisal_id'] ?>">
                        </td>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="mr-2">
                              <i class="fas fa-user-circle fa-lg text-primary"></i>
                            </div>
                            <div>
                              <strong><?= htmlspecialchars($appraisal['full_name']) ?></strong>
                            </div>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($appraisal['department'] ?? 'N/A') ?></td>
                        <td>
                          <span class="badge badge-info"><?= htmlspecialchars($appraisal['review_period']) ?></span>
                        </td>
                        <td>
                          <div class="d-flex align-items-center">
                            <span class="mr-2 font-weight-bold <?= $appraisal['overall_score'] >= 4.0 ? 'text-success' : ($appraisal['overall_score'] >= 3.0 ? 'text-warning' : 'text-danger') ?>">
                              <?= number_format($appraisal['overall_score'], 1) ?>/5.0
                            </span>
                            <div class="progress" style="width: 60px; height: 6px;">
                              <div class="progress-bar bg-<?= $appraisal['overall_score'] >= 4.0 ? 'success' : ($appraisal['overall_score'] >= 3.0 ? 'warning' : 'danger') ?>"
                                   style="width: <?= ($appraisal['overall_score'] / 5) * 100 ?>%"></div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <i class="fas fa-calendar-alt text-muted mr-1"></i>
                          <?= date('M d, Y', strtotime($appraisal['review_date'])) ?>
                        </td>
                        <td>
                          <?php
                          $days_since = (strtotime('now') - strtotime($appraisal['review_date'])) / (60*60*24);
                          if ($days_since <= 30) {
                            echo '<span class="badge badge-success">Recent</span>';
                          } elseif ($days_since <= 90) {
                            echo '<span class="badge badge-warning">Current</span>';
                          } else {
                            echo '<span class="badge badge-secondary">Historical</span>';
                          }
                          ?>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info edit-appraisal-btn"
                                    data-id="<?= $appraisal['appraisal_id'] ?>"
                                    data-employee="<?= $appraisal['employee_id'] ?>"
                                    data-period="<?= $appraisal['review_period'] ?>"
                                    data-goals="<?= htmlspecialchars($appraisal['goals_kpis']) ?>"
                                    data-ratings='<?= htmlspecialchars($appraisal['performance_ratings'], ENT_QUOTES) ?>'
                                    data-evaluation="<?= htmlspecialchars($appraisal['manager_evaluation']) ?>"
                                    data-score="<?= $appraisal['overall_score'] ?>"
                                    data-comments="<?= htmlspecialchars($appraisal['comments']) ?>"
                                    data-date="<?= $appraisal['review_date'] ?>"
                                    title="Edit Appraisal">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary view-appraisal-btn"
                                    data-id="<?= $appraisal['appraisal_id'] ?>"
                                    data-employee="<?= htmlspecialchars($appraisal['full_name']) ?>"
                                    data-period="<?= $appraisal['review_period'] ?>"
                                    data-goals="<?= htmlspecialchars($appraisal['goals_kpis']) ?>"
                                    data-ratings='<?= htmlspecialchars($appraisal['performance_ratings'], ENT_QUOTES) ?>'
                                    data-evaluation="<?= htmlspecialchars($appraisal['manager_evaluation']) ?>"
                                    data-score="<?= $appraisal['overall_score'] ?>"
                                    data-comments="<?= htmlspecialchars($appraisal['comments']) ?>"
                                    data-date="<?= $appraisal['review_date'] ?>"
                                    title="View Details">
                              <i class="fas fa-eye"></i>
                            </button>
                            <form action="" method="POST" style="display:inline-block;">
                              <input type="hidden" name="appraisal_id" value="<?= $appraisal['appraisal_id'] ?>">
                              <button type="submit" name="delete_appraisal" class="btn btn-sm btn-danger"
                                      onclick="return confirm('Are you sure you want to delete this appraisal?')"
                                      title="Delete Appraisal">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Add Appraisal Modal with Templates -->
  <div class="modal fade" id="addAppraisalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Appraisal</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" method="POST" id="addAppraisalForm">
          <div class="modal-body">
            <!-- Template Selection -->
            <div class="form-group">
              <label>Use Review Template (Optional)</label>
              <select name="template_id" id="templateSelect" class="form-control">
                <option value="">Select a template or create custom...</option>
                <?php foreach ($review_templates as $template): ?>
                  <option value="<?= $template['template_id'] ?>" data-categories='<?= htmlspecialchars($template['rating_categories']) ?>'>
                    <?= htmlspecialchars($template['template_name']) ?> (<?= $template['review_period'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Employee <span class="text-danger">*</span></label>
                  <select name="employee_id" id="add_employee_id" class="form-control" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Review Period <span class="text-danger">*</span></label>
                  <select name="review_period" id="add_review_period" class="form-control" required>
                    <option value="Quarterly">Quarterly</option>
                    <option value="Annual">Annual</option>
                    <option value="Mid-Year">Mid-Year</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Review Date <span class="text-danger">*</span></label>
                  <input type="date" name="review_date" id="add_review_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Goals & KPIs <span class="text-danger">*</span></label>
              <textarea name="goals_kpis" id="add_goals_kpis" class="form-control" rows="3" placeholder="Enter goals and KPIs for this review period" required></textarea>
            </div>

            <div class="form-group">
              <label>Performance Ratings (1-5) <span class="text-danger">*</span></label>
              <div id="add-ratings-container">
                <div class="rating-item mb-2">
                  <div class="row">
                    <div class="col-md-6">
                      <input type="text" name="ratings[0][name]" class="form-control rating-name" placeholder="Rating Category (e.g., Communication)" required>
                    </div>
                    <div class="col-md-4">
                      <input type="number" name="ratings[0][score]" class="form-control rating-score" min="1" max="5" step="0.1" placeholder="Score" required>
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-sm btn-danger remove-rating-btn">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-secondary" id="add-rating-btn">
                <i class="fas fa-plus"></i> Add Rating Category
              </button>
              <button type="button" class="btn btn-sm btn-info ml-2" id="autoCalculateBtn">
                <i class="fas fa-calculator"></i> Auto-Calculate Overall Score
              </button>
            </div>

            <div class="form-group">
              <label>Manager's Evaluation <span class="text-danger">*</span></label>
              <textarea name="manager_evaluation" id="add_manager_evaluation" class="form-control" rows="4" placeholder="Enter manager's evaluation and feedback" required></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Overall Score <span class="text-danger">*</span></label>
                  <input type="number" name="overall_score" id="add_overall_score" class="form-control" step="0.1" min="0" max="5" placeholder="0.0 - 5.0" required>
                  <small class="form-text text-muted">Score will be auto-calculated based on ratings or enter manually</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Additional Comments</label>
                  <textarea name="comments" id="add_comments" class="form-control" rows="3" placeholder="Additional comments or notes..."></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times"></i> Close
            </button>
            <button type="submit" name="add_appraisal" class="btn btn-primary">
              <i class="fas fa-save"></i> Save Appraisal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Appraisal Modal -->
  <div class="modal fade" id="editAppraisalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title">Edit Appraisal</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <input type="hidden" name="appraisal_id" id="edit_appraisal_id">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Employee</label>
                  <select name="employee_id" id="edit_employee_id" class="form-control" required>
                    <?php foreach ($employees as $employee): ?>
                      <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['full_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Review Period</label>
                  <select name="review_period" id="edit_review_period" class="form-control" required>
                    <option value="Quarterly">Quarterly</option>
                    <option value="Annual">Annual</option>
                    <option value="Mid-Year">Mid-Year</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Review Date</label>
                  <input type="date" name="review_date" id="edit_review_date" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Goals & KPIs</label>
              <textarea name="goals_kpis" id="edit_goals_kpis" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label>Performance Ratings (1-5)</label>
              <div id="edit-ratings-container"></div>
              <button type="button" class="btn btn-sm btn-secondary" id="edit-add-rating-btn">Add Rating Category</button>
            </div>
            <div class="form-group">
              <label>Manager's Evaluation</label>
              <textarea name="manager_evaluation" id="edit_manager_evaluation" class="form-control" rows="4" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Overall Score</label>
                  <input type="number" name="overall_score" id="edit_overall_score" class="form-control" step="0.01" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Comments & Feedback</label>
                  <textarea name="comments" id="edit_comments" class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="update_appraisal" class="btn btn-info">Update Appraisal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery UI -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <!-- Select2 -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize Select2 Elements
      $('#add_employee_id, #edit_employee_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Employee',
        allowClear: true
      });

      // Search Autocomplete
      $('#search').autocomplete({
        source: function(request, response) {
          $.getJSON('../get_suggestions.php', {
            term: request.term,
            type: 'employee'
          }, response);
        },
        minLength: 1,
        select: function(event, ui) {
          $('#search').val(ui.item.label);
          $(this).closest('form').submit();
        }
      });

      // Template Selection Handler
      $('#templateSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const categories = selectedOption.data('categories');

        if (categories && categories.length > 0) {
          const container = $('#add-ratings-container');
          container.empty();

          categories.forEach(function(category, index) {
            const ratingHtml = `
              <div class="rating-item mb-2">
                <div class="row">
                  <div class="col-md-6">
                    <input type="text" name="ratings[${index}][name]" class="form-control rating-name" value="${category}" required>
                  </div>
                  <div class="col-md-4">
                    <input type="number" name="ratings[${index}][score]" class="form-control rating-score" min="1" max="5" step="0.1" placeholder="Score" required>
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-rating-btn">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>`;
            container.append(ratingHtml);
          });
        }
      });

      // Auto-Calculate Overall Score
      $('#autoCalculateBtn').on('click', function() {
        const ratingScores = $('.rating-score');
        let totalScore = 0;
        let count = 0;

        ratingScores.each(function() {
          const score = parseFloat($(this).val());
          if (!isNaN(score) && score >= 1 && score <= 5) {
            totalScore += score;
            count++;
          }
        });

        if (count > 0) {
          const averageScore = (totalScore / count).toFixed(1);
          $('#add_overall_score').val(averageScore);
          alert(`Overall score calculated: ${averageScore} (average of ${count} ratings)`);
        } else {
          alert('Please enter valid rating scores first.');
        }
      });

      // Add Rating Button
      let ratingIndex = 1;
      $('#add-rating-btn').on('click', function() {
        const newRating = `
          <div class="rating-item mb-2">
            <div class="row">
              <div class="col-md-6">
                <input type="text" name="ratings[${ratingIndex}][name]" class="form-control rating-name" placeholder="Rating Category" required>
              </div>
              <div class="col-md-4">
                <input type="number" name="ratings[${ratingIndex}][score]" class="form-control rating-score" min="1" max="5" step="0.1" placeholder="Score" required>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-rating-btn">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>`;
        $('#add-ratings-container').append(newRating);
        ratingIndex++;
      });

      // Remove Rating Button
      $(document).on('click', '.remove-rating-btn', function() {
        $(this).closest('.rating-item').remove();
      });

      // Bulk Operations
      $('#selectAll').on('change', function() {
        $('.appraisal-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkDeleteButton();
      });

      $(document).on('change', '.appraisal-checkbox', function() {
        const allChecked = $('.appraisal-checkbox:checked').length === $('.appraisal-checkbox').length;
        $('#selectAll').prop('checked', allChecked);
        toggleBulkDeleteButton();
      });

      function toggleBulkDeleteButton() {
        const checkedCount = $('.appraisal-checkbox:checked').length;
        if (checkedCount > 0) {
          $('#bulkDeleteBtn').show();
          $('#selectedIds').val($('.appraisal-checkbox:checked').map(function() {
            return this.value;
          }).get().join(','));
        } else {
          $('#bulkDeleteBtn').hide();
        }
      }

      // Clear Filters Function
      window.clearFilters = function() {
        $('#search').val('');
        $('#filter_period').val('');
        $('#score_min').val('');
        $('#score_max').val('');
        $('#sort_by').val('review_date');
        $('#filterForm').submit();
      };

      // Edit Appraisal Modal
      let editRatingIndex = 0;
      $('.edit-appraisal-btn').on('click', function() {
        const id = $(this).data('id');
        const employee = $(this).data('employee');
        const period = $(this).data('period');
        const goals = $(this).data('goals');
        const ratings = $(this).data('ratings');
        const evaluation = $(this).data('evaluation');
        const score = $(this).data('score');
        const comments = $(this).data('comments');
        const date = $(this).data('date');

        $('#edit_appraisal_id').val(id);
        $('#edit_employee_id').val(employee);
        $('#edit_review_period').val(period);
        $('#edit_goals_kpis').val(goals);
        $('#edit_manager_evaluation').val(evaluation);
        $('#edit_overall_score').val(score);
        $('#edit_comments').val(comments);
        $('#edit_review_date').val(date);

        const container = $('#edit-ratings-container');
        container.empty();
        editRatingIndex = 0;

        try {
          const ratingsData = typeof ratings === 'string' ? JSON.parse(ratings) : ratings;
          if (ratingsData && ratingsData.length > 0) {
            ratingsData.forEach(function(rating) {
              const newRating = `
                <div class="rating-item mb-2">
                  <div class="row">
                    <div class="col-md-8">
                      <input type="text" name="ratings[${editRatingIndex}][name]" class="form-control" value="${rating.name || ''}" required>
                    </div>
                    <div class="col-md-3">
                      <input type="number" name="ratings[${editRatingIndex}][score]" class="form-control" value="${rating.score || ''}" min="1" max="5" step="0.1" required>
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-sm btn-danger remove-rating-btn">&times;</button>
                    </div>
                  </div>
                </div>`;
              container.append(newRating);
              editRatingIndex++;
            });
          }
        } catch (e) {
          console.error('Error parsing ratings data:', e);
        }

        $('#editAppraisalModal').modal('show');
      });

      $('#edit-add-rating-btn').on('click', function() {
        const newRating = `
          <div class="rating-item mb-2">
            <div class="row">
              <div class="col-md-8">
                <input type="text" name="ratings[${editRatingIndex}][name]" class="form-control" placeholder="Rating Category" required>
              </div>
              <div class="col-md-3">
                <input type="number" name="ratings[${editRatingIndex}][score]" class="form-control" min="1" max="5" step="0.1" placeholder="Score" required>
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-rating-btn">&times;</button>
              </div>
            </div>
          </div>`;
        $('#edit-ratings-container').append(newRating);
        editRatingIndex++;
      });

      // View Appraisal Modal Handler
      $('.view-appraisal-btn').on('click', function() {
        const employee = $(this).data('employee');
        const period = $(this).data('period');
        const goals = $(this).data('goals');
        const ratings = $(this).data('ratings');
        const evaluation = $(this).data('evaluation');
        const score = $(this).data('score');
        const comments = $(this).data('comments');
        const date = $(this).data('date');

        let ratingsHtml = '<p>No ratings available.</p>';
        try {
          const ratingsData = typeof ratings === 'string' ? JSON.parse(ratings) : ratings;
          if (ratingsData && ratingsData.length > 0) {
            ratingsHtml = '<ul class="list-group">';
            ratingsData.forEach(function(rating) {
              ratingsHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                ${rating.name || 'Unnamed'}
                <span class="badge badge-primary badge-pill">${rating.score || 'N/A'}/5</span>
              </li>`;
            });
            ratingsHtml += '</ul>';
          }
        } catch (e) {
          console.error('Error parsing ratings data:', e);
        }

        const viewModal = `
          <div class="modal fade" id="viewAppraisalModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header bg-success">
                  <h5 class="modal-title"><i class="fas fa-eye"></i> View Appraisal - ${employee}</h5>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6">
                      <strong>Review Period:</strong> ${period}<br>
                      <strong>Review Date:</strong> ${new Date(date).toLocaleDateString()}<br>
                      <strong>Overall Score:</strong> <span class="badge badge-${score >= 4 ? 'success' : (score >= 3 ? 'warning' : 'danger')}">${score}/5.0</span>
                    </div>
                    <div class="col-md-6">
                      <strong>Goals & KPIs:</strong><br>
                      <p class="text-muted">${goals}</p>
                    </div>
                  </div>
                  <hr>
                  <strong>Performance Ratings:</strong><br>
                  ${ratingsHtml}
                  <hr>
                  <strong>Manager's Evaluation:</strong><br>
                  <p class="text-muted">${evaluation}</p>
                  ${comments ? '<hr><strong>Additional Comments:</strong><br><p class="text-muted">' + comments + '</p>' : ''}
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>`;

        $('body').append(viewModal);
        $('#viewAppraisalModal').modal('show');
        $('#viewAppraisalModal').on('hidden.bs.modal', function() {
          $(this).remove();
        });
      });
    });
  </script>
</body>

</html>
