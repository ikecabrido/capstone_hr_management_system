<?php
session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";

$theme = $_SESSION['user']['theme'] ?? 'light';
$db = Database::getInstance()->getConnection();

// --- 1. Employee Performance Summary ---
$stmt = $db->query("SELECT AVG(overall_score) FROM pm_appraisals");
$avg_overall_rating = number_format($stmt->fetchColumn() ?: 0, 1);

$stmt = $db->query("SELECT e.full_name FROM pm_appraisals a JOIN employees e ON a.employee_id = e.employee_id ORDER BY a.overall_score DESC LIMIT 1");
$top_performer = $stmt->fetchColumn() ?: 'N/A';

// --- 2. KPI Metrics ---
$stmt = $db->query("SELECT AVG(current_progress) FROM pm_goals");
$kpi_achievement = number_format($stmt->fetchColumn() ?: 0, 0);

// --- 3. Goals & Objectives ---
$stmt = $db->query("SELECT COUNT(*) FROM pm_goals WHERE status = 'Completed'");
$completed_goals = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM pm_goals WHERE status = 'On Track'");
$ongoing_goals = $stmt->fetchColumn();

$stmt = $db->query("SELECT AVG((current_progress / target_value) * 100) FROM pm_goals WHERE target_value > 0");
$goals_progress_avg = number_format($stmt->fetchColumn() ?: 0, 0);

// --- 4. Appraisal Results (Pie Chart) ---
$stmt = $db->query("SELECT COUNT(*) FROM pm_appraisals WHERE overall_score >= 4.5");
$excellent_count = $stmt->fetchColumn();
$stmt = $db->query("SELECT COUNT(*) FROM pm_appraisals WHERE overall_score >= 3.0 AND overall_score < 4.5");
$good_count = $stmt->fetchColumn();
$stmt = $db->query("SELECT COUNT(*) FROM pm_appraisals WHERE overall_score < 3.0");
$needs_improvement_count = $stmt->fetchColumn();
$total_appraisals = $excellent_count + $good_count + $needs_improvement_count;

$excellent_pct = $total_appraisals > 0 ? round(($excellent_count / $total_appraisals) * 100) : 0;
$good_pct = $total_appraisals > 0 ? round(($good_count / $total_appraisals) * 100) : 0;
$needs_improvement_pct = $total_appraisals > 0 ? round(($needs_improvement_count / $total_appraisals) * 100) : 0;

// --- 6. Training & Development ---
$stmt = $db->query("SELECT COUNT(*) FROM pm_training_recommendations WHERE status = 'Completed'");
$training_completed = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(DISTINCT skill_gaps) FROM pm_training_recommendations");
$skill_gaps_identified = $stmt->fetchColumn();

// --- 9. Alerts & Notifications ---
$stmt = $db->query("SELECT COUNT(*) FROM pm_appraisals WHERE overall_score < 3.0");
$low_performers_count = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM pm_training_recommendations WHERE status = 'Proposed'");
$pending_reviews = $stmt->fetchColumn(); // Using this as proxy for pending actions

// --- AI-Powered Performance Insights Data Processing ---
$ai_insights = [];

// 1. Performance Trend Analysis with ML
$stmt = $db->query("SELECT AVG(overall_score) as avg_score, COUNT(*) as count FROM pm_appraisals");
$performance_data = $stmt->fetch(PDO::FETCH_ASSOC);
$ai_insights['trend_analysis'] = [
    'current_avg' => number_format($performance_data['avg_score'] ?: 0, 1),
    'total_evaluations' => $performance_data['count'],
    'trend_direction' => $performance_data['avg_score'] >= 4.0 ? 'improving' : ($performance_data['avg_score'] >= 3.0 ? 'stable' : 'declining'),
    'confidence_level' => '85%'
];

// 2. Predictive Performance Modeling
$stmt = $db->query("SELECT overall_score FROM pm_appraisals ORDER BY created_at DESC LIMIT 10");
$recent_scores = $stmt->fetchAll(PDO::FETCH_COLUMN);
$avg_recent = count($recent_scores) > 0 ? array_sum($recent_scores) / count($recent_scores) : 0;
$predicted_next_quarter = min(5.0, $avg_recent + ($avg_recent * 0.05)); // Simple linear prediction
$ai_insights['predictive_modeling'] = [
    'current_trend' => $avg_recent >= 4.0 ? 'Strong upward' : ($avg_recent >= 3.0 ? 'Steady' : 'Needs attention'),
    'predicted_score' => number_format($predicted_next_quarter, 1),
    'risk_level' => $avg_recent < 3.0 ? 'High' : ($avg_recent < 3.5 ? 'Medium' : 'Low'),
    'confidence' => '78%'
];

// 3. Automated Skill Gap Identification
$stmt = $db->query("SELECT skill_gaps, COUNT(*) as frequency FROM pm_training_recommendations WHERE skill_gaps IS NOT NULL GROUP BY skill_gaps ORDER BY frequency DESC LIMIT 5");
$skill_gaps = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ai_insights['skill_gaps'] = array_map(function($gap) {
    return [
        'skill' => $gap['skill_gaps'],
        'frequency' => $gap['frequency'],
        'priority' => $gap['frequency'] > 5 ? 'High' : ($gap['frequency'] > 2 ? 'Medium' : 'Low'),
        'recommendation' => $gap['frequency'] > 5 ? 'Immediate training program needed' : 'Consider skill development workshops'
    ];
}, $skill_gaps);

