<?php
session_start();
// require_once "auth.php";
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

require_once __DIR__ . '/../autoload.php';


use App\Controllers\SurveyController;
use App\Controllers\GrievanceController;
use App\Controllers\RecognitionController;
use App\Controllers\SocialController;
use App\Controllers\FeedbackController;
use App\Controllers\CommunicationController;
use App\Controllers\SurveyAnswerController;

$theme = $_SESSION['user']['theme'] ?? 'light';

$surveyCtrl = new SurveyController();
$grievanceCtrl = new GrievanceController();
$recognitionCtrl = new RecognitionController();
$socialCtrl = new SocialController();
$feedbackCtrl = new FeedbackController();
$communicationCtrl = new CommunicationController();
$surveyAnswerCtrl = new SurveyAnswerController();

$payload = $payload ?? [];
$payload['surveys'] = $surveyCtrl->index();
$payload['grievances'] = $grievanceCtrl->getGrievances();
$payload['recognitions'] = $recognitionCtrl->getRecognitions();
$payload['social'] = $socialCtrl->getPosts();
$payload['feedback'] = $feedbackCtrl->index();
$payload['announcements'] = $communicationCtrl->getAnnouncements();
$payload['survey_answers'] = $payload['survey_answers'] ?? [];

// Debug log to verify the structure of survey_answers data
error_log('Survey Answers Data: ' . print_r($payload['survey_answers'], true));

// Debug log to verify survey_answers data
error_log('Survey Answers Payload: ' . print_r($payload['survey_answers'], true));

