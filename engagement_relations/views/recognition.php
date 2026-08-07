<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

require_once __DIR__ . '/../autoload.php';


use App\Controllers\RecognitionController;
use App\Controllers\GrievanceController;
use App\Controllers\SocialController;
use App\Controllers\SurveyController;
use App\Controllers\FeedbackController;
use App\Controllers\CommunicationController;
use App\Controllers\RewardController;
use App\Controllers\RewardRedemptionController;
use App\Controllers\BadgeController;
use App\Controllers\EmployeeBadgeController;
use App\Controllers\AwardHistoryController;
use App\Controllers\EmployeeController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$ctrl = new RecognitionController();
$grievanceCtrl = new GrievanceController();
$socialCtrl = new SocialController();
$surveyCtrl = new SurveyController();
$feedbackCtrl = new FeedbackController();
$communicationCtrl = new CommunicationController();


$payload = $payload ?? [];
$payload['recognitions'] = $ctrl->getRecognitions();
$payload['leaderboard'] = $ctrl->getLeaderboard();

// Only include Employee Recognition & Rewards tables
$rewardCtrl = new RewardController();
$rewardRedemptionCtrl = new RewardRedemptionController();
$badgeCtrl = new BadgeController();
$employeeBadgeCtrl = new EmployeeBadgeController();
$awardHistoryCtrl = new AwardHistoryController();
$employeeCtrl = new EmployeeController();

$payload['rewards'] = $rewardCtrl->index();
$payload['reward_redemptions'] = $rewardRedemptionCtrl->index();
$payload['badges'] = $badgeCtrl->index();
$payload['employee_badges'] = $employeeBadgeCtrl->index();
$payload['award_history'] = $awardHistoryCtrl->index();
$payload['employees'] = $employeeCtrl->index();
$payload['announcements'] = $communicationCtrl->getRecognitionAnnouncements();
$payload['recently_recognized'] = $ctrl->getRecentlyRecognizedEmployees(30);
$payload['comprehensive_leaderboard'] = $ctrl->getComprehensiveLeaderboard(10);
$payload['department_leaderboard'] = $ctrl->getDepartmentLeaderboard(null, 10);
$payload['recognition_recommendations'] = $ctrl->getRecognitionRecommendations(10);
$payload['employees_without_reports'] = $ctrl->getEmployeesWithoutPerformanceReports();
$payload['performance_candidates'] = $rewardCtrl->getPerformanceBasedCandidates();
$payload['top_performers'] = $rewardCtrl->getTopPerformers();
$payload['improvement_candidates'] = $rewardCtrl->getImprovementCandidates();

$currentEmployeeId = $_SESSION['user']['employee_id'] ?? null;
$currentUserId = $_SESSION['user']['id'] ?? null;
if ($currentEmployeeId) {
  $payload['my_points'] = $ctrl->getEmployeeTotalPoints($currentEmployeeId);
  $payload['my_badge_recommendations'] = $ctrl->getBadgeRecommendations($currentEmployeeId);
}

$selectedMonth = (int)($_GET['month'] ?? date('m'));
$selectedYear = (int)($_GET['year'] ?? date('Y'));
$payload['employee_of_month_candidates'] = $ctrl->getEmployeeOfTheMonthCandidates($selectedMonth, $selectedYear, $currentUserId);