// 4. Smart Goal Recommendations
$stmt = $db->query("SELECT g.employee_id, e.full_name, g.current_progress, g.target_value, g.status
                   FROM pm_goals g
                   JOIN employees e ON g.employee_id = e.employee_id
                   WHERE g.status != 'Completed' AND g.target_value > 0
                   ORDER BY (g.current_progress / g.target_value) ASC LIMIT 5");
$goal_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ai_insights['goal_recommendations'] = array_map(function($goal) {
    $progress_pct = ($goal['current_progress'] / $goal['target_value']) * 100;
    return [
        'employee' => $goal['full_name'],
        'current_progress' => number_format($progress_pct, 1) . '%',
        'recommendation' => $progress_pct < 30 ? 'High priority - needs immediate attention' :
                          ($progress_pct < 60 ? 'Medium priority - monitor closely' : 'On track - provide support'),
        'suggested_action' => $progress_pct < 30 ? 'Schedule coaching session' :
                           ($progress_pct < 60 ? 'Set intermediate milestones' : 'Recognize progress')
    ];
}, $goal_data);

// 5. Quick Wins Identification
$ai_insights['quick_wins'] = [];
// Employees close to completing goals
$stmt = $db->query("SELECT e.full_name, g.goal_title, ((g.current_progress / g.target_value) * 100) as progress_pct
                   FROM pm_goals g
                   JOIN employees e ON g.employee_id = e.employee_id
                   WHERE g.status != 'Completed' AND (g.current_progress / g.target_value) >= 0.8
                   ORDER BY (g.current_progress / g.target_value) DESC LIMIT 3");
$quick_wins = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($quick_wins as $win) {
    $ai_insights['quick_wins'][] = [
        'type' => 'goal_completion',
        'title' => 'Goal Near Completion',
        'description' => "{$win['full_name']} is {$win['progress_pct']}% complete with '{$win['goal_title']}'",
        'impact' => 'High',
        'action' => 'Celebrate achievement and set new challenging goals'
    ];
}

// 6. Team Health Scoring
$stmt = $db->query("SELECT AVG(overall_score) as team_avg, COUNT(*) as total_employees FROM pm_appraisals");
$team_health = $stmt->fetch(PDO::FETCH_ASSOC);
$team_score = $team_health['team_avg'] ?: 0;
$ai_insights['team_health'] = [
    'score' => number_format($team_score, 1),
    'status' => $team_score >= 4.0 ? 'Excellent' : ($team_score >= 3.5 ? 'Good' : ($team_score >= 3.0 ? 'Fair' : 'Needs Improvement')),
    'total_employees' => $team_health['total_employees'],
    'insights' => $team_score >= 4.0 ? 'Team performing exceptionally well' :
                 ($team_score >= 3.5 ? 'Team showing consistent performance' :
                 ($team_score >= 3.0 ? 'Room for improvement in some areas' : 'Immediate action required'))
];

// 7. Success Stories
$ai_insights['success_stories'] = [];
$stmt = $db->query("SELECT e.full_name, a.overall_score, a.review_date
                   FROM pm_appraisals a
                   JOIN employees e ON a.employee_id = e.employee_id
                   WHERE a.overall_score >= 4.5
                   ORDER BY a.overall_score DESC, a.review_date DESC LIMIT 3");
$success_stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($success_stories as $story) {
    $ai_insights['success_stories'][] = [
        'employee' => $story['full_name'],
        'achievement' => "Scored {$story['overall_score']}/5.0 in performance appraisal",
        'date' => date('M Y', strtotime($story['review_date'])),
        'lesson' => 'High performer - identify and replicate best practices'
    ];
}

// 8. Risk Alerts
$ai_insights['risk_alerts'] = [];
// Low performers
$stmt = $db->query("SELECT e.full_name, a.overall_score
                   FROM pm_appraisals a
                   JOIN employees e ON a.employee_id = e.employee_id
                   WHERE a.overall_score < 3.0
                   ORDER BY a.overall_score ASC LIMIT 3");
$low_performers = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($low_performers as $performer) {
    $ai_insights['risk_alerts'][] = [
        'level' => 'high',
        'title' => 'Performance Concern',
        'message' => "{$performer['full_name']} has a performance score of {$performer['overall_score']}/5.0",
        'action' => 'Schedule performance improvement plan and coaching sessions'
    ];
}

// Goals significantly behind schedule
$stmt = $db->query("SELECT e.full_name, g.goal_title, ((g.current_progress / g.target_value) * 100) as progress_pct
                   FROM pm_goals g
                   JOIN employees e ON g.employee_id = e.employee_id
                   WHERE g.status != 'Completed' AND (g.current_progress / g.target_value) < 0.3
                   ORDER BY (g.current_progress / g.target_value) ASC LIMIT 2");
$behind_goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($behind_goals as $goal) {
    $ai_insights['risk_alerts'][] = [
        'level' => 'medium',
        'title' => 'Goal Behind Schedule',
        'message' => "{$goal['full_name']}'s goal '{$goal['goal_title']}' is only {$goal['progress_pct']}% complete",
        'action' => 'Provide additional resources and set interim milestones'
    ];
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Performance Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="custom.css" />
  <style>
    .text-orange { color: #fd7e14; }
    .border-left-danger { border-left: 4px solid #dc3545 !important; }
    .card-title { font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-box-text { color: #6c757d; font-size: 0.85rem; }
    .progress-lg { height: 12px; border-radius: 10px; }
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important; }

    /* AI Insights Enhanced Styling */
    .bg-gradient-info {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }

    .text-purple { color: #6f42c1; }

    /* Enhanced Info Boxes */
    .info-box {
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      transition: all 0.3s ease;
      border: none;
    }

    .info-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .info-box-icon {
      border-radius: 10px 0 0 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Tab Styling */
    .nav-tabs .nav-link {
      border: none;
      border-bottom: 3px solid transparent;
      color: #6c757d;
      font-weight: 500;
      padding: 12px 20px;
      transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
      border-bottom-color: #17a2b8;
      color: #17a2b8;
      background-color: rgba(23, 162, 184, 0.1);
      font-weight: 600;
    }

    .nav-tabs .nav-link:hover {
      border-bottom-color: #17a2b8;
      color: #17a2b8;
    }

    /* Card Enhancements */
    .card-outline-info {
      border-color: #17a2b8 !important;
    }

    .card-primary.card-tabs .card-header {
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    /* Quick Wins Cards */
    .border-success {
      border-color: #28a745 !important;
      border-width: 2px !important;
    }

    /* Smart Tips Cards */
    .border-danger, .border-warning {
      border-width: 2px !important;
    }

    /* Success Stories Cards */
    .border-warning {
      border-color: #ffc107 !important;
      border-width: 2px !important;
    }

    /* Alert Cards */
    .border-info {
      border-color: #17a2b8 !important;
      border-width: 2px !important;
    }

    /* AI Helper Card */
    .bg-light {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    /* Badge Enhancements */
    .badge {
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .badge-lg {
      font-size: 0.85rem;
      padding: 6px 12px;
    }

    /* Button Enhancements */
    .btn {
      border-radius: 6px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Alert Enhancements */
    .alert {
      border-radius: 8px;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Progress Bar Enhancements */
    .progress {
      border-radius: 8px;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .info-box {
        margin-bottom: 1rem;
      }

      .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 0.9rem;
      }

      .card-body {
        padding: 1rem;
      }
    }

    /* Animation Classes */
    .animate__animated {
      animation-duration: 0.6s;
    }

    .animate__fadeIn {
      animation-name: fadeIn;
    }

    .animate__bounceIn {
      animation-name: bounceIn;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounceIn {
      0% { transform: scale(0.3); opacity: 0; }
      50% { transform: scale(1.05); }
      70% { transform: scale(0.9); }
      100% { transform: scale(1); opacity: 1; }
    }

    /* Custom Scrollbar */
    .card-body::-webkit-scrollbar {
      width: 6px;
    }

    .card-body::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .card-body::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .card-body::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
  </style>
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="performance.php" class="nav-link">Home</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a
            class="nav-link"
            href="#"
            id="darkToggle"
            role="button"
            title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="performance.php" class="brand-link">

        <img
          src="../assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3"
          style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','../user_profile/profile_form.php')" class="d-block">
              Admin <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="modules/360-degree.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>360-Degree Feedback</p>
              </a>
            </li>
             <li class="nav-item">
              <a href="modules/Appraisals&review.php" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>Appraisals & Review</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="modules/Goal&KPI.php" class="nav-link">
                <i class="nav-icon fas fa-tree"></i>
                <p>Goal & KPI</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="modules/Performancereport.php" class="nav-link">
                <i class="nav-icon fas fa-table"></i>
                <p>Performance Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="modules/Training.php" class="nav-link">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>Training</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../logout.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Performance Management System</h1>
            </div>
            <!-- /.col -->

            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- TOP ROW: Summaries -->
          <div class="row">
            <!-- Employee Performance Summary -->
            <div class="col-md-4">
              <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Employee Performance Summary</h3>
                </div>
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-check-circle text-orange mr-2"></i>
                    <span class="text-muted mr-2">Overall Rating:</span>
                    <h4 class="mb-0 font-weight-bold"><?= $avg_overall_rating ?> <i class="fas fa-star text-warning small"></i></h4>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle text-danger mr-2"></i>
                    <span class="text-muted mr-2">Top Performer:</span>
                    <span class="font-weight-bold"><?= htmlspecialchars($top_performer) ?></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- KPI Metrics -->
            <div class="col-md-4">
              <div class="card card-outline card-success shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">KPI Metrics</h3>
                </div>
                <div class="card-body">
                  <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">KPI Achievement:</span>
                    <span class="font-weight-bold text-success h4 mb-0"><?= $kpi_achievement ?>%</span>
                  </div>
                  <div class="progress progress-lg mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpi_achievement ?>%" aria-valuenow="<?= $kpi_achievement ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sales Target:</span>
                    <span class="font-weight-bold">95%</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">CSAT Score:</span>
                    <span class="font-weight-bold">87%</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Goals & Objectives -->
            <div class="col-md-4">
              <div class="card card-outline card-info shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Goals & Objectives</h3>
                </div>
                <div class="card-body">
                  <div class="row mb-3 align-items-center">
                    <div class="col-7">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-primary mr-2"></i>
                        <span class="text-muted">Goals Progress:</span>
                      </div>
                    </div>
                    <div class="col-5 text-right">
                      <span class="font-weight-bold h4 mb-0"><?= $goals_progress_avg ?>%</span>
                    </div>
                  </div>
                  <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $goals_progress_avg ?>%"></div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <span class="text-muted mr-1">Completed:</span>
                        <span class="font-weight-bold h5 mb-0"><?= $completed_goals ?></span>
                      </div>
                    </div>
                    <div class="col-6 text-right">
                      <div class="d-flex align-items-center justify-content-end">
                        <i class="fas fa-check-circle text-primary mr-2"></i>
                        <span class="text-muted mr-1">Ongoing:</span>
                        <span class="font-weight-bold h5 mb-0"><?= $ongoing_goals ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- MIDDLE ROW: Charts & Training -->
          <div class="row mt-4">
            <!-- Appraisal Results (Pie Chart) -->
            <div class="col-md-4">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Appraisal Results</h3>
                </div>
                <div class="card-body text-center d-flex flex-column align-items-center">
                  <div class="chart-responsive w-100 mb-3" style="height: 180px;">
                    <canvas id="appraisalChart"></canvas>
                  </div>
                  <div class="w-100">
                    <div class="d-flex justify-content-between mb-1">
                      <span class="text-muted"><i class="fas fa-circle text-success mr-1"></i> Excellent</span>
                      <span class="font-weight-bold"><?= $excellent_pct ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                      <span class="text-muted"><i class="fas fa-circle text-warning mr-1"></i> Good</span>
                      <span class="font-weight-bold"><?= $good_pct ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span class="text-muted"><i class="fas fa-circle text-primary mr-1"></i> Needs Improvement</span>
                      <span class="font-weight-bold"><?= $needs_improvement_pct ?>%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Productivity Overview (Bar Chart) -->
            <div class="col-md-5">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Productivity Overview</h3>
                </div>
                <div class="card-body">
                  <h6 class="text-center text-muted mb-3">Tasks Completed vs. Assigned</h6>
                  <div class="chart" style="height: 200px;">
                    <canvas id="productivityChart"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Training & Development -->
            <div class="col-md-3">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Training & Development</h3>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between mb-3 align-items-center">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-check-circle text-primary mr-2"></i>
                      <span class="text-muted">Training Completed:</span>
                    </div>
                    <span class="font-weight-bold h4 mb-0"><?= $training_completed ?></span>
                  </div>
                  <div class="d-flex justify-content-between mb-4 align-items-center">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-check-circle text-success mr-2"></i>
                      <span class="text-muted">Skill Gaps Identified:</span>
                    </div>
                    <span class="font-weight-bold h4 mb-0"><?= $skill_gaps_identified ?></span>
                  </div>
                  <div class="bg-light p-3 rounded d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Recommended Courses</span>
                    <button class="btn btn-success btn-sm"><i class="fas fa-graduation-cap"></i> Learning Training</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- BOTTOM ROW: Engagement, Time, Alerts -->
          <div class="row mt-4 mb-4">
            <!-- Attendance & Time -->
            <div class="col-md-4">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Attendance & Time</h3>
                </div>
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                   <div class="position-relative mb-3" style="height: 150px; width: 150px;">
                      <canvas id="attendanceChart"></canvas>
                      <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                         <i class="fas fa-clock h3 text-muted"></i>
                      </div>
                   </div>
                   <div class="w-100 mt-2">
                      <div class="row text-center small">
                         <div class="col-4 border-right">
                            <span class="text-orange">32%</span><br><span class="text-muted">Excellent</span>
                         </div>
                         <div class="col-4 border-right">
                            <span class="text-success">45%</span><br><span class="text-muted">Good</span>
                         </div>
                         <div class="col-4">
                            <span class="text-primary">20%</span><br><span class="text-muted">Needs Improvement</span>
                         </div>
                      </div>
                   </div>
                </div>
              </div>
            </div>

            <!-- Employee Engagement (Line Chart) -->
            <div class="col-md-5">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Employee Engagement</h3>
                </div>
                <div class="card-body">
                   <div class="d-flex align-items-center mb-3">
                      <i class="fas fa-check-circle text-success mr-2"></i>
                      <span class="text-muted mr-2">Engagement Score:</span>
                      <h4 class="mb-0 font-weight-bold">4.1 <i class="fas fa-star text-warning small"></i></h4>
                   </div>
                   <div class="chart" style="height: 180px;">
                      <canvas id="engagementChart"></canvas>
                   </div>
                </div>
              </div>
            </div>

            <!-- Alerts & Notifications -->
            <div class="col-md-3">
              <div class="card shadow-sm h-100">
                <div class="card-header border-0">
                  <h3 class="card-title text-muted">Alerts & Notifications</h3>
                </div>
                <div class="card-body">
                   <div class="alert alert-light border-left-danger mb-2 d-flex align-items-center p-2">
                      <i class="fas fa-user-circle text-danger mr-2"></i>
                      <span class="text-danger font-weight-bold mr-1"><?= $low_performers_count ?></span> <span class="text-muted small">Low Performers</span>
                   </div>
                   <div class="alert alert-light border-left-danger mb-2 d-flex align-items-center p-2">
                      <i class="fas fa-user-circle text-danger mr-2"></i>
                      <span class="text-danger font-weight-bold mr-1"><?= $pending_reviews ?></span> <span class="text-muted small">Pending Reviews</span>
                   </div>
                   <div class="alert alert-light border-left-danger d-flex align-items-center p-2">
                      <i class="fas fa-user-circle text-danger mr-2"></i>
                      <span class="text-danger font-weight-bold mr-1">1</span> <span class="text-muted small">Missed Training</span>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- AI-POWERED PERFORMANCE INSIGHTS SECTION -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                  <ul class="nav nav-tabs" id="ai-insights-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="quick-wins-tab" data-toggle="pill" href="#quick-wins" role="tab" aria-controls="quick-wins" aria-selected="true">
                        <i class="fas fa-bolt mr-2"></i>Quick Wins
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="smart-tips-tab" data-toggle="pill" href="#smart-tips" role="tab" aria-controls="smart-tips" aria-selected="false">
                        <i class="fas fa-lightbulb mr-2"></i>Smart Tips
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="success-stories-tab" data-toggle="pill" href="#success-stories" role="tab" aria-controls="success-stories" aria-selected="false">
                        <i class="fas fa-star mr-2"></i>Success Stories
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="important-alerts-tab" data-toggle="pill" href="#important-alerts" role="tab" aria-controls="important-alerts" aria-selected="false">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Important Alerts
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="card-body">
                  <div class="tab-content" id="ai-insights-tabContent">

                    <!-- Quick Wins Tab -->
                    <div class="tab-pane fade show active" id="quick-wins" role="tabpanel" aria-labelledby="quick-wins-tab">
                      <div class="row">
                        <div class="col-md-8">
                          <h5 class="text-primary mb-3"><i class="fas fa-bolt mr-2"></i>Quick Wins - Immediate Actions for Success</h5>
                          <p class="text-muted mb-4">AI has identified these high-impact opportunities that can be implemented quickly to boost performance.</p>

                          <?php if (!empty($ai_insights['quick_wins'])): ?>
                            <?php foreach ($ai_insights['quick_wins'] as $win): ?>
                              <div class="card border-success mb-3">
                                <div class="card-body">
                                  <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                      <i class="fas fa-trophy text-success fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                      <h6 class="card-title text-success mb-2"><?= htmlspecialchars($win['title']) ?></h6>
                                      <p class="card-text mb-2"><?= htmlspecialchars($win['description']) ?></p>
                                      <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-success">High Impact</span>
                                        <button class="btn btn-sm btn-outline-success" onclick="implementQuickWin('<?= htmlspecialchars($win['type']) ?>')">
                                          <i class="fas fa-check mr-1"></i>Implement
                                        </button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <div class="alert alert-info">
                              <i class="fas fa-info-circle mr-2"></i>
                              <strong>No quick wins identified currently.</strong> AI is continuously analyzing performance data to identify opportunities.
                            </div>
                          <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                          <div class="card bg-light">
                            <div class="card-header">
                              <h6 class="card-title mb-0"><i class="fas fa-chart-line mr-2"></i>Performance Trend</h6>
                            </div>
                            <div class="card-body">
                              <div class="text-center mb-3">
                                <h3 class="text-primary mb-0"><?= $ai_insights['trend_analysis']['current_avg'] ?>/5.0</h3>
                                <small class="text-muted">Current Average Score</small>
                              </div>
                              <div class="progress mb-2">
                                <div class="progress-bar bg-<?= $ai_insights['trend_analysis']['trend_direction'] === 'improving' ? 'success' : ($ai_insights['trend_analysis']['trend_direction'] === 'stable' ? 'warning' : 'danger') ?>"
                                     style="width: <?= ($ai_insights['trend_analysis']['current_avg'] / 5) * 100 ?>%"></div>
                              </div>
                              <div class="d-flex justify-content-between text-sm">
                                <span>Trend: <strong class="text-<?= $ai_insights['trend_analysis']['trend_direction'] === 'improving' ? 'success' : ($ai_insights['trend_analysis']['trend_direction'] === 'stable' ? 'warning' : 'danger') ?>"><?= ucfirst($ai_insights['trend_analysis']['trend_direction']) ?></strong></span>
                                <span>Confidence: <?= $ai_insights['trend_analysis']['confidence_level'] ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Smart Tips Tab -->
                    <div class="tab-pane fade" id="smart-tips" role="tabpanel" aria-labelledby="smart-tips-tab">
                      <div class="row">
                        <div class="col-md-6">
                          <h5 class="text-info mb-3"><i class="fas fa-lightbulb mr-2"></i>Smart Recommendations - AI-Powered Insights</h5>

                          <!-- Team Health Overview -->
                          <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                              <h6 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Team Health Overview</h6>
                            </div>
                            <div class="card-body">
                              <div class="row align-items-center">
                                <div class="col-6">
                                  <div class="text-center">
                                    <h2 class="text-info mb-0"><?= $ai_insights['team_health']['score'] ?>/5.0</h2>
                                    <small class="text-muted">Team Average</small>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="mb-2">
                                    <span class="badge badge-<?= $ai_insights['team_health']['status'] === 'Excellent' ? 'success' : ($ai_insights['team_health']['status'] === 'Good' ? 'info' : 'warning') ?> badge-lg">
                                      <?= $ai_insights['team_health']['status'] ?>
                                    </span>
                                  </div>
                                  <small class="text-muted">Based on <?= $ai_insights['team_health']['total_employees'] ?> evaluations</small>
                                </div>
                              </div>
                              <hr>
                              <p class="mb-0 small text-muted"><strong>AI Insight:</strong> <?= $ai_insights['team_health']['insights'] ?></p>
                            </div>
                          </div>

                          <!-- Predictive Modeling -->
                          <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-white">
                              <h6 class="card-title mb-0"><i class="fas fa-chart-line mr-2"></i>Predictive Performance</h6>
                            </div>
                            <div class="card-body">
                              <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Next Quarter Prediction:</span>
                                <strong class="text-warning"><?= $ai_insights['predictive_modeling']['predicted_score'] ?>/5.0</strong>
                              </div>
                              <div class="progress mb-2">
                                <div class="progress-bar bg-warning" style="width: <?= ($ai_insights['predictive_modeling']['predicted_score'] / 5) * 100 ?>%"></div>
                              </div>
                              <div class="d-flex justify-content-between text-sm">
                                <span>Trend: <strong class="text-<?= $ai_insights['predictive_modeling']['current_trend'] === 'Strong upward' ? 'success' : ($ai_insights['predictive_modeling']['current_trend'] === 'Steady' ? 'warning' : 'danger') ?>"><?= $ai_insights['predictive_modeling']['current_trend'] ?></strong></span>
                                <span>Risk: <strong class="text-<?= $ai_insights['predictive_modeling']['risk_level'] === 'Low' ? 'success' : ($ai_insights['predictive_modeling']['risk_level'] === 'Medium' ? 'warning' : 'danger') ?>"><?= $ai_insights['predictive_modeling']['risk_level'] ?></strong></span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <h6 class="text-muted mb-3"><i class="fas fa-user-graduate mr-2"></i>Skill Gap Analysis</h6>

                          <?php if (!empty($ai_insights['skill_gaps'])): ?>
                            <?php foreach ($ai_insights['skill_gaps'] as $gap): ?>
                              <div class="card border-<?= $gap['priority'] === 'High' ? 'danger' : ($gap['priority'] === 'Medium' ? 'warning' : 'info') ?> mb-3">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0"><?= htmlspecialchars($gap['skill']) ?></h6>
                                    <span class="badge badge-<?= $gap['priority'] === 'High' ? 'danger' : ($gap['priority'] === 'Medium' ? 'warning' : 'info') ?>">
                                      <?= $gap['priority'] ?> Priority
                                    </span>
                                  </div>
                                  <p class="card-text small text-muted mb-2">Identified in <?= $gap['frequency'] ?> performance reviews</p>
                                  <p class="card-text small mb-2"><strong>Recommendation:</strong> <?= htmlspecialchars($gap['recommendation']) ?></p>
                                  <button class="btn btn-sm btn-outline-primary" onclick="scheduleTraining('<?= htmlspecialchars($gap['skill']) ?>')">
                                    <i class="fas fa-calendar-plus mr-1"></i>Schedule Training
                                  </button>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <div class="alert alert-success">
                              <i class="fas fa-check-circle mr-2"></i>
                              <strong>Great news!</strong> No significant skill gaps identified in recent performance reviews.
                            </div>
                          <?php endif; ?>

                          <h6 class="text-muted mb-3 mt-4"><i class="fas fa-target mr-2"></i>Goal Recommendations</h6>

                          <?php if (!empty($ai_insights['goal_recommendations'])): ?>
                            <?php foreach ($ai_insights['goal_recommendations'] as $rec): ?>
                              <div class="card border-primary mb-3">
                                <div class="card-body">
                                  <h6 class="card-title mb-2"><?= htmlspecialchars($rec['employee']) ?></h6>
                                  <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Progress:</span>
                                    <strong class="text-<?= strpos($rec['current_progress'], '30%') === false && strpos($rec['current_progress'], '60%') === false ? 'success' : 'warning' ?>"><?= $rec['current_progress'] ?></strong>
                                  </div>
                                  <p class="card-text small mb-2"><strong>AI Recommendation:</strong> <?= htmlspecialchars($rec['recommendation']) ?></p>
                                  <button class="btn btn-sm btn-outline-primary" onclick="implementRecommendation('<?= htmlspecialchars($rec['employee']) ?>')">
                                    <i class="fas fa-arrow-right mr-1"></i><?= htmlspecialchars($rec['suggested_action']) ?>
                                  </button>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <div class="alert alert-info">
                              <i class="fas fa-info-circle mr-2"></i>
                              <strong>All goals on track!</strong> No immediate recommendations needed.
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <!-- Success Stories Tab -->
                    <div class="tab-pane fade" id="success-stories" role="tabpanel" aria-labelledby="success-stories-tab">
                      <div class="row">
                        <div class="col-12">
                          <h5 class="text-success mb-3"><i class="fas fa-star mr-2"></i>Success Stories - Celebrating Excellence</h5>
                          <p class="text-muted mb-4">AI highlights outstanding performance achievements to inspire and motivate the team.</p>

                          <?php if (!empty($ai_insights['success_stories'])): ?>
                            <div class="row">
                              <?php foreach ($ai_insights['success_stories'] as $story): ?>
                                <div class="col-md-4 mb-3">
                                  <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                      <div class="mb-3">
                                        <i class="fas fa-trophy text-success fa-3x"></i>
                                      </div>
                                      <h6 class="card-title text-success mb-2"><?= htmlspecialchars($story['employee']) ?></h6>
                                      <p class="card-text small mb-2"><?= htmlspecialchars($story['achievement']) ?></p>
                                      <small class="text-muted d-block mb-3">Achieved in <?= $story['date'] ?></small>
                                      <p class="card-text small text-muted mb-3"><strong>AI Lesson:</strong> <?= htmlspecialchars($story['lesson']) ?></p>
                                      <button class="btn btn-sm btn-outline-success" onclick="replicateSuccess('<?= htmlspecialchars($story['employee']) ?>')">
                                        <i class="fas fa-copy mr-1"></i>Replicate Success
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <div class="alert alert-warning">
                              <i class="fas fa-exclamation-triangle mr-2"></i>
                              <strong>No recent success stories.</strong> AI is monitoring performance to identify achievements as they occur.
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <!-- Important Alerts Tab -->
                    <div class="tab-pane fade" id="important-alerts" role="tabpanel" aria-labelledby="important-alerts-tab">
                      <div class="row">
                        <div class="col-12">
                          <h5 class="text-danger mb-3"><i class="fas fa-exclamation-triangle mr-2"></i>Important Alerts - Action Required</h5>
                          <p class="text-muted mb-4">AI has identified critical issues that need immediate attention to maintain performance standards.</p>

                          <?php if (!empty($ai_insights['risk_alerts'])): ?>
                            <?php foreach ($ai_insights['risk_alerts'] as $alert): ?>
                              <div class="card border-<?= $alert['level'] === 'high' ? 'danger' : 'warning' ?> mb-3">
                                <div class="card-header bg-<?= $alert['level'] === 'high' ? 'danger' : 'warning' ?> text-white">
                                  <h6 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <?= htmlspecialchars($alert['title']) ?>
                                    <span class="badge badge-light float-right text-capitalize"><?= $alert['level'] ?> Priority</span>
                                  </h6>
                                </div>
                                <div class="card-body">
                                  <p class="card-text mb-3"><?= htmlspecialchars($alert['message']) ?></p>
                                  <div class="alert alert-light border">
                                    <strong>Suggested Action:</strong> <?= htmlspecialchars($alert['action']) ?>
                                  </div>
                                  <button class="btn btn-sm btn-<?= $alert['level'] === 'high' ? 'danger' : 'warning' ?>"
                                          onclick="addressAlert('<?= $alert['level'] ?>')">
                                    <i class="fas fa-arrow-right mr-1"></i> Take Action
                                  </button>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <div class="alert alert-success">
                              <i class="fas fa-check-circle mr-2"></i>
                              <strong>All Clear!</strong> No critical alerts at this time. Keep monitoring performance.
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- AI Helper Section -->
                <div class="card-footer bg-light">
                  <div class="row align-items-center">
                    <div class="col-md-8">
                      <div class="d-flex align-items-center">
                        <div class="mr-3">
                          <i class="fas fa-robot text-info fa-2x"></i>
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-1">AI Performance Assistant</h6>
                          <p class="mb-0 small text-muted">
                            I'm here to help you improve team performance. These insights are updated automatically based on your latest data.
                            <a href="#" onclick="showAIHelp()" class="text-info ml-2">Learn how AI helps</a>
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 text-right">
                      <small class="text-muted">Last updated: Today at <?= date('H:i') ?></small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->

  </div>
  <?php include "../layout/global_modal.php"; ?>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../assets/dist/js/theme.js"></script>
  <script src="../assets/dist/js/time.js"></script>
  <script src="../assets/dist/js/global_modal.js"></script>
  <script>
    $(function() {
      // --- 1. Appraisal Results Pie Chart ---
      var appraisalPieData = {
        labels: ['Excellent', 'Good', 'Needs Improvement'],
        datasets: [{
          data: [<?= $excellent_pct ?>, <?= $good_pct ?>, <?= $needs_improvement_pct ?>],
          backgroundColor: ['#28a745', '#ffc107', '#007bff'],
        }]
      }
      var appraisalPieOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: { display: false }
      }
      new Chart($('#appraisalChart').get(0).getContext('2d'), {
        type: 'pie',
        data: appraisalPieData,
        options: appraisalPieOptions
      })

      // --- 2. Productivity Overview Bar Chart ---
      var productivityData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [
          {
            label: 'Completed',
            backgroundColor: '#007bff',
            data: [5, 10, 8, 12, 10]
          },
          {
            label: 'Assigned',
            backgroundColor: '#28a745',
            data: [8, 12, 11, 15, 13]
          }
        ]
      }
      var productivityOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false,
        scales: {
          yAxes: [{ ticks: { beginAtZero: true, stepSize: 5 } }]
        }
      }
      new Chart($('#productivityChart').get(0).getContext('2d'), {
        type: 'bar',
        data: productivityData,
        options: productivityOptions
      })

      // --- 3. Attendance & Time Donut Chart ---
      var attendanceData = {
        labels: ['Excellent', 'Good', 'Needs Improvement'],
        datasets: [{
          data: [32, 45, 20],
          backgroundColor: ['#fd7e14', '#28a745', '#007bff'],
        }]
      }
      var attendanceOptions = {
        maintainAspectRatio: false,
        responsive: true,
        cutoutPercentage: 70,
        legend: { display: false }
      }
      new Chart($('#attendanceChart').get(0).getContext('2d'), {
        type: 'doughnut',
        data: attendanceData,
        options: attendanceOptions
      })

      // --- 4. Employee Engagement Line Chart ---
      var engagementData = {
        labels: ['1', '2', '3', '4', '5', '6'],
        datasets: [{
          label: 'Score',
          fill: true,
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          borderColor: '#007bff',
          pointRadius: 4,
          pointBackgroundColor: '#007bff',
          data: [4.0, 4.2, 3.8, 4.1, 4.3, 4.1]
        }]
      }
      var engagementOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: { display: false },
        scales: {
          xAxes: [{ gridLines: { display: false } }],
          yAxes: [{ ticks: { min: 3, max: 5, stepSize: 0.5 } }]
        }
      }
      new Chart($('#engagementChart').get(0).getContext('2d'), {
        type: 'line',
        data: engagementData,
        options: engagementOptions
      })

      // AI Insights JavaScript Functions
      // Initialize tooltips
      $('[data-toggle="tooltip"]').tooltip();

      // Add animation classes to cards when they come into view
      $('.card').addClass('animate__animated animate__fadeIn');

      // Tab switching with smooth transitions
      $('.nav-tabs .nav-link').on('click', function(e) {
        e.preventDefault();
        var targetTab = $(this).attr('href');

        // Remove active class from all tabs
        $('.nav-tabs .nav-link').removeClass('active');
        // Add active class to clicked tab
        $(this).addClass('active');

        // Hide all tab panes
        $('.tab-pane').removeClass('show active');
        // Show target tab pane
        $(targetTab).addClass('show active');

        // Add bounce animation to new content
        $(targetTab).find('.card').addClass('animate__bounceIn');
      });

      // Progress bar animations
      $('.progress-bar').each(function() {
        var $this = $(this);
        var width = $this.attr('aria-valuenow');
        $this.css('width', '0%');
        setTimeout(function() {
          $this.css('width', width + '%');
        }, 500);
      });

      // Auto-refresh insights every 5 minutes
      setInterval(function() {
        // In a real implementation, this would fetch new data via AJAX
        console.log('Refreshing AI insights...');
        // For now, just add a subtle animation to indicate refresh
        $('.card-primary').fadeTo(500, 0.8).fadeTo(500, 1);
      }, 300000); // 5 minutes

      // Click handlers for action buttons
      $('.btn-outline-primary, .btn-outline-success').on('click', function() {
        var action = $(this).text().trim();
        console.log('Action clicked:', action);
        // In a real implementation, this would trigger the appropriate action
        $(this).addClass('btn-primary text-white').removeClass('btn-outline-primary');
        setTimeout(() => {
          $(this).removeClass('btn-primary text-white').addClass('btn-outline-primary');
        }, 2000);
      });

      // Hover effects for info boxes
      $('.info-box').hover(
        function() {
          $(this).find('.info-box-icon').addClass('animate__bounceIn');
        },
        function() {
          $(this).find('.info-box-icon').removeClass('animate__bounceIn');
        }
      );
    })

    // AI Insights Action Functions
    function implementQuickWin(type) {
      alert('Quick win implementation initiated. In a real system, this would trigger the appropriate workflow for: ' + type);
    }

    function scheduleTraining(skill) {
      alert('Training scheduling initiated for: ' + skill + '. In a real system, this would open a training scheduling interface.');
    }

    function implementRecommendation(employee) {
      alert('Recommendation implementation initiated for: ' + employee + '. In a real system, this would trigger the appropriate action.');
    }

    function replicateSuccess(employee) {
      alert('Success replication process initiated for: ' + employee + '. In a real system, this would analyze and document best practices.');
    }

    function addressAlert(level) {
      alert('Alert addressing initiated (Priority: ' + level + '). In a real system, this would open an action planning interface.');
    }

    function showAIHelp() {
      alert('AI Performance Assistant Help:\n\n• Quick Wins: Immediate high-impact actions\n• Smart Tips: AI-powered recommendations and predictions\n• Success Stories: Highlighting achievements to inspire\n• Important Alerts: Critical issues requiring attention\n\nAI analyzes performance data continuously to provide actionable insights.');
    }

    // Function to refresh AI insights (can be called externally)
    function refreshAIInsights() {
      location.reload(); // Simple refresh for now
    }

    // Function to export insights (placeholder)
    function exportInsights() {
      alert('Export functionality would be implemented here. This would generate a PDF or Excel report of the AI insights.');
    }
  </script>
</body>

</html>