// Fetch survey answers for a specific survey or response
$surveyId = null;
$responseId = null;
$apiUrl = '';
if (isset($_GET['response_id']) && is_numeric($_GET['response_id'])) {
    $responseId = (int)$_GET['response_id'];
} elseif (isset($_GET['survey_id']) && is_numeric($_GET['survey_id'])) {
    $surveyId = (int)$_GET['survey_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $surveyId = (int)$_GET['id'];
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if ($responseId !== null) {
    $payload['survey_answers'] = $surveyAnswerCtrl->getByResponse($responseId);
} elseif ($surveyId !== null) {
    $payload['survey_answers'] = $surveyAnswerCtrl->getBySurvey($surveyId);
} else {
    $payload['survey_answers'] = $surveyAnswerCtrl->getAll();
}

// Fetch survey results for a specific survey
if (isset($_GET['action']) && $_GET['action'] === 'view_results' && isset($_GET['survey_id'])) {
    $surveyId = (int)$_GET['survey_id'];
    $results = $surveyCtrl->getSurveyResults($surveyId);
    $payload['survey_results'] = $results;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flashSuccess = '';
    $flashError = '';

    if (!empty($_POST['action']) && $_POST['action'] === 'delete') {
        $surveyId = (int) ($_POST['survey_id'] ?? 0);

        if ($surveyId > 0) {
            $deleted = $surveyCtrl->delete($surveyId);
            if ($deleted) {
                $_SESSION['flash_success'] = 'Survey deleted successfully.';
            } else {
                $_SESSION['flash_error'] = 'Failed to delete survey. It may not exist.';
            }
        } else {
            $_SESSION['flash_error'] = 'Invalid survey ID for deletion.';
        }
    } elseif (!empty($_POST['title']) && !empty($_POST['questions_raw'])) {
        $questions = array_map('trim', explode(',', $_POST['questions_raw']));
        $formatted = array_map(function ($q) {
            return ['question_text' => $q];
        }, array_filter($questions));

        $employeeId = (int)($_SESSION['user']['id'] ?? 0);
        if ($employeeId > 0) {
            $surveyCtrl->store($_POST['title'], $employeeId, $formatted);
            $_SESSION['flash_success'] = 'Survey created successfully.';
        }
    } else {
        $_SESSION['flash_error'] = 'Title and at least one question are required.';
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
  <link rel="stylesheet" href="css/survey.css" />
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
              <a href="survey.php" class="nav-link active">
                <i class="nav-icon fas fa-poll"></i>
                <p>Survey</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="recognition.php" class="nav-link">
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
              <h3>Surveys & Feedback</h3>
              <p class="text-muted">Use the sidebar to manage surveys and feedback workflow.</p>
            </div>
          </div>
        </div>

    <div class="survey-area">
      <div class="row">
        <div class="col-12">
          <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success"><?=htmlspecialchars($flashSuccess)?></div>
          <?php endif; ?>
          <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($flashError)?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="row">
      <div class="col-12">
        <div class="card card-secondary card-outline">
          <div class="card-header"><h3 class="card-title">Create Survey</h3></div>
          <div class="card-body">
            <form method="post" class="survey-form">
              <div class="form-group">
                <label for="survey-title">Survey Title</label>
                <input id="survey-title" type="text" name="title" class="form-control" placeholder="Enter survey title" required>
              </div>
              <div class="form-group">
                <label for="survey-questions">Questions (comma-separated)</label>
                <input id="survey-questions" type="text" name="questions_raw" class="form-control" placeholder="Question 1, Question 2" required>
              </div>
              <button class="btn btn-success" type="submit">Create Survey</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card card-success card-outline">
          <div class="card-header"><h3 class="card-title">Available Surveys</h3></div>
          <div class="card-body">
            <?php if (!empty($payload['surveys'])): ?>
              <ul class="list-group">
                <?php foreach ($payload['surveys'] as $survey): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?=htmlspecialchars($survey['title'])?></strong><br>
                      <small>Survey ID: <?=htmlspecialchars($survey['eer_survey_id'])?></small>
                    </div>
                    <div class="btn-group" role="group" aria-label="Survey actions">
                      <a class="btn btn-sm btn-info" href="survey_view.php?module=survey&action=view&id=<?=$survey['eer_survey_id']?>">View</a>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted">No surveys yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card card-warning card-outline">
          <div class="card-header"><h3 class="card-title">Survey Feedback</h3></div>
          <div class="card-body">
            <?php if (!empty($payload['feedback'])): ?>
              <ul class="list-group">
                <?php foreach ($payload['feedback'] as $feedback): ?>
                  <li class="list-group-item">
                    <strong><?=htmlspecialchars($feedback['employee_name'] ?? 'Anonymous')?></strong>
                    <span class="text-muted">(survey #<?= (int) $feedback['survey_id']?>)</span>
                    <p><?=nl2br(htmlspecialchars($feedback['comment']))?></p>
                    <small>
                      Rating: <?= $feedback['rating'] !== null ? (int)$feedback['rating'] . '/5' : 'n/a' ?>
                      <?php if (isset($feedback['created_at'])): ?>
                        &bull; <?=htmlspecialchars($feedback['created_at'])?>
                      <?php endif; ?>
                    </small>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted">No feedback submitted yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>


    <!-- Survey Answers Section -->
  <div class="row">
    <div class="col-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Survey Answers</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($payload['survey_answers'])): ?>
            <ul class="list-group">
              <?php foreach ($payload['survey_answers'] as $answer): ?>
                <li class="list-group-item">
                  <strong>Question ID:</strong> <?= htmlspecialchars($answer['question_id'] ?? 'N/A') ?><br>
                  <strong>Answer:</strong> <?= htmlspecialchars($answer['answer'] ?? 'N/A') ?><br>
                  <small><strong>Answer Record:</strong> <?= htmlspecialchars($answer['eer_survey_answer_id'] ?? 'N/A') ?> | <strong>Response:</strong> <?= htmlspecialchars($answer['response_id'] ?? 'N/A') ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No answers available for this survey.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php
    $surveyChartLabels = [];
    $surveyChartValues = [];
    if (!empty($payload['survey_answers'])) {
        $answerCounts = [];
        foreach ($payload['survey_answers'] as $answer) {
            $label = 'Question ' . ($answer['question_id'] ?? 'N/A');
            $answerCounts[$label] = ($answerCounts[$label] ?? 0) + 1;
        }
        $surveyChartLabels = array_keys($answerCounts);
        $surveyChartValues = array_values($answerCounts);
    }
  ?>

  <!-- Survey Results Section -->
  <div class="card card-info card-outline">
    <div class="card-header"><h3 class="card-title">Survey Results</h3></div>
    <div class="card-body">
      <canvas
        id="surveyResultsChart"
        data-survey-labels='<?= json_encode($surveyChartLabels) ?>'
        data-survey-values='<?= json_encode($surveyChartValues) ?>'
      ></canvas>
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

  <script src="js/survey.js"></script>
</body>

</html>

