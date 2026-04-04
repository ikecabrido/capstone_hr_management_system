<?php
session_start();

require_once "../../auth/auth_check.php";


require_once __DIR__ . '/../autoload.php';

use App\Controllers\EmployeeController;


use App\Controllers\SurveyController;
use App\Controllers\RecognitionController;
use App\Controllers\GrievanceController;
use App\Controllers\CommunicationController;
use App\Controllers\SocialController;
use App\Controllers\FeedbackController;
use App\Controllers\GroupController;

$theme = $_SESSION['user']['theme'] ?? 'light';


$surveyCtrl = new SurveyController();
$recognitionCtrl = new RecognitionController();
$grievanceCtrl = new GrievanceController();
$communicationCtrl = new CommunicationController();
$socialCtrl = new SocialController();
$feedbackCtrl = new FeedbackController();
$employeeCtrl = new EmployeeController();
$groupCtrl = new GroupController();

$payload = $payload ?? [];
$payload['surveys'] = $surveyCtrl->index();
$payload['recognitions'] = $recognitionCtrl->getRecognitions();
$payload['grievances'] = $grievanceCtrl->getGrievances();
$payload['feedback'] = $feedbackCtrl->index();
$payload['announcements'] = $communicationCtrl->getAnnouncements();
$payload['feed'] = $socialCtrl->getPosts();
$payload['employees'] = $employeeCtrl->index();
$payload['groups'] = $groupCtrl->getGroups();
$payload['notifications'] = [];
$payload['notifications'] = $communicationCtrl->getNotifications();