// Get nominated employees (for badge assignment filtering)
$nominatedEmployeeIds = [];
foreach ($payload['award_history'] ?? [] as $award) {
  if (strpos($award['award_name'] ?? '', 'Nomination') !== false) {
    if (!in_array($award['employee_id'], $nominatedEmployeeIds)) {
      $nominatedEmployeeIds[] = $award['employee_id'];
    }
  }
}
$payload['nominated_employee_ids'] = $nominatedEmployeeIds;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $currentEmployeeId = $_SESSION['user']['employee_id'] ?? null;
  $currentUserId = $_SESSION['user']['id'] ?? null;
  $senderId = $currentUserId ?? $currentEmployeeId;

  if ($senderId) {
    // Handle recognition submission
    if (!empty($_POST['receiver_id']) && !empty($_POST['message'])) {
      $ctrl->sendRecognition($senderId, $_POST['receiver_id'], $_POST['message'], (int)($_POST['points'] ?? 10));
      $_SESSION['flash_success'] = 'Recognition sent successfully.';
    }

    // Handle badge assignment
    if (!empty($_POST['badge_employee_id']) && !empty($_POST['badge_id'])) {
      $performanceScore = null;
      if (!empty($_POST['badge_employee_id'])) {
        $employeeId = (int)$_POST['badge_employee_id'];
        $performanceScore = $ctrl->getEmployeePerformanceScore($employeeId);
      }
      $ctrl->assignAchievementBadge($_POST['badge_employee_id'], $_POST['badge_id'], $currentUserId, $performanceScore);
      $_SESSION['flash_success'] = 'Badge assigned successfully.';
    }

    // Handle adding new reward
    if (!empty($_POST['action']) && $_POST['action'] === 'add_reward' && !empty($_POST['reward_name']) && !empty($_POST['reward_points'])) {
      try {
        $rewardCtrl->store([
          'name' => $_POST['reward_name'],
          'description' => $_POST['reward_description'] ?? '',
          'points_required' => (int)$_POST['reward_points']
        ]);
        $_SESSION['flash_success'] = 'Reward added successfully!';
      } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Error adding reward: ' . $e->getMessage();
      }
    }

    // Handle recognition announcements
    if (!empty($_POST['form_type']) && $_POST['form_type'] === 'recognition_announcement') {
      $title = trim($_POST['title'] ?? '');
      $content = trim($_POST['content'] ?? '');
      if ($title && $content) {
        $communicationCtrl->postRecognitionAnnouncement($title, $content, $senderId);
        $_SESSION['flash_success'] = 'Recognition announcement posted successfully.';
      } else {
        $_SESSION['flash_error'] = 'Title and content are required for recognition announcements.';
      }
    }

    // Handle Employee of the Month nomination
    if (!empty($_POST['nominate_employee_id']) && !empty($_POST['nomination_reason'])) {
      $employee = $employeeCtrl->find($_POST['nominate_employee_id']);
      if ($employee) {
        $awardHistoryCtrl->store([
          'employee_id' => $_POST['nominate_employee_id'],
          'award_name' => 'Employee of the Month Nomination',
          'reason' => $_POST['nomination_reason'],
          'nominated_by' => $senderId,
          'award_type' => 'employee_of_month',
          'month_year' => date('Y-m'),
          'status' => 'nominated'
        ]);
        $_SESSION['flash_success'] = 'Employee nominated for Employee of the Month.';
      }
    }

        // Handle Employee of the Month vote
        if (!empty($_POST['action']) && $_POST['action'] === 'vote_employee_month' && !empty($_POST['award_history_id'])) {
          $awardHistoryId = (int)$_POST['award_history_id'];

          if (!isset($_SESSION['employee_month_votes'])) {
            $_SESSION['employee_month_votes'] = [];
          }

          if (!in_array($awardHistoryId, $_SESSION['employee_month_votes'])) {
            $_SESSION['employee_month_votes'][] = $awardHistoryId;

            $nomineeEmployeeId = $ctrl->getEmployeeFromAwardHistory($awardHistoryId);

            if ($nomineeEmployeeId) {
              $ctrl->addVotePoints($senderId, $nomineeEmployeeId);
              $awardHistoryCtrl->incrementVoteCount($awardHistoryId);
            }

            $_SESSION['flash_success'] = 'Your vote has been recorded! (+5 points awarded to nominee)';
          } else {
            $_SESSION['flash_error'] = 'You have already voted for this nomination.';
          }
        }
      }

      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit;
    }

    $flashSuccess = $_SESSION['flash_success'] ?? null;
    $flashError = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);

    function getTierLabel($points) {
      if ($points >= 1000) {
        return 'Platinum';
      }
      if ($points >= 750) {
        return 'Gold';
      }
      if ($points >= 500) {
        return 'Silver';
      }
      return 'Bronze';
    }
    ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Employee Engagement and Relations Management</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />

  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="css/recognition.css" />
  <link rel="stylesheet" href="../custom.css" />    
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../../assets/pics/bcpLogo.png"
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
          <a href="../engagement_relations.php" class="nav-link">Home</a>        </li>
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
      <a href="../engagement_relations.php" class="brand-link">

        <img
          src="../../assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3 brand-image-opacity" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','../../user_profile/profile_form.php')" class="d-block">
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
              <a href="dashboard.php" class="nav-link ">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="communication.php" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>Communication</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="survey.php" class="nav-link">
                <i class="nav-icon fas fa-poll"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link active">
                <i class="nav-icon fas fa-award"></i>
                <p>Recognition</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="grievance.php" class="nav-link">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p> Grievances</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="social.php" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p> Social</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../logout.php" class="nav-link">
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


        <!-- MAIN CONTENT -->
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h3><i class="fas fa-trophy mr-2"></i>Employee Recognition & Rewards</h3>
              <p class="text-muted">Motivate employees through recognition, achievements, and rewards 🏆</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <div class="recognition-area">
        <div class="row"> 
          <div class="col-12">
            <?php if ($flashSuccess): ?>
              <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <?= htmlspecialchars($flashSuccess) ?>
              </div>
           <?php endif; ?>
           <?php if ($flashError): ?>
             <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                 <?= htmlspecialchars($flashError) ?>
               </div>
            <?php endif; ?>
          </div>
        </div>
      <!-- Main content -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0 recognition-card">
            <div class="card-header p-0 border-0">
              <ul class="nav nav-tabs recognition-nav-tabs" id="recognition-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="recognition-tab" data-toggle="pill" href="#recognition" role="tab">
                    <i class="fas fa-heart mr-2"></i>Recognition Feed
                  </a>
                </li>
                                <li class="nav-item">
                                  <a class="nav-link" id="employee-month-tab" data-toggle="pill" href="#employee-month" role="tab">
                                    <i class="fas fa-star mr-2"></i>Employee of the Month
                                  </a>
                                </li>

                <li class="nav-item">
                  <a class="nav-link" id="badges-tab" data-toggle="pill" href="#badges" role="tab">
                    <i class="fas fa-medal mr-2"></i>Achievement Badges
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="rewards-tab" data-toggle="pill" href="#rewards" role="tab">
                    <i class="fas fa-gift mr-2"></i>Rewards & Incentives
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" id="leaderboard-tab" data-toggle="pill" href="#leaderboard" role="tab">
                    <i class="fas fa-trophy mr-2"></i>Points & Leaderboard
                  </a>
                </li>
              </ul>
            </div>

            <div class="card-body recognition-tabs-body">
              <div class="tab-content" id="recognition-tabs-content">

                <!-- Recognition Feed Tab -->
                <div class="tab-pane fade show active" id="recognition" role="tabpanel" aria-labelledby="recognition-tab">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="card card-success card-outline shadow-sm border-0 recognition-feed">
                        <div class="card-header d-flex align-items-center justify-content-between">
                          <h3 class="card-title mb-0"><i class="fas fa-heart mr-2"></i>Recognition Feed</h3>
                        </div>
                        <div class="card-body" id="recognition-feed">
                          <?php if (!empty($payload['recognitions'])): ?>
                            <div class="list-group list-group-flush">
                              <?php foreach ($payload['recognitions'] as $recognition): ?>
                                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2 shadow-sm">
                                  <div class="d-flex flex-column">
                                    <div class="mb-1">
                                      <strong><?= htmlspecialchars($recognition['sender_name']) ?></strong>
                                      <span class="text-muted">recognized</span>
                                      <strong><?= htmlspecialchars($recognition['receiver_name']) ?></strong>
                                    </div>
                                    <div class="text-muted small mb-1"><i class="fas fa-clock mr-1"></i><?= htmlspecialchars(date('M j, Y H:i', strtotime($recognition['created_at']))) ?></div>
                                    <div class="lh-relaxed">
                                      <?= htmlspecialchars($recognition['message']) ?>
                                    </div>
                                  </div>
                                  <span class="badge badge-success align-self-md-start align-self-end mt-2 mt-md-0">+<?= htmlspecialchars($recognition['points']) ?> pts</span>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <p class="text-muted text-center">No recognitions yet. Be the first to recognize a colleague!</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <!-- Quick Stats -->
                      <div class="card card-info card-outline shadow-sm border-0 mb-4">
                        <div class="card-header">
                          <h3 class="card-title mb-0"><i class="fas fa-chart-bar mr-2"></i>Quick Stats</h3>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-6 text-center">
                              <div class="text-muted">Total Recognitions</div>
                              <div class="h4 text-info font-weight-bold"><?= count($payload['recognitions'] ?? []) ?></div>
                            </div>
                            <div class="col-6 text-center">
                              <div class="text-muted">Points Awarded</div>
                              <div class="h4 text-success font-weight-bold">
                                <?= array_sum(array_column($payload['recognitions'] ?? [], 'points')) ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Recent Recognitions -->
                      <div class="card card-warning card-outline shadow-sm border-0 rounded-xl">
                        <div class="card-header recent-activity-header">
                          <h3 class="card-title mb-0 recent-activity-title"><i class="fas fa-clock mr-2"></i>Recent Activity</h3>
                        </div>
                        <div class="card-body p-0">
                          <?php
                          $recentRecognitions = array_slice($payload['recognitions'] ?? [], 0, 5);
                          if (!empty($recentRecognitions)):
                          ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($recentRecognitions as $recognition): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center recent-activity-item">
                                  <div>
                                    <strong class="text-sm recent-activity-name"></strong>
                                    <div class="text-muted text-xs">recognized <?= htmlspecialchars($recognition['receiver_name']) ?></div>
                                  </div>
                                  <span class="badge badge-success recent-activity-badge">+<?= htmlspecialchars($recognition['points']) ?></span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center p-3">No recent activity</p>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Performance Recommendations -->
                      <div class="card card-light card-outline shadow-sm border-0 mt-3">
                        <div class="card-header">
                          <h3 class="card-title mb-0"><i class="fas fa-thumbs-up mr-2"></i>Performance Recommendations</h3>
                        </div>
                        <div class="card-body p-2" id="performance-recommendations-list">
                          <?php if (!empty($payload['recognition_recommendations'])): ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($payload['recognition_recommendations'] as $rec): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center rounded-list-item">
                                  <div>
                                    <strong><?= htmlspecialchars($rec['employee_name']) ?></strong>
                                    <div class="text-muted small">
                                      <?= htmlspecialchars($rec['evaluation_period'] ?? 'Performance Report') ?> • Grade: <?= htmlspecialchars($rec['final_grade'] ?? 'N/A') ?> • Score: <?= htmlspecialchars($rec['final_rating_percent']) ?>%
                                    </div>
                                    <?php if (!empty($rec['period_end'])): ?>
                                      <div class="text-muted extra-small">Period end: <?= htmlspecialchars(date('M j, Y', strtotime($rec['period_end']))) ?></div>
                                    <?php endif; ?>
                                  </div>
                                  <div>
                                    <button class="btn btn-sm btn-outline-success recommend-recognize" data-employee-id="<?= htmlspecialchars($rec['employee_id']) ?>" data-employee-name="<?= htmlspecialchars($rec['employee_name']) ?>">Recognize</button>
                                  </div>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center m-2">No recommendations available yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Employees without Performance Reports -->
                      <div class="card card-secondary card-outline shadow-sm border-0 mt-3">
                        <div class="card-header">
                          <h3 class="card-title mb-0"><i class="fas fa-user-clock mr-2"></i>Employees without Performance Reports</h3>
                        </div>
                        <div class="card-body p-2">
                          <?php if (!empty($payload['employees_without_reports'])): ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($payload['employees_without_reports'] as $emp): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center rounded-list-item">
                                  <div>
                                    <strong><?= htmlspecialchars($emp['employee_name']) ?></strong>
                                    <div class="text-muted small"><?= htmlspecialchars($emp['department'] ?? 'No department') ?></div>
                                  </div>
                                  <span class="badge badge-danger">No Report</span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center m-2">All employees have performance report data.</p>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Top Performers -->
                      <div class="card card-info card-outline shadow-sm border-0 mt-3">
                        <div class="card-header">
                          <h3 class="card-title mb-0"><i class="fas fa-chart-line mr-2"></i>Top Performers</h3>
                        </div>
                        <div class="card-body p-2" id="top-performers-list">
                          <?php if (!empty($payload['top_performers'])): ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($payload['top_performers'] as $tp): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center rounded-list-item">
                                  <div>
                                    <strong><?= htmlspecialchars($tp['employee_name']) ?></strong>
                                    <?php $tpScore = $tp['final_rating_percent'] ?? $tp['performance_score'] ?? null; ?>
                                    <div class="text-muted small">Score: <?= $tpScore !== null ? htmlspecialchars($tpScore) . '%' : 'N/A' ?> • Grade: <?= htmlspecialchars($tp['final_grade'] ?? 'N/A') ?></div>
                                  </div>
                                  <div>
                                    <span class="badge badge-success">+<?= $tpScore !== null ? htmlspecialchars($tpScore) . '%' : 'N/A' ?></span>
                                  </div>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center m-2">No top performers found.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Employee of the Month Tab -->
                <div class="tab-pane fade" id="employee-month" role="tabpanel" aria-labelledby="employee-month-tab">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="card card-warning card-outline">
                        <div class="card-header d-flex align-items-center justify-content-between">
                          <h3 class="card-title"><i class="fas fa-star mr-2"></i>Employee of the Month Nominations</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#nominateEmployeeModal">
                              <i class="fas fa-plus mr-1"></i> Add Nomination
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <form id="employee-month-filter-form" method="GET" class="form-inline mb-3">
                            <label class="mr-2">Month</label>
                            <select id="employee-of-month-month" name="month" class="form-control mr-2 employee-month-select">
                              <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $selectedMonth == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                              <?php endfor; ?>
                            </select>
                            <label class="mr-2">Year</label>
                            <select id="employee-of-month-year" name="year" class="form-control mr-2 employee-year-select">
                              <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                              <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-warning">Apply</button>
                          </form>

                          <?php if (!empty($payload['employee_of_month_candidates'])): ?>
                            <div class="list-group" id="employee-month-candidates-list">
                              <?php foreach ($payload['employee_of_month_candidates'] as $candidate): ?>
                                <?php
                                $hasVoted = isset($candidate['has_voted']) ? (bool)$candidate['has_voted'] : (isset($_SESSION['employee_month_votes']) && in_array($candidate['eer_award_history_id'] ?? null, $_SESSION['employee_month_votes']));
                                $statusBadge = $candidate['status'] === 'winner' ? 'badge-success' : ($candidate['status'] === 'shortlisted' ? 'badge-info' : 'badge-warning');
                                ?>
                                <div class="list-group-item d-flex justify-content-between align-items-start candidate-item" data-award-history-id="<?= htmlspecialchars($candidate['eer_award_history_id']) ?>">
                                  <div>
                                    <strong><?= htmlspecialchars($candidate['employee_name'] ?? 'Unknown') ?></strong><br>
                                    <small class="text-muted">
                                      Department: <?= htmlspecialchars($candidate['department'] ?? 'N/A') ?> • Votes: <?= htmlspecialchars($candidate['votes'] ?? 0) ?> • Performance: <?= htmlspecialchars($candidate['performance_score'] ?? 0) ?>%
                                    </small>
                                    <div class="mt-1">
                                      <span class="badge <?= $statusBadge ?>"><?= htmlspecialchars(ucfirst($candidate['status'] ?? 'nominated')) ?></span>
                                      <?php if (!empty($candidate['nomination_reason'])): ?>
                                        <span class="text-muted small">Reason: <?= htmlspecialchars($candidate['nomination_reason']) ?></span>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                  <div class="text-right">
                                    <span class="badge badge-info">Recognition: <?= htmlspecialchars($candidate['recognition_total'] ?? 0) ?> pts</span>
                                    <?php if (!empty($candidate['eer_award_history_id'])): ?>
                                      <form method="POST" action="" class="mt-2 vote-form">
                                        <input type="hidden" name="action" value="vote_employee_month">
                                        <input type="hidden" name="award_history_id" value="<?= htmlspecialchars($candidate['eer_award_history_id']) ?>">
                                        <button type="submit" class="btn btn-sm <?= $hasVoted ? 'btn-success disabled' : 'btn-outline-warning' ?>" <?= $hasVoted ? 'disabled' : '' ?>>
                                          <i class="fas fa-vote-yea"></i> <?= $hasVoted ? 'Voted (+5)' : 'Vote +5' ?>
                                        </button>
                                      </form>
                                    <?php else: ?>
                                      <button type="button" class="btn btn-sm btn-outline-secondary mt-2" disabled>Not Nominated</button>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <p class="text-muted text-center">No nominations yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <!-- Current Employee of the Month -->
                      <div class="card card-warning card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-crown mr-2"></i>Current Winner</h3>
                        </div>
                        <div class="card-body text-center">
                          <?php
                          // Get the current month's employee of the month
                          $currentMonth = date('Y-m');
                          $currentWinner = null;
                          $winnerEmployee = null;
                          
                          // Search for Employee of the Month nominations for current month
                          foreach ($payload['award_history'] ?? [] as $award) {
                            if (strpos($award['award_name'], 'Employee of the Month') !== false) {
                              $awardMonth = date('Y-m', strtotime($award['created_at'] ?? $award['awarded_at'] ?? date('Y-m-d')));
                              if ($awardMonth === $currentMonth) {
                                $currentWinner = $award;
                                break;
                              }
                            }
                          }
                          
                          // Get the employee details
                          if ($currentWinner) {
                            $winnerEmployee = array_filter($payload['employees'] ?? [], function($emp) use ($currentWinner) {
                              return $emp['employee_id'] == $currentWinner['employee_id'];
                            });
                            $winnerEmployee = reset($winnerEmployee);
                          }
                          ?>
                          
                          <?php if ($winnerEmployee): ?>
                            <img src="../../assets/pics/default-avatar.png" class="img-circle elevation-2" alt="<?= htmlspecialchars($winnerEmployee['full_name'] ?? $winnerEmployee['employee_name']) ?>" style="width: 80px; height: 80px;">
                            <h5 class="mt-2"><?= htmlspecialchars($winnerEmployee['full_name'] ?? $winnerEmployee['employee_name']) ?></h5>
                            <p class="text-muted"><?= date('F Y') ?></p>
                            <span class="badge badge-warning"><i class="fas fa-crown mr-1"></i>Employee of the Month</span>
                            <?php if ($currentWinner && !empty($currentWinner['reason'])): ?>
                              <div class="mt-3 text-left">
                                <small class="text-muted">
                                  <strong>Reason:</strong> <?= htmlspecialchars($currentWinner['reason']) ?>
                                </small>
                              </div>
                            <?php endif; ?>
                          <?php else: ?>
                            <div class="text-center text-muted py-4">
                              <i class="fas fa-trophy fa-3x mb-3 text-warning"></i>
                              <p>No Employee of the Month selected yet for <?= date('F Y') ?></p>
                              <small>Nominations will be announced soon!</small>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Nomination Rules -->
                      <div class="card card-info card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Nomination Rules</h3>
                        </div>
                        <div class="card-body">
                          <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i> Any employee can nominate</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Must provide specific reason</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Voting period: 2 weeks</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Winner announced monthly</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Achievement Badges Tab -->
                <div class="tab-pane fade" id="badges" role="tabpanel" aria-labelledby="badges-tab">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="card card-info card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-medal mr-2"></i>Achievement Badges</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#assignBadgeModal">
                              <i class="fas fa-plus mr-1"></i>Assign Badge
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="badges-feed"></div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="card card-info card-outline mb-3">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-sparkles mr-2"></i>Recommended for You</h3>
                        </div>
                        <div class="card-body">
                          <?php if (!empty($payload['my_badge_recommendations'])): ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($payload['my_badge_recommendations'] as $recommendation): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                  <div>
                                    <strong><?= htmlspecialchars($recommendation['name']) ?></strong>
                                    <div class="text-muted small"><?= htmlspecialchars($recommendation['description'] ?? '') ?></div>
                                  </div>
                                  <span class="badge <?= $recommendation['status'] === 'owned' ? 'badge-success' : 'badge-info' ?>">
                                    <?= htmlspecialchars(ucfirst($recommendation['status'] ?? 'pending')) ?>
                                  </span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center">No badge recommendations available yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Employee Badges -->
                      <div class="card card-success card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-award mr-2"></i>Employee Badges</h3>
                        </div>
                        <div class="card-body">
                          <div id="employee-badges-feed" class="list-group"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Rewards & Incentives Tab -->
                <div class="tab-pane fade" id="rewards" role="tabpanel" aria-labelledby="rewards-tab">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="card card-primary card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-gift mr-2"></i>Rewards Catalog</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRewardModal">
                              <i class="fas fa-plus mr-1"></i>Add Reward
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="rewards-feed"></div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <!-- Reward Redemptions -->
                      <div class="card card-success card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Redemptions</h3>
                        </div>
                        <div class="card-body">
                          <div id="reward-redemptions-feed"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Points & Leaderboard Tab -->
                <div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="card card-warning card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-trophy mr-2"></i>Comprehensive Leaderboard</h3>
                        </div>
                        <div class="card-body">
                          <?php if (!empty($payload['comprehensive_leaderboard'])): ?>
                            <div class="table-responsive">
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>Rank</th>
                                    <th>Employee</th>
                                    <th>Recognition</th>
                                    <th>Performance</th>
                                    <th>Badges</th>
                                    <th>Awards</th>
                                    <th>Total</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php foreach ($payload['comprehensive_leaderboard'] as $entry): ?>
                                    <tr>
                                      <td>
                                        <span class="badge <?= ($entry['rank_position'] ?? 1) <= 3 ? 'badge-warning' : 'badge-secondary' ?>">
                                          <?= htmlspecialchars($entry['rank_position'] ?? 1) ?>
                                        </span>
                                      </td>
                                      <td>
                                        <strong><?= htmlspecialchars($entry['employee_name'] ?? 'Unknown') ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($entry['department'] ?? 'N/A') ?></div>
                                      </td>
                                      <td><?= htmlspecialchars($entry['recognition_points'] ?? 0) ?></td>
                                      <td><?= htmlspecialchars($entry['performance_points'] ?? 0) ?></td>
                                      <td><?= htmlspecialchars($entry['badge_points'] ?? 0) ?></td>
                                      <td><?= htmlspecialchars($entry['award_points'] ?? 0) ?></td>
                                      <td><span class="badge badge-success badge-pill"><?= htmlspecialchars($entry['total_points'] ?? 0) ?> pts</span></td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          <?php else: ?>
                            <p class="text-muted text-center">No leaderboard data available yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="card card-info card-outline mb-3">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-coins mr-2"></i>My Points Tracker</h3>
                        </div>
                        <div class="card-body">
                          <?php if (!empty($payload['my_points'])): ?>
                            <div class="text-center">
                              <div class="h2 text-success mb-1"><?= htmlspecialchars($payload['my_points']['total_points'] ?? 0) ?></div>
                              <div class="text-muted">Total points</div>
                              <div class="mt-3">
                                <span class="badge badge-info">Tier: <?= htmlspecialchars(getTierLabel((int)($payload['my_points']['total_points'] ?? 0))) ?></span>
                              </div>
                            </div>
                            <div class="row mt-3 text-center">
                              <div class="col-6 mb-2">
                                <div class="small text-muted">Recognition</div>
                                <strong><?= htmlspecialchars($payload['my_points']['recognition_points'] ?? 0) ?></strong>
                              </div>
                              <div class="col-6 mb-2">
                                <div class="small text-muted">Performance</div>
                                <strong><?= htmlspecialchars($payload['my_points']['performance_points'] ?? 0) ?></strong>
                              </div>
                              <div class="col-6 mb-2">
                                <div class="small text-muted">Badges</div>
                                <strong><?= htmlspecialchars($payload['my_points']['badge_points'] ?? 0) ?></strong>
                              </div>
                              <div class="col-6 mb-2">
                                <div class="small text-muted">Awards</div>
                                <strong><?= htmlspecialchars($payload['my_points']['award_points'] ?? 0) ?></strong>
                              </div>
                            </div>
                          <?php else: ?>
                            <p class="text-muted text-center">Sign in with an employee profile to see your points breakdown.</p>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="card card-secondary card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-users mr-2"></i>Department Leaderboard</h3>
                        </div>
                        <div class="card-body">
                          <?php if (!empty($payload['department_leaderboard'])): ?>
                            <ul class="list-group list-group-flush">
                              <?php foreach ($payload['department_leaderboard'] as $deptEntry): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                  <div>
                                    <strong><?= htmlspecialchars($deptEntry['employee_name'] ?? 'Unknown') ?></strong>
                                    <div class="text-muted small"><?= htmlspecialchars($deptEntry['department'] ?? 'N/A') ?></div>
                                  </div>
                                  <div class="text-right">
                                    <span class="badge badge-info">#<?= htmlspecialchars($deptEntry['dept_rank'] ?? 1) ?></span>
                                    <div class="text-muted small"><?= htmlspecialchars($deptEntry['total_points'] ?? 0) ?> pts</div>
                                  </div>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          <?php else: ?>
                            <p class="text-muted text-center">No department ranking data yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  <!-- CONTENT -->

    </div>
    <?php include "../../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->

  </div>
  <!-- ./wrapper -->

  <!-- Modals -->

  <!-- Send Recognition Modal -->
  <div class="modal fade" id="sendRecognitionModal" tabindex="-1" role="dialog" aria-labelledby="sendRecognitionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sendRecognitionModalLabel"><i class="fas fa-heart mr-2"></i>Send Recognition</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <div class="form-group">
              <label for="rec-receiver">Recognize Employee</label>
              <select name="receiver_id" id="rec-receiver" class="form-control no-select-arrow" disabled aria-disabled="true">
              </select>
              <small class="form-text text-muted">Employee selection is locked in this feed.</small>
            </div>
            <div class="form-group">
              <label for="rec-message">Message</label>
              <textarea name="message" id="rec-message" class="form-control" rows="3" placeholder="Why are you recognizing this employee?" required></textarea>
            </div>
            <div class="form-group">
              <label for="rec-points">Points</label>
              <input type="number" name="points" id="rec-points" class="form-control" value="10" min="1" max="100" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" id="send-recognition-btn" class="btn btn-success">Send Recognition</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Nominate Employee Modal -->
  <div class="modal fade" id="nominateEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="nominateEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="nominateEmployeeModalLabel"><i class="fas fa-star mr-2"></i>Nominate for Employee of the Month</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <div class="form-group">
              <label for="nominate_employee_id">Select Employee</label>
              <select name="nominate_employee_id" id="nominate_employee_id" class="form-control" required>
                <option value="">Choose a recognized employee...</option>
                <?php
                $recognizedEmployees = $payload['recently_recognized'] ?? [];
                if (!empty($recognizedEmployees)):
                  foreach ($recognizedEmployees as $recognized): ?>
                    <option value="<?= $recognized['employee_id'] ?>">
                      <?= htmlspecialchars($recognized['full_name'] ?? 'Unknown') ?>
                      (<?= $recognized['recognition_count'] ?> recognitions)
                    </option>
                  <?php endforeach;
                else: ?>
                  <option value="" disabled>No recognized employees available</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="nomination_reason">Why should they be Employee of the Month?</label>
              <textarea name="nomination_reason" id="nomination_reason" class="form-control" rows="4"
                        placeholder="Describe their achievements, contributions, and why they deserve this recognition..." required></textarea>
            </div>
            <div class="alert alert-info">
              <i class="fas fa-info-circle mr-2"></i>
              <strong>Nomination Process:</strong> All nominations will be reviewed and employees can vote for 2 weeks. The winner will be announced at the end of the month.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Submit Nomination</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Assign Badge Modal -->
  <div class="modal fade" id="assignBadgeModal" tabindex="-1" role="dialog" aria-labelledby="assignBadgeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assignBadgeModalLabel"><i class="fas fa-medal mr-2"></i>Assign Achievement Badge</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="assign-badge-form" method="POST" action="">
          <div class="modal-body">
            <div class="form-group">
              <label for="badge_employee_id">Select Employee</label>
              <select name="badge_employee_id" id="badge_employee_id" class="form-control" required>
                <option value="">Choose an employee...</option>
                <?php foreach ($payload['employees'] as $employee): ?>
                  <?php if (in_array($employee['employee_id'], $payload['nominated_employee_ids'] ?? [])): ?>
                    <option value="<?= $employee['employee_id'] ?>">
                      <?= htmlspecialchars($employee['full_name'] ?? $employee['employee_name'] ?? 'Unknown') ?> (Nominated)
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="badge_id">Select Badge</label>
              <select name="badge_id" id="badge_id" class="form-control" required>
                <option value="">Choose a badge...</option>
                <?php foreach ($payload['badges'] as $badge): ?>
                  <option value="<?= $badge['eer_badge_id'] ?>">
                    <?= htmlspecialchars($badge['name']) ?> - <?= htmlspecialchars($badge['description']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-info">Assign Badge</button>
          </div>
        </form>
      </div>
    </div>
  </div>



  <!-- Add Reward Modal -->
  <div class="modal fade" id="addRewardModal" tabindex="-1" role="dialog" aria-labelledby="addRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addRewardModalLabel"><i class="fas fa-plus mr-2"></i>Add New Reward</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST">
          <input type="hidden" name="action" value="add_reward">
          <div class="modal-body">
            <div class="form-group">
              <label for="rewardName">Reward Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="rewardName" name="reward_name" placeholder="e.g., Gift Card, Voucher, Badge" required>
            </div>
            <div class="form-group">
              <label for="rewardDescription">Description</label>
              <textarea class="form-control" id="rewardDescription" name="reward_description" rows="3" placeholder="e.g., Redeem for a store gift card"></textarea>
            </div>
            <div class="form-group">
              <label for="rewardPoints">Points Required <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="rewardPoints" name="reward_points" placeholder="e.g., 100" min="1" required>
              <small class="form-text text-muted">How many recognition points are needed to redeem this reward?</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save mr-1"></i>Add Reward
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../assets/dist/js/profile.js"></script>
  <script src="js/live_updates.js"></script>

  <script src="js/recognition.js"></script>

  <script>
    // Handle Assign Badge Button Clicks
    document.addEventListener('DOMContentLoaded', function() {
      const assignBadgeButtons = document.querySelectorAll('.assign-badge-btn');
      
      assignBadgeButtons.forEach(button => {
        button.addEventListener('click', function() {
          const badgeId = this.getAttribute('data-badge-id');
          const badgeName = this.getAttribute('data-badge-name');
          
          // Set the badge dropdown to the selected badge
          const badgeDropdown = document.getElementById('badge_id');
          if (badgeDropdown) {
            badgeDropdown.value = badgeId;
          }
          
          // Reset employee selection
          const employeeDropdown = document.getElementById('badge_employee_id');
          if (employeeDropdown) {
            employeeDropdown.value = '';
          }
          
          // Show the modal
          $('#assignBadgeModal').modal('show');
        });
      });
    });
  </script>
</body>

</html>

