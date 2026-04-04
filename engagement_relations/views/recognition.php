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

$theme = $_SESSION['user']['theme'] ?? 'light';

$ctrl = new RecognitionController();
$grievanceCtrl = new GrievanceController();
$socialCtrl = new SocialController();
$surveyCtrl = new SurveyController();
$feedbackCtrl = new FeedbackController();
$communicationCtrl = new CommunicationController();


$payload = $payload ?? [];
// $payload['recognitions'] = $ctrl->getRecognitions();

// Only include Employee Recognition & Rewards tables
use App\Controllers\RewardController;
use App\Controllers\RewardRedemptionController;
use App\Controllers\BadgeController;
use App\Controllers\EmployeeBadgeController;
use App\Controllers\AwardHistoryController;

$rewardCtrl = new RewardController();
$rewardRedemptionCtrl = new RewardRedemptionController();
$badgeCtrl = new BadgeController();
$employeeBadgeCtrl = new EmployeeBadgeController();
$awardHistoryCtrl = new AwardHistoryController();

$payload['rewards'] = $rewardCtrl->index();
$payload['reward_redemptions'] = $rewardRedemptionCtrl->index();
$payload['badges'] = $badgeCtrl->index();
$payload['employee_badges'] = $employeeBadgeCtrl->index();
$payload['award_history'] = $awardHistoryCtrl->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['receiver_id']) && !empty($_POST['message'])) {
  $currentEmployeeId = $_SESSION['user']['employee_id'] ?? null;
  $currentUserId = $_SESSION['user']['id'] ?? null;
  $senderId = $currentEmployeeId ?? $currentUserId;
  if ($senderId) {
    $ctrl->sendRecognition($senderId, $_POST['receiver_id'], $_POST['message'], (int)($_POST['points'] ?? 10));
    $_SESSION['flash_success'] = 'Recognition sent successfully.';
  }
  header('Location: ' . $_SERVER['REQUEST_URI']);
  exit;
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
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


        <!-- MAIN CONTENT --
            <!-- HEADER -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h3>Recognition</h3>
                <p class="text-muted">Employee recognition and rewards</p>  
            </div>
          </div>
        </div>

        <div class="card card-success card-outline">
        <?php if ($flashSuccess): ?>
          <div class="alert alert-success"> <?= htmlspecialchars($flashSuccess) ?> </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
          <div class="alert alert-danger"> <?= htmlspecialchars($flashError) ?> </div>
        <?php endif; ?>

        <!-- Recognition Form -->
        <section class="content">
          <div class="container-fluid">
            <form method="POST" action="">
              <div class="form-group">
                <label for="receiver_id">Select Employee</label>
                <select name="receiver_id" id="receiver_id" class="form-control">
                  <?php foreach ($payload['employee_badges'] as $employee): ?>
                    <option value="<?= $employee['employee_id'] ?>">
                      <?= htmlspecialchars($employee['employee_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <label for="points">Points</label>
                <input type="number" name="points" id="points" class="form-control" value="10" />
              </div>
              <button type="submit" class="btn btn-primary">Send Recognition</button>
            </form>
          </div>
        </section>
      </div>


        <div class="card card-success card-outline">
          <div class="card-header"><h3 class="card-title">Recognition Feed</h3></div>
          <div class="card-body" id="recognition-feed">
            <!-- Recognition feed will be loaded here by JS -->
          </div>
        </div>

      <!-- Add containers for each feed below the recognition feed -->

        <div class="card card-info card-outline">
          <div class="card-header"><h3 class="card-title">Badges</h3></div>
          <div class="card-body" id="badges-feed"></div>
        </div>


        <div class="card card-warning card-outline">
          <div class="card-header"><h3 class="card-title">Award History</h3></div>
          <div class="card-body" id="award-history-feed"></div>
        </div>


        <div class="card card-primary card-outline">
          <div class="card-header"><h3 class="card-title">Rewards</h3></div>
          <div class="card-body" id="rewards-feed"></div>
        </div>


        <div class="card card-success card-outline">
          <div class="card-header"><h3 class="card-title">Reward Redemptions</h3></div>
          <div class="card-body" id="reward-redemptions-feed"></div>
        </div>


        <div class="card card-secondary card-outline">
          <div class="card-header"><h3 class="card-title">Employee Badges</h3></div>
          <div class="card-body" id="employee-badges-feed"></div>
        </div>


    <!-- Leaderboard Section -->
  <div class="card card-info card-outline">
    <div class="card-header"><h3 class="card-title">Leaderboard</h3></div>
    <div class="card-body">
      <ul class="list-group">
        <li class="list-group-item">Employee A - 120 Points</li>
        <li class="list-group-item">Employee B - 100 Points</li>
        <li class="list-group-item">Employee C - 90 Points</li>
      </ul>
    </div>
  </div>

  <!-- Rewards Catalog Section -->
  <div class="card card-success card-outline">
    <div class="card-header"><h3 class="card-title">Rewards Catalog</h3></div>
    <div class="card-body">
      <ul class="list-group">
        <li class="list-group-item">Gift Card - 50 Points</li>
        <li class="list-group-item">Extra Leave - 100 Points</li>
        <li class="list-group-item">Company Swag - 30 Points</li>
      </ul>
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

  <script src="js/recognition.js"></script>
</body>

</html>