// Data will be loaded via API using JavaScript
// var_dump($payload);
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
          <a href="../engagement_relations.php" class="nav-link">Home</a>
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
              <a href="dashboard.php" class="nav-link active">
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
                <i class="nav-icon fas fa-tree"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
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
               <h1 class="m-0">Dashboard</h1>
              <p class="text-muted">Use the sidebar to manage surveys, recognitions, grievances, announcements, messages, and social posts.</p>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-info">
          <div class="inner">
            <h3 id="count-surveys"><?= count($payload['surveys'] ?? []) ?></h3>
            <p>Surveys</p>
          </div>
          <div class="icon"><i class="fas fa-poll"></i></div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-success">
          <div class="inner">
            <h3 id="count-recognitions"><?= count($payload['recognitions'] ?? []) ?></h3>
            <p>Recognitions</p>
          </div>
          <div class="icon"><i class="fas fa-award"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3 id="count-grievances"><?= count($payload['grievances'] ?? []) ?></h3>
            <p>Grievances</p>
          </div>
          <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3 id="count-announcements"><?= count($payload['announcements'] ?? []) ?></h3>
            <p>Announcements</p>
          </div>
          <div class="icon"><i class="fas fa-bullhorn"></i></div>
        </div>
      </div>
           <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3 id="count-feedback"><?= count($payload['feedback'] ?? []) ?></h3>
            <p>Feedback</p>
          </div>
          <div class="icon"><i class="fas fa-comments"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-secondary">
          <div class="inner">
            <h3 id="count-feed"><?= count($payload['feed'] ?? []) ?></h3>
            <p>Social posts</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-indigo">
          <div class="inner">
            <h3 id="count-employees"><?= count($payload['employees'] ?? []) ?></h3>
            <p>Employees</p>
          </div>
          <div class="icon"><i class="fas fa-user-friends"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-teal">
          <div class="inner">
            <h3 id="count-groups"><?= count($payload['groups'] ?? []) ?></h3>
            <p>Groups</p>
          </div>
          <div class="icon"><i class="fas fa-object-group"></i></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-purple">
          <div class="inner">
            <h3 id="count-notifications"><?= count($payload['notifications'] ?? []) ?></h3>
            <p>Notifications</p>
          </div>
          <div class="icon"><i class="fas fa-bell"></i></div>
        </div>
      </div>
    </div>

    <!-- Quick Overviews -->
    <?php
      $latestGrievances = array_slice($payload['grievances'] ?? [], 0, 3);
      $latestFeed = array_slice($payload['feed'] ?? [], 0, 3);
      $latestSurveys = array_slice($payload['surveys'] ?? [], 0, 3);
    ?>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Notifications</h5>
            <span class="badge badge-primary"><?= count($payload['notifications'] ?? []) ?> total</span>
          </div>
          <div class="card-body">
            <?php if (!empty($payload['notifications'])): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($payload['notifications'] as $notification): ?>
                  <li class="list-group-item">
                    <strong><?= htmlspecialchars($notification['title'] ?? $notification['message'] ?? 'Notification') ?></strong><br>
                    <small><?= htmlspecialchars($notification['created_at'] ?? '') ?> | <?= htmlspecialchars($notification['created_by_name'] ?? $notification['created_by'] ?? '') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted mb-0">No notifications available.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Latest Grievances</h5>
            <span class="badge badge-primary"><?= count($payload['grievances'] ?? []) ?> total</span>
          </div>
          <div class="card-body">
            <?php if (!empty($latestGrievances)): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($latestGrievances as $grievance): ?>
                  <li class="list-group-item">
                    <strong><?= htmlspecialchars($grievance['subject'] ?? 'No title') ?></strong><br>
                    <small>Status: <?= htmlspecialchars($grievance['status'] ?? 'Unknown') ?> | Filed by: <?= htmlspecialchars($grievance['employee_name'] ?? 'Unknown') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php if (count($payload['grievances'] ?? []) > 3): ?>
                <div class="mt-3 text-right"><small>Showing latest 3 of <?= count($payload['grievances']) ?></small></div>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted">No grievances yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Latest Social Posts</h5>
            <span class="badge badge-primary"><?= count($payload['feed'] ?? []) ?> total</span>
          </div>
          <div class="card-body">
            <?php if (!empty($latestFeed)): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($latestFeed as $post): ?>
                  <li class="list-group-item">
                    <?= htmlspecialchars(mb_strimwidth($post['content'] ?? 'No content', 0, 80, '...')) ?><br>
                    <small>By: <?= htmlspecialchars($post['author_name'] ?? $post['employee_name'] ?? 'Unknown') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php if (count($payload['feed'] ?? []) > 3): ?>
                <div class="mt-3 text-right"><small>Showing latest 3 of <?= count($payload['feed']) ?></small></div>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted">No social posts yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Latest Surveys</h5>
            <span class="badge badge-primary"><?= count($payload['surveys'] ?? []) ?> total</span>
          </div>
          <div class="card-body">
            <?php if (!empty($latestSurveys)): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($latestSurveys as $survey): ?>
                  <li class="list-group-item">
                    <strong><?= htmlspecialchars($survey['title'] ?? 'No title') ?></strong><br>
                    <small>Created by: <?= htmlspecialchars($survey['created_by_name'] ?? $survey['created_by'] ?? 'Unknown') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php if (count($payload['surveys'] ?? []) > 3): ?>
                <div class="mt-3 text-right"><small>Showing latest 3 of <?= count($payload['surveys']) ?></small></div>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted">No surveys yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Latest Survey Feedback -->
    <div class="row mt-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Latest Survey Feedback</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($payload['feedback'])): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($payload['feedback'] as $feedback): ?>
                  <li class="list-group-item">
                    <?= htmlspecialchars($feedback['survey_title'] ?? 'No title available') ?><br>
                    <small>By: <?= htmlspecialchars($feedback['employee_name'] ?? 'Unknown') ?> | <?= htmlspecialchars($feedback['comment'] ?? 'No comments available') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted">No feedback yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Latest Communications -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Latest Communications</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($payload['announcements'])): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($payload['announcements'] as $announcement): ?>
                  <li class="list-group-item">
                    <?= htmlspecialchars($announcement['title'] ?? 'No title available') ?><br>
                    <small>By: <?= htmlspecialchars($announcement['created_by_name'] ?? $announcement['created_by'] ?? 'Unknown') ?> | <?= htmlspecialchars($announcement['created_at'] ?? 'Unknown') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted">No announcements yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Latest Recognitions -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Latest Recognitions</h5>
          </div>
          <div class="card-body">
            <?php if (!empty($payload['recognitions'])): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($payload['recognitions'] as $recognition): ?>
                  <li class="list-group-item">
                    <?= htmlspecialchars($recognition['message'] ?? 'No message available') ?><br>
                    <small>From: <?= htmlspecialchars($recognition['sender_name'] ?? 'Unknown') ?> | To: <?= htmlspecialchars($recognition['receiver_name'] ?? 'Unknown') ?> | Points: <?= htmlspecialchars($recognition['points'] ?? '0') ?></small>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted">No recognitions yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    

          </div>
        </div>
      </section>
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
  <script src="../../assets/dist/js/profile.js"></script>

  <script src="js/dashboard.js"></script>
</body>

</html>
