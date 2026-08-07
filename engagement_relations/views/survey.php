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

$userRole = strtolower(trim($_SESSION['user']['role'] ?? ''));
$userName = strtolower(trim($_SESSION['user']['name'] ?? ''));
$username = strtolower(trim($_SESSION['user']['username'] ?? ''));
$showHrFeedbackTab = preg_match('/\b(hr|human resources|hr manager|admin|administrator|default_admin)\b/i', $userRole)
    || stripos($userRole, 'default_admin') !== false
    || in_array($username, ['admin', 'hr_engagement'], true)
    || stripos($userName, 'admin') !== false;


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

    if (!empty($_POST['action']) && $_POST['action'] === 'hr_feedback') {
        // Handle HR feedback submission
        $feedbackData = [
            'employee_id' => $_POST['employee_id'] ?? null,
            'comment' => $_POST['comments'] ?? '',
            'category' => $_POST['category'] ?? 'general',
            'rating' => !empty($_POST['rating']) ? (int)$_POST['rating'] : null,
            'evaluator_type' => 'HR',
            'is_anonymous' => 0,
            'allow_followup' => 1
        ];

        try {
            $feedbackCtrl->store(
                $feedbackData['employee_id'],
                $feedbackData['comment'],
                $feedbackData['rating'],
                $feedbackData['evaluator_type'],
                $feedbackData['category'],
                $feedbackData['is_anonymous']
            );
            $_SESSION['flash_success'] = 'Feedback submitted successfully to the employee.';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error submitting feedback. Please try again.';
        }
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'submit_suggestion') {
        $suggestionComment = trim($_POST['comment'] ?? '');
        $suggestionCategory = $_POST['category'] ?? 'other';
        $suggestionRating = !empty($_POST['rating']) ? (int)$_POST['rating'] : null;
        $isAnonymous = !empty($_POST['is_anonymous']) ? 1 : 0;
        $employeeId = $_SESSION['user']['employee_id'] ?? $_SESSION['user']['id'] ?? null;

        if ($suggestionComment === '') {
            $_SESSION['flash_error'] = 'Please add a suggestion before submitting.';
        } elseif (empty($employeeId)) {
            $_SESSION['flash_error'] = 'Unable to identify the submitting employee.';
        } else {
            try {
                $feedbackCtrl->store(
                    $employeeId,
                    $suggestionComment,
                    $suggestionRating,
                    'Suggestion',
                    $suggestionCategory,
                    $isAnonymous
                );
                $_SESSION['flash_success'] = 'Suggestion submitted successfully.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error submitting suggestion. Please try again.';
            }
        }
    } elseif (!empty($_POST['action']) && $_POST['action'] === 'delete') {
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
        // Handle survey creation
        $questions = array_map('trim', explode("\n", $_POST['questions_raw']));
        $formatted = array_map(function ($q) {
            return ['question_text' => $q];
        }, array_filter($questions));

        $surveyData = [
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'survey_type' => $_POST['survey_type'] ?? 'satisfaction',
            'is_anonymous' => isset($_POST['is_anonymous']) ? 1 : 0
        ];

        $employeeId = (int)($_SESSION['user']['id'] ?? 0);
        if ($employeeId > 0) {
            try {
                $surveyCtrl->store($surveyData, $formatted, $employeeId);
                $surveyTypeName = $surveyData['survey_type'] === 'pulse' ? 'Pulse Survey' : 'Satisfaction Survey';
                $_SESSION['flash_success'] = $surveyTypeName . ' created successfully.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error creating survey: ' . $e->getMessage();
            }
        } else {
            $_SESSION['flash_error'] = 'User authentication required.';
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


        <!-- MAIN CONTENT -->
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
      <!-- Main survey tab navigation and content -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm border-0">
            <div class="card-header p-0">
              <ul class="nav nav-tabs survey-nav-tabs" id="survey-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="satisfaction-tab" data-toggle="tab" href="#satisfaction" role="tab" aria-controls="satisfaction" aria-selected="true">
                    <i class="fas fa-chart-line mr-2"></i>Employee Satisfaction Surveys
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="pulse-tab" data-toggle="tab" href="#pulse" role="tab" aria-controls="pulse" aria-selected="false">
                    <i class="fas fa-bolt mr-2"></i>Pulse Surveys
                  </a>
                </li>
                <?php if ($showHrFeedbackTab): ?>
                <li class="nav-item">
                  <a class="nav-link" id="hr-feedback-tab" data-toggle="tab" href="#hr-feedback" role="tab" aria-controls="hr-feedback" aria-selected="false">
                    <i class="fas fa-user-tie mr-2"></i>HR Feedback
                  </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                  <a class="nav-link" id="suggestions-tab" data-toggle="tab" href="#suggestions" role="tab" aria-controls="suggestions" aria-selected="false">
                    <i class="fas fa-lightbulb mr-2"></i>Suggestions & Ideas
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body survey-tabs-body">
              <div class="tab-content" id="survey-tab-content">

                  

                <!-- Employee Satisfaction Surveys Tab -->
                <div class="tab-pane fade show active" id="satisfaction" role="tabpanel" aria-labelledby="satisfaction-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-secondary card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Create Employee Satisfaction Survey</h3>
                          <div class="card-tools">

                          </div>
                        </div>
                        <div class="card-body">
                          <form method="post" class="survey-form">
                            <input type="hidden" name="survey_type" value="satisfaction">
                            <div class="form-group">
                              <label for="survey-title">Survey Title</label>
                              <input id="survey-title" type="text" name="title" class="form-control" placeholder="Enter survey title" required>
                            </div>
                            <div class="form-group">
                              <label for="survey-description">Description (Optional)</label>
                              <textarea id="survey-description" name="description" class="form-control" rows="2" placeholder="Brief description of the survey purpose"></textarea>
                            </div>
                            <div class="form-group">
                              <label for="survey-questions">Questions (one per line)</label>
                              <textarea id="survey-questions" name="questions_raw" class="form-control" rows="5" placeholder="How satisfied are you with your work environment?
How would you rate your work-life balance?
What improvements would you suggest?" required></textarea>
                              <small class="form-text text-muted">Enter each question on a new line</small>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="is-anonymous" name="is_anonymous" value="1">
                              <label class="form-check-label" for="is-anonymous">
                                Allow anonymous responses
                              </label>
                            </div>
                            <button class="btn btn-success" type="submit">Create Satisfaction Survey</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="card card-info card-outline">
                        <div class="card-header"><h3 class="card-title">Available Satisfaction Surveys</h3></div>
                        <div class="card-body">
                          <?php
                          $satisfactionSurveys = array_filter($payload['surveys'] ?? [], function($survey) {
                            return ($survey['survey_type'] ?? 'satisfaction') === 'satisfaction';
                          });
                          ?>
                          <?php if (!empty($satisfactionSurveys)): ?>
                            <div class="list-group">
                              <?php foreach ($satisfactionSurveys as $survey): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                  <div>
                                    <strong><?=htmlspecialchars($survey['title'])?></strong><br>
                                    <small class="text-muted">
                                      Created: <?=htmlspecialchars($survey['created_at'] ?? 'N/A')?> |
                                      Anonymous: <?=($survey['is_anonymous'] ?? 0) ? 'Yes' : 'No'?>
                                    </small>
                                  </div>
                                  <div class="btn-group" role="group">
                                    <a class="btn btn-sm btn-info" href="survey_view.php?module=survey&action=view&id=<?=$survey['eer_survey_id']?>">View</a>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <p class="text-muted">No satisfaction surveys yet.</p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Pulse Surveys Tab -->
                <div class="tab-pane fade" id="pulse" role="tabpanel" aria-labelledby="pulse-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-warning card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Pulse Surveys</h3>
                          <div class="card-tools">

                          </div>
                        </div>
                        <div class="card-body">
                          <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Pulse Surveys</strong> are short, frequent surveys designed to quickly gauge employee sentiment on specific topics.
                          </div>

                          <form method="post" class="pulse-survey-form">
                            <input type="hidden" name="survey_type" value="pulse">
                            <div class="form-group">
                              <label for="pulse-title">Pulse Survey Title</label>
                              <input id="pulse-title" type="text" name="title" class="form-control" placeholder="e.g., How are you feeling about the new policy?" required>
                            </div>
                            <div class="form-group">
                              <label for="pulse-question">Single Question</label>
                              <input id="pulse-question" type="text" name="questions_raw" class="form-control" placeholder="On a scale of 1-5, how satisfied are you with...?" required>
                              <small class="form-text text-muted">Pulse surveys should have only one focused question</small>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="pulse-anonymous" name="is_anonymous" value="1" checked>
                              <label class="form-check-label" for="pulse-anonymous">
                                Allow anonymous responses (recommended for pulse surveys)
                              </label>
                            </div>
                            <button class="btn btn-warning" type="submit">Create Pulse Survey</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12">
                      <div class="card card-light card-outline">
                        <div class="card-header"><h3 class="card-title">Active Pulse Surveys</h3></div>
                        <div class="card-body">
                          <?php
                          $pulseSurveys = array_filter($payload['surveys'] ?? [], function($survey) {
                            return ($survey['survey_type'] ?? '') === 'pulse';
                          });
                          ?>
                          <?php if (!empty($pulseSurveys)): ?>
                            <div class="row">
                              <?php foreach ($pulseSurveys as $survey): ?>
                                <div class="col-md-6 mb-3">
                                  <div class="card border-warning">
                                    <div class="card-body">
                                      <h6 class="card-title"><?=htmlspecialchars($survey['title'])?></h6>
                                      <p class="card-text small text-muted">
                                        Created: <?=htmlspecialchars($survey['created_at'] ?? 'N/A')?><br>
                                        Anonymous: <?=($survey['is_anonymous'] ?? 0) ? 'Yes' : 'No'?>
                                      </p>
                                      <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-info" href="survey_view.php?module=survey&action=view&id=<?=$survey['eer_survey_id']?>">Take Survey</a>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <div class="text-center text-muted py-4">
                              <i class="fas fa-bolt fa-3x mb-3 text-warning"></i>
                              <p>No active pulse surveys</p>
                              <small>Create your first pulse survey to get quick feedback from employees</small>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- HR Feedback Tab -->
               <div class="tab-pane fade" id="hr-feedback" role="tabpanel" aria-labelledby="hr-feedback-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-primary card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Provide Feedback to Employee</h3>
                        </div>
                        <div class="card-body">
                          <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>HR Feedback:</strong> This section is for the HR team to give feedback directly to selected employees.
                          </div>

                          <form method="post" class="hr-feedback-form">
                            <input type="hidden" name="action" value="hr_feedback">
                            <div class="form-group">
                              <label for="feedback-employee">Select Employee</label>
                              <select id="feedback-employee" name="employee_id" class="form-control" required>
                                <option value="">Select an employee</option>
                                <?php
                                use App\Controllers\EmployeeController;
                                $employeeCtrl = new EmployeeController();
                                $employees = $employeeCtrl->index();
                                foreach ($employees as $employee): ?>
                                  <option value="<?= $employee['employee_id'] ?>"><?= htmlspecialchars($employee['full_name'] ?? $employee['name'] ?? 'Unknown') ?> (<?= htmlspecialchars($employee['department'] ?? 'N/A') ?>)</option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="form-group">
                              <label for="feedback-category">Category</label>
                              <select id="feedback-category" name="category" class="form-control" required>
                                <option value="">Select a category</option>
                                <option value="performance">Performance</option>
                                <option value="behavior">Behavior</option>
                                <option value="skills">Skills Development</option>
                                <option value="teamwork">Teamwork</option>
                                <option value="leadership">Leadership</option>
                                <option value="other">Other</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label for="feedback-rating">Overall Rating</label>
                              <select id="feedback-rating" name="rating" class="form-control" required>
                                <option value="">Select rating</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Very Good</option>
                                <option value="3">⭐⭐⭐ Good</option>
                                <option value="2">⭐⭐ Fair</option>
                                <option value="1">⭐ Poor</option>
                              </select>
                            </div>
                            <div class="form-group">
                              <label for="hr-feedback">Feedback Comments</label>
                              <textarea id="hr-feedback" name="comments" class="form-control" rows="5" placeholder="Provide detailed feedback to help the employee improve." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                              <i class="fas fa-paper-plane mr-2"></i>Submit Feedback
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <!-- Suggestions & Ideas Tab -->
                <div class="tab-pane fade" id="suggestions" role="tabpanel" aria-labelledby="suggestions-tab">
                  <div class="row">
                    <div class="col-12">
                      <div class="card card-primary card-outline">
                        <div class="card-header">
                          <h3 class="card-title"><i class="fas fa-lightbulb mr-2"></i>Suggestions & Improvement Ideas</h3>
                        </div>
                        <div class="card-body">
                          <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Improvement Ideas:</strong> This section collects suggestions for workplace improvements from anonymous feedback and survey responses.
                          </div>

                          <div class="row">
                            <div class="col-md-8">
                              <div class="card mb-4">
                                <div class="card-header">
                                  <h6 class="card-title mb-0">Submit a Suggestion</h6>
                                </div>
                                <div class="card-body">
                                  <form method="post" class="suggestion-form">
                                    <input type="hidden" name="action" value="submit_suggestion">
                                    <div class="form-group">
                                      <label for="suggestion-comment">Suggestion or Idea</label>
                                      <textarea id="suggestion-comment" name="comment" class="form-control" rows="4" placeholder="Share an improvement idea, recommendation, or feedback." required></textarea>
                                    </div>
                                    <div class="form-row">
                                      <div class="form-group col-md-6">
                                        <label for="suggestion-category">Category</label>
                                        <select id="suggestion-category" name="category" class="form-control">
                                          <option value="work_environment">Work Environment</option>
                                          <option value="management">Management</option>
                                          <option value="policies">Company Policies</option>
                                          <option value="colleagues">Colleague Relations</option>
                                          <option value="compensation">Compensation & Benefits</option>
                                          <option value="work_life_balance">Work-Life Balance</option>
                                          <option value="other">Other</option>
                                        </select>
                                      </div>
                                      <div class="form-group col-md-3">
                                        <label for="suggestion-rating">Rating</label>
                                        <select id="suggestion-rating" name="rating" class="form-control">
                                          <option value="">Optional rating</option>
                                          <option value="5">5 ⭐ Excellent</option>
                                          <option value="4">4 ⭐ Very good</option>
                                          <option value="3">3 ⭐ Good</option>
                                          <option value="2">2 ⭐ Fair</option>
                                          <option value="1">1 ⭐ Poor</option>
                                        </select>
                                      </div>
                                      <div class="form-group col-md-3 d-flex align-items-end">
                                        <div class="form-check mb-0">
                                          <input class="form-check-input" type="checkbox" id="suggestion-anonymous" name="is_anonymous" value="1">
                                          <label class="form-check-label" for="suggestion-anonymous">Submit anonymously</label>
                                        </div>
                                      </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Suggestion</button>
                                  </form>
                                </div>
                              </div>

                              <h5>Recent Suggestions</h5>
                              <div class="mb-3">
                                <div class="d-flex flex-wrap align-items-center">
                                  <div class="btn-group mr-3 mb-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary sort-btn" data-sort="newest">Newest First</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary sort-btn" data-sort="highest">Highest Rated</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary sort-btn" data-sort="lowest">Lowest Rated</button>
                                  </div>
                                  <div class="form-group mb-0 flex-fill" style="min-width:240px; max-width:320px;">
                                    <label for="category-filter" class="sr-only">Filter by category</label>
                                    <select id="category-filter" class="form-control form-control-sm">
                                      <option value="">All Categories</option>
                                      <option value="work_environment">Work Environment</option>
                                      <option value="management">Management</option>
                                      <option value="policies">Company Policies</option>
                                      <option value="colleagues">Colleague Relations</option>
                                      <option value="compensation">Compensation & Benefits</option>
                                      <option value="work_life_balance">Work-Life Balance</option>
                                      <option value="other">Other</option>
                                    </select>
                                  </div>
                                </div>
                              </div>

                              <?php
                              $suggestions = array_filter($payload['feedback'] ?? [], function($feedback) {
                                $comment = strtolower($feedback['comment'] ?? '');
                                $category = strtolower($feedback['category'] ?? '');
                                $evaluatorType = strtolower($feedback['evaluator_type'] ?? '');

                                $suggestionCategories = [
                                  'work_environment',
                                  'management',
                                  'policies',
                                  'colleagues',
                                  'compensation',
                                  'work_life_balance',
                                  'other'
                                ];

                                return in_array($category, $suggestionCategories, true)
                                    || $evaluatorType === 'suggestion'
                                    || strpos($comment, 'suggest') !== false
                                    || strpos($comment, 'improve') !== false
                                    || strpos($comment, 'better') !== false
                                    || strpos($comment, 'recommend') !== false;
                              });
                              ?>

                              <?php if (!empty($suggestions)): ?>
                                <div class="suggestions-container">
                                  <?php foreach (array_slice($suggestions, 0, 15) as $suggestion): ?>
                                    <div class="card mb-3 suggestion-item" data-category="<?php echo htmlspecialchars($suggestion['category'] ?? 'other'); ?>" data-rating="<?php echo ($suggestion['rating'] ?? 0); ?>">
                                      <div class="card-body pb-2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                          <span class="badge badge-pill badge-success"><?php echo ucfirst(str_replace('_', ' ', $suggestion['category'] ?? 'general')); ?></span>
                                          <div class="text-right">
                                            <small class="text-muted">Rating: <?php echo ($suggestion['rating'] ?? 'N/A'); ?> ⭐</small>
                                          </div>
                                        </div>
                                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($suggestion['comment'] ?? '')); ?></p>
                                        <small class="text-muted">
                                          <i class="fas fa-user-secret mr-1"></i><?php echo ($suggestion['is_anonymous'] ?? 0) ? 'Anonymous Submission' : 'From: ' . htmlspecialchars($suggestion['employee_name'] ?? 'Unknown'); ?>
                                          | <i class="fas fa-calendar mr-1"></i><?php echo htmlspecialchars($suggestion['evaluation_date'] ?? 'Recent'); ?>
                                        </small>
                                      </div>
                                    </div>
                                  <?php endforeach; ?>
                                </div>
                                <?php if (count($suggestions) > 15): ?>
                                  <button class="btn btn-sm btn-outline-primary" id="load-more-suggestions">Load More Suggestions</button>
                                <?php endif; ?>
                              <?php else: ?>
                                <div class="text-center text-muted py-4">
                                  <i class="fas fa-lightbulb fa-3x mb-3 text-warning"></i>
                                  <p>No suggestions collected yet</p>
                                  <small>Suggestions will appear here as employees submit feedback with improvement ideas</small>
                                </div>
                              <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                              <h5>Suggestion Analytics</h5>
                              <div class="card mb-3">
                                <div class="card-header">
                                  <h6 class="card-title mb-0">By Category</h6>
                                </div>
                                <div class="card-body">
                                  <?php
                                  $categoryCount = [];
                                  foreach ($suggestions as $suggestion) {
                                    $category = $suggestion['category'] ?? 'other';
                                    $categoryCount[$category] = ($categoryCount[$category] ?? 0) + 1;
                                  }
                                  arsort($categoryCount);
                                  ?>

                                  <?php if (!empty($categoryCount)): ?>
                                    <?php foreach (array_slice($categoryCount, 0, 7) as $category => $count): ?>
                                      <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><?php echo ucfirst(str_replace('_', ' ', $category)); ?></span>
                                        <span class="badge badge-primary"><?php echo $count; ?></span>
                                      </div>
                                    <?php endforeach; ?>
                                  <?php else: ?>
                                    <p class="text-muted small">No categories yet</p>
                                  <?php endif; ?>
                                </div>
                              </div>

                              <div class="card">
                                <div class="card-header">
                                  <h6 class="card-title mb-0">Quality Insights</h6>
                                </div>
                                <div class="card-body small">
                                  <?php
                                  $totalSuggestions = count($suggestions);
                                  $highQualitySuggestions = count(array_filter($suggestions, function($s) {
                                    return ($s['rating'] ?? 0) >= 4;
                                  }));
                                  $suggestionQuality = $totalSuggestions > 0 ? ($highQualitySuggestions / $totalSuggestions) * 100 : 0;
                                  $avgSuggestionRating = !empty($suggestions) ? array_sum(array_column($suggestions, 'rating')) / count($suggestions) : 0;
                                  ?>
                                  <div class="mb-2">
                                    <strong>Total Suggestions:</strong><br>
                                    <span class="text-primary"><?php echo $totalSuggestions; ?></span>
                                  </div>
                                  <div class="mb-2">
                                    <strong>Avg Quality Rating:</strong><br>
                                    <span class="text-info"><?php echo number_format($avgSuggestionRating, 1); ?> ⭐</span>
                                  </div>
                                  <div>
                                    <strong>Quality Suggestions:</strong><br>
                                    <span class="text-success"><?php echo $highQualitySuggestions; ?> (<?php echo number_format($suggestionQuality, 0); ?>%)</span>
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
          </div>
        </div>
      </div>

    </div>
                    <script>
                  // Sorting functionality
                  document.querySelectorAll('.sort-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                      const sortType = this.dataset.sort;
                      const suggestions = Array.from(document.querySelectorAll('.suggestion-item'));
                      
                      suggestions.sort((a, b) => {
                        switch(sortType) {
                          case 'newest':
                            return 0; // Keep original order
                          case 'highest':
                            return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
                          case 'lowest':
                            return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
                          default: return 0;
                        }
                      });
                      
                      const container = document.querySelector('.suggestions-container');
                      if (container) {
                        suggestions.forEach(s => container.appendChild(s));
                      }
                      
                      document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                      this.classList.add('active');
                    });
                  });

                  // Category filter functionality
                  const categoryFilter = document.getElementById('category-filter');
                  if (categoryFilter) {
                    categoryFilter.addEventListener('change', function() {
                      const filterValue = this.value;
                      document.querySelectorAll('.suggestion-item').forEach(item => {
                        if (!filterValue || item.dataset.category === filterValue) {
                          item.style.display = 'block';
                        } else {
                          item.style.display = 'none';
                        }
                      });
                    });
                  }
                </script>
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
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script src="../../assets/build/js/Layout.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>
  <!-- PAGE PLUGINS (optional, keep if needed) -->
  <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>
  <!-- Custom Survey JS -->

  <script>
    // Auto-switch to Analytics tab when viewing survey results
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('action') === 'view_results') {
        // Click the analytics tab to show survey results
        const analyticsTab = document.getElementById('analytics-tab');
        if (analyticsTab) {
          analyticsTab.click();
          // Optionally scroll to the analytics section
          setTimeout(function() {
            const analyticsSection = document.getElementById('analytics');
            if (analyticsSection) {
              analyticsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }, 100);
        }
      }
    });
  </script>

  <!-- Create Satisfaction Survey Modal -->
  
  <script>
    // Hide preloader when page is fully loaded (AdminLTE style)
    window.addEventListener('load', function() {
      var preloader = document.querySelector('.preloader');
      if (preloader) {
        preloader.style.display = 'none';
      }
    });
  </script>
</body>

</html>

