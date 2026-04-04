<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();

$selected_employee_id = $_GET['employee_id'] ?? null;
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 year'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Fetch all employees for the dropdown
$stmt = $db->query("SELECT employee_id as id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$report_data = null;
if ($selected_employee_id) {
    // 1. Employee Info
    $stmt = $db->prepare("SELECT e.employee_id as id, e.full_name, e.department, e.position, e.date_hired, u.username FROM employees e LEFT JOIN users u ON e.user_id = u.id WHERE e.employee_id = ?");
    $stmt->execute([$selected_employee_id]);
    $employee_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee_info) {
        // 2. Appraisal Summary (within date range)
        $stmt = $db->prepare("SELECT AVG(overall_score) as avg_score, COUNT(*) as total_reviews FROM pm_appraisals WHERE employee_id = ? AND review_date BETWEEN ? AND ?");
        $stmt->execute([$selected_employee_id, $start_date, $end_date]);
        $appraisal_summary = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['avg_score' => 0, 'total_reviews' => 0];

        // 3. Goal Summary
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_goals,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_goals,
                AVG((current_progress / target_value) * 100) as avg_progress
            FROM pm_goals 
            WHERE employee_id = ? AND target_value > 0
        ");
        $stmt->execute([$selected_employee_id]);
        $goal_summary = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_goals' => 0, 'completed_goals' => 0, 'avg_progress' => 0];

        // 4. Training Summary
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_training,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_training
            FROM pm_training_recommendations 
            WHERE employee_id = ?
        ");
        $stmt->execute([$selected_employee_id]);
        $training_summary = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_training' => 0, 'completed_training' => 0];

        // 5. Recent 360 Feedback (within date range)
        $stmt = $db->prepare("SELECT * FROM pm_360_feedback WHERE employee_id = ? AND evaluation_date BETWEEN ? AND ? ORDER BY evaluation_date DESC LIMIT 5");
        $stmt->execute([$selected_employee_id, $start_date, $end_date]);
        $recent_feedback = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 6. Recent Appraisals (within date range)
        $stmt = $db->prepare("SELECT * FROM pm_appraisals WHERE employee_id = ? AND review_date BETWEEN ? AND ? ORDER BY review_date DESC LIMIT 5");
        $stmt->execute([$selected_employee_id, $start_date, $end_date]);
        $recent_appraisals = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 7. All Goals
        $stmt = $db->prepare("SELECT * FROM pm_goals WHERE employee_id = ? ORDER BY end_date ASC");
        $stmt->execute([$selected_employee_id]);
        $all_goals = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 8. Competency Analysis (within date range)
        $stmt = $db->prepare("
            SELECT 
                c.competency_name,
                c.category,
                AVG(cfb.rating) as avg_rating,
                COUNT(cfb.rating) as feedback_count,
                GROUP_CONCAT(DISTINCT cfb.strengths SEPARATOR '; ') as strengths,
                GROUP_CONCAT(DISTINCT cfb.improvement_areas SEPARATOR '; ') as improvement_areas
            FROM pm_competencies c
            LEFT JOIN pm_360_competency_feedback cfb ON c.competency_id = cfb.competency_id
            LEFT JOIN pm_360_feedback f ON cfb.feedback_id = f.feedback_id
            WHERE f.employee_id = ? AND f.evaluation_date BETWEEN ? AND ?
            GROUP BY c.competency_id, c.competency_name, c.category
            HAVING feedback_count > 0
            ORDER BY avg_rating DESC
        ");
        $stmt->execute([$selected_employee_id, $start_date, $end_date]);
        $competency_analysis = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 9. Training Recommendations
        $stmt = $db->prepare("SELECT * FROM pm_training_recommendations WHERE employee_id = ? ORDER BY suggested_completion_date ASC");
        $stmt->execute([$selected_employee_id]);
        $all_training = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 10. Development Action Plans
        $stmt = $db->prepare("
            SELECT 
                ap.action_description,
                ap.priority,
                ap.target_date,
                ap.status,
                ap.progress_notes,
                e.full_name as assigned_to_name
            FROM pm_feedback_action_plans ap
            LEFT JOIN pm_360_feedback f ON ap.feedback_id = f.feedback_id
            LEFT JOIN employees e ON ap.assigned_to = e.employee_id
            WHERE f.employee_id = ?
            ORDER BY 
                CASE ap.priority 
                    WHEN 'High' THEN 1 
                    WHEN 'Medium' THEN 2 
                    WHEN 'Low' THEN 3 
                END,
                ap.target_date ASC
        ");
        $stmt->execute([$selected_employee_id]);
        $action_plans = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 11. Performance Trend Data for Chart
        $stmt = $db->prepare("
            SELECT 
                DATE_FORMAT(review_date, '%Y-%m') as month,
                AVG(overall_score) as avg_score,
                COUNT(*) as review_count
            FROM pm_appraisals 
            WHERE employee_id = ? AND review_date BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(review_date, '%Y-%m')
            ORDER BY month ASC
        ");
        $stmt->execute([$selected_employee_id, $start_date, $end_date]);
        $performance_trend = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $report_data = [
            'employee' => $employee_info,
            'appraisal_summary' => $appraisal_summary,
            'goal_summary' => $goal_summary,
            'training_summary' => $training_summary,
            'feedback' => $recent_feedback,
            'appraisals' => $recent_appraisals,
            'goals' => $all_goals,
            'training' => $all_training,
            'competency_analysis' => $competency_analysis,
            'action_plans' => $action_plans,
            'performance_trend' => $performance_trend
        ];
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Performance Report | Performance Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="../../assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- html2pdf.js for PDF export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <style>
    @media print {
      .no-print { display: none !important; }
      .content-wrapper { margin-left: 0 !important; }
      .main-sidebar, .main-header, .main-footer { display: none !important; }
      .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
    .report-header { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark no-print">
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
    <aside class="main-sidebar sidebar-dark-primary elevation-4 no-print">
      <a href="../performance.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: 0.9" />
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
              <a href="Appraisals&review.php" class="nav-link">
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
              <a href="Performancereport.php" class="nav-link active">
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
      <div class="content-header no-print">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Comprehensive Performance Report</h1>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          
          <!-- Employee Selector (no-print) -->
          <div class="card card-outline card-primary no-print">
            <div class="card-body">
              <form action="" method="GET" class="form-inline">
                <label class="mr-2" for="employee_id">Select Employee to Generate Report:</label>
                <select name="employee_id" id="employee_id" class="form-control mr-2" required>
                  <option value="">-- Choose Employee --</option>
                  <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $selected_employee_id == $emp['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($emp['full_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-group mr-2">
                  <label for="start_date" class="mr-1">From:</label>
                  <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $start_date ?>" required>
                </div>
                <div class="form-group mr-2">
                  <label for="end_date" class="mr-1">To:</label>
                  <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $end_date ?>" required>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Generate Report</button>
                <?php if ($report_data): ?>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-secondary" onclick="window.print()">
                      <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToPDF()">
                      <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                  </div>
                <?php endif; ?>
              </form>
            </div>
          </div>

          <?php if ($report_data): ?>
            <!-- Report Content -->
            <div id="printableReport">
              <div class="report-header text-center">
                <h2>Performance Evaluation Report</h2>
                <p class="text-muted">Generated on <?= date('M d, Y') ?></p>
              </div>

              <!-- Employee Details -->
              <div class="row">
                <div class="col-md-6">
                  <h5><strong>Employee Information</strong></h5>
                  <table class="table table-sm">
                    <tr><td width="40%">Name:</td><td><strong><?= htmlspecialchars($report_data['employee']['full_name']) ?></strong></td></tr>
                    <tr><td>Employee ID:</td><td>#<?= $report_data['employee']['id'] ?></td></tr>
                    <tr><td>Username:</td><td><?= htmlspecialchars($report_data['employee']['username'] ?? 'N/A') ?></td></tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <h5><strong>Performance Snapshot</strong></h5>
                  <div class="row">
                    <div class="col-4 text-center border-right">
                      <h3 class="text-primary"><?= number_format($report_data['appraisal_summary']['avg_score'] ?? 0, 1) ?></h3>
                      <small class="text-muted uppercase">Avg Rating</small>
                    </div>
                    <div class="col-4 text-center border-right">
                      <h3 class="text-success"><?= number_format($report_data['goal_summary']['avg_progress'] ?? 0, 0) ?>%</h3>
                      <small class="text-muted uppercase">Goal Progress</small>
                    </div>
                    <div class="col-4 text-center">
                      <h3 class="text-info"><?= $report_data['training_summary']['completed_training'] ?>/<?= $report_data['training_summary']['total_training'] ?></h3>
                      <small class="text-muted uppercase">Training</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Performance Trend Chart -->
              <div class="row mt-4">
                <div class="col-12">
                  <h5><i class="fas fa-chart-line text-primary mr-2"></i> Performance Trend (<?= date('M Y', strtotime($start_date)) ?> - <?= date('M Y', strtotime($end_date)) ?>)</h5>
                  <div class="card">
                    <div class="card-body">
                      <canvas id="performanceChart" height="100"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <hr>

              <!-- Goals Progress Section -->
              <div class="row mt-4">
                <div class="col-12">
                  <h5><i class="fas fa-bullseye text-primary mr-2"></i> Goals & KPI Progress</h5>
                  <table class="table table-bordered table-striped mt-2">
                    <thead>
                      <tr>
                        <th>Goal Title</th>
                        <th>KPI</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Timeline</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($report_data['goals'])): ?>
                        <tr><td colspan="5" class="text-center">No goals assigned.</td></tr>
                      <?php else: ?>
                        <?php foreach ($report_data['goals'] as $goal): ?>
                          <?php $pct = ($goal['target_value'] > 0) ? ($goal['current_progress'] / $goal['target_value']) * 100 : 0; ?>
                          <tr>
                            <td><?= htmlspecialchars($goal['goal_title']) ?></td>
                            <td><?= htmlspecialchars($goal['kpi_name']) ?></td>
                            <td>
                              <div class="progress progress-xs">
                                <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                              </div>
                              <small><?= number_format($pct, 0) ?>%</small>
                            </td>
                            <td><span class="badge badge-<?= $goal['status'] == 'Completed' ? 'success' : 'primary' ?>"><?= $goal['status'] ?></span></td>
                            <td><small><?= date('M Y', strtotime($goal['start_date'])) ?> - <?= date('M Y', strtotime($goal['end_date'])) ?></small></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Appraisal & 360 Feedback Section -->
              <div class="row mt-4">
                <div class="col-md-6">
                  <h5><i class="fas fa-edit text-info mr-2"></i> Recent Appraisals</h5>
                  <div class="list-group">
                    <?php if (empty($report_data['appraisals'])): ?>
                      <p class="text-muted p-2">No appraisal records found.</p>
                    <?php else: ?>
                      <?php foreach ($report_data['appraisals'] as $app): ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                          <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><strong><?= $app['review_period'] ?> Review</strong></h6>
                            <span class="badge badge-info"><?= number_format($app['overall_score'], 1) ?> / 5.0</span>
                          </div>
                          <p class="mb-1 small"><?= htmlspecialchars($app['manager_evaluation']) ?></p>
                          <small class="text-muted">Date: <?= date('M d, Y', strtotime($app['review_date'])) ?></small>
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <h5><i class="fas fa-comments text-warning mr-2"></i> Recent 360-Degree Feedback</h5>
                  <div class="list-group">
                    <?php if (empty($report_data['feedback'])): ?>
                      <p class="text-muted p-2">No multi-source feedback found.</p>
                    <?php else: ?>
                      <?php foreach ($report_data['feedback'] as $fb): ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                          <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><strong><?= $fb['category'] ?></strong></h6>
                            <small class="text-muted"><?= $fb['evaluator_type'] ?></small>
                          </div>
                          <div class="mb-1">
                            <?php for($i=1; $i<=5; $i++): ?>
                              <i class="fas fa-star <?= $i <= $fb['rating'] ? 'text-warning' : 'text-muted' ?> small"></i>
                            <?php endfor; ?>
                          </div>
                          <p class="mb-1 small">"<?= htmlspecialchars($fb['comments']) ?>"</p>
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- Training Section -->
              <div class="row mt-4 mb-5">
                <div class="col-12">
                  <h5><i class="fas fa-graduation-cap text-success mr-2"></i> Training & Development History</h5>
                  <table class="table table-sm table-bordered mt-2">
                    <thead>
                      <tr>
                        <th>Program</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Target Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($report_data['training'])): ?>
                        <tr><td colspan="5" class="text-center">No training records found.</td></tr>
                      <?php else: ?>
                        <?php foreach ($report_data['training'] as $train): ?>
                          <tr>
                            <td><?= htmlspecialchars($train['training_program']) ?></td>
                            <td><?= $train['training_type'] ?></td>
                            <td><span class="badge badge-<?= $train['priority_level'] == 'High' ? 'danger' : ($train['priority_level'] == 'Medium' ? 'warning' : 'info') ?>"><?= $train['priority_level'] ?></span></td>
                            <td><?= $train['status'] ?></td>
                            <td><?= date('M d, Y', strtotime($train['suggested_completion_date'])) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Competency Analysis Section -->
              <div class="row mt-4">
                <div class="col-12">
                  <h5><i class="fas fa-chart-line text-info mr-2"></i> Competency Analysis (Last 12 Months)</h5>
                  <?php if (empty($report_data['competency_analysis'])): ?>
                    <div class="alert alert-info">
                      <i class="fas fa-info-circle mr-2"></i> No competency feedback available for analysis.
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped mt-2">
                        <thead>
                          <tr>
                            <th>Competency</th>
                            <th>Category</th>
                            <th>Average Rating</th>
                            <th>Feedback Count</th>
                            <th>Key Strengths</th>
                            <th>Areas for Improvement</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($report_data['competency_analysis'] as $competency): ?>
                            <tr>
                              <td><strong><?= htmlspecialchars($competency['competency_name']) ?></strong></td>
                              <td><span class="badge badge-secondary"><?= $competency['category'] ?></span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <span class="mr-2"><strong><?= number_format($competency['avg_rating'], 1) ?>/5.0</strong></span>
                                  <div class="progress" style="width: 60px; height: 8px;">
                                    <div class="progress-bar bg-info" style="width: <?= ($competency['avg_rating'] / 5) * 100 ?>%"></div>
                                  </div>
                                </div>
                              </td>
                              <td><span class="badge badge-light"><?= $competency['feedback_count'] ?> reviews</span></td>
                              <td>
                                <?php if ($competency['strengths']): ?>
                                  <small class="text-success"><?= htmlspecialchars(substr($competency['strengths'], 0, 100)) ?><?php if (strlen($competency['strengths']) > 100): ?>...<?php endif; ?></small>
                                <?php else: ?>
                                  <small class="text-muted">Not specified</small>
                                <?php endif; ?>
                              </td>
                              <td>
                                <?php if ($competency['improvement_areas']): ?>
                                  <small class="text-warning"><?= htmlspecialchars(substr($competency['improvement_areas'], 0, 100)) ?><?php if (strlen($competency['improvement_areas']) > 100): ?>...<?php endif; ?></small>
                                <?php else: ?>
                                  <small class="text-muted">Not specified</small>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Development Action Plans Section -->
              <div class="row mt-4">
                <div class="col-12">
                  <h5><i class="fas fa-tasks text-warning mr-2"></i> Development Action Plans</h5>
                  <?php if (empty($report_data['action_plans'])): ?>
                    <div class="alert alert-info">
                      <i class="fas fa-info-circle mr-2"></i> No development action plans available.
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped mt-2">
                        <thead>
                          <tr>
                            <th>Action Description</th>
                            <th>Priority</th>
                            <th>Target Date</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Progress Notes</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($report_data['action_plans'] as $plan): ?>
                            <tr>
                              <td><strong><?= htmlspecialchars($plan['action_description']) ?></strong></td>
                              <td>
                                <span class="badge badge-<?= $plan['priority'] == 'High' ? 'danger' : ($plan['priority'] == 'Medium' ? 'warning' : 'info') ?>">
                                  <?= $plan['priority'] ?>
                                </span>
                              </td>
                              <td>
                                <span class="<?= strtotime($plan['target_date']) < time() && $plan['status'] != 'Completed' ? 'text-danger' : 'text-dark' ?>">
                                  <?= date('M d, Y', strtotime($plan['target_date'])) ?>
                                </span>
                              </td>
                              <td>
                                <span class="badge badge-<?= $plan['status'] == 'Completed' ? 'success' : ($plan['status'] == 'In Progress' ? 'primary' : 'secondary') ?>">
                                  <?= $plan['status'] ?>
                                </span>
                              </td>
                              <td><?= htmlspecialchars($plan['assigned_to_name'] ?? 'Not assigned') ?></td>
                              <td>
                                <?php if ($plan['progress_notes']): ?>
                                  <small><?= htmlspecialchars(substr($plan['progress_notes'], 0, 80)) ?><?php if (strlen($plan['progress_notes']) > 80): ?>...<?php endif; ?></small>
                                <?php else: ?>
                                  <small class="text-muted">No updates</small>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="text-center mt-5 mb-3 visible-print-block">
                <hr>
                <div class="row">
                  <div class="col-6">
                    <p>__________________________<br>Employee Signature</p>
                  </div>
                  <div class="col-6">
                    <p>__________________________<br>Manager Signature</p>
                  </div>
                </div>
              </div>
            </div>
          <?php elseif ($selected_employee_id): ?>
            <div class="alert alert-warning">No data found for the selected employee.</div>
          <?php else: ?>
            <div class="alert alert-info">Please select an employee above to generate their performance report.</div>
          <?php endif; ?>

        </div>
      </section>
    </div>
  </div>

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 -->
  <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script>
    $(function () {
      // Initialize Select2 Elements
      $('#employee_id').select2({
        theme: 'bootstrap4',
        placeholder: '-- Choose Employee --',
        allowClear: true
      });

      // Performance Trend Chart
      <?php if ($report_data && !empty($report_data['performance_trend'])): ?>
      const ctx = document.getElementById('performanceChart').getContext('2d');
      const performanceData = <?= json_encode($report_data['performance_trend']) ?>;

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: performanceData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
          }),
          datasets: [{
            label: 'Average Performance Rating',
            data: performanceData.map(item => parseFloat(item.avg_score)),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 5,
              ticks: {
                stepSize: 0.5
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'top'
            }
          }
        }
      });
      <?php endif; ?>
    });

    // PDF Export Function
    function exportToPDF() {
      const element = document.getElementById('printableReport');
      const opt = {
        margin: 1,
        filename: 'Performance_Report_<?= htmlspecialchars($report_data['employee']['full_name'] ?? 'Employee') ?>_<?= date('Y-m-d') ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
      };

      // Temporarily hide non-print elements for PDF
      const noPrintElements = document.querySelectorAll('.no-print');
      noPrintElements.forEach(el => el.style.display = 'none');

      html2pdf().set(opt).from(element).save().then(() => {
        // Restore non-print elements
        noPrintElements.forEach(el => el.style.display = '');
      });
    }
  </script>
</body>

</html>
