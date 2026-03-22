<?php
session_start();
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../autoload.php';

use App\Controllers\SurveyController;
use App\Controllers\FeedbackController;
use App\Models\Employee;

$surveyCtrl = new SurveyController();
$feedbackCtrl = new FeedbackController();
$employeeModel = new Employee();

$surveys = $surveyCtrl->index();
$feedbacks = $feedbackCtrl->index();
$employees = $employeeModel->all();
$employeeId = (int) ($_SESSION['user']['id'] ?? 0);
$theme = $_SESSION['user']['theme'] ?? 'light';
$userName = htmlspecialchars($_SESSION['user']['name'] ?? 'Employee');
$currentPage = basename($_SERVER['PHP_SELF']);

function navItem($page, $icon, $label, $currentPage) {
    $active = $currentPage === $page ? 'active' : '';
    return "<li class=\"nav-item\"><a href=\"{$page}\" class=\"nav-link {$active}\"><i class=\"nav-icon fas fa-{$icon}\"></i><p>{$label}</p></a></li>";
}

$flashSuccess = null;
$flashError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'survey') {
        $flashError = 'Creating surveys is disabled on this page.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'feedback') {
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $targetEmployeeId = $employeeId > 0 ? $employeeId : (int)($_POST['employee_id'] ?? 0);

        if ($surveyId <= 0 || $comment === '') {
            $flashError = 'Feedback comment is required.';
        } else {
            $feedbackCtrl->store($surveyId, $targetEmployeeId, $comment);
            $flashSuccess = 'Feedback submitted.';
            $feedbacks = $feedbackCtrl->index();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'answer_survey') {
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $answers = [];
        
        // Collect all question answers (form fields named answer_QID)
        if (isset($_POST['answers']) && is_array($_POST['answers'])) {
            $answers = $_POST['answers'];
        }
        
        if ($surveyId <= 0 || empty($answers)) {
            $flashError = 'Please answer all survey questions.';
        } else {
            $surveyCtrl->submit($surveyId, $employeeId, $answers);
            $flashSuccess = 'Survey submitted successfully!';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Portal - Survey</title>
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li><li class="nav-item d-none d-sm-inline-block"><a href="index.php" class="nav-link">Dashboard</a></li></ul>
      <ul class="navbar-nav ml-auto"><li class="nav-item"><div class="nav-link" id="clock">--:--:--</div></li><li class="nav-item"><a class="nav-link" data-widget="fullscreen" href="#" role="button"><i class="fas fa-expand-arrows-alt"></i></a></li><li class="nav-item"><a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode"><i class="fas fa-moon" id="themeIcon"></i></a></li></ul>
    </nav>
    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="index.php" class="brand-link">
        <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: .8" />
        <span class="brand-text font-weight-light">BCP Employee</span>
      </a>
      <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info">
            <a href="#" class="d-block"><?= $userName ?></a>
          </div>
        </div>
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php echo navItem('index.php', 'bullhorn', 'Announcements', $currentPage); ?>
            <?php echo navItem('survey.php', 'poll', 'Survey', $currentPage); ?>
            <?php echo navItem('social.php', 'users', 'Social', $currentPage); ?>
            <?php echo navItem('grievance.php', 'exclamation-triangle', 'Grievances', $currentPage); ?>
            <?php echo navItem('../../logout.php', 'sign-out-alt', 'Logout', $currentPage); ?>
          </ul>
      </div>
    </aside>
    <div class="content-wrapper">
      <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Survey & Feedback</h1></div></div></div></div>
      <section class="content"><div class="container-fluid">
        <?php if ($flashSuccess): ?><div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
        <div class="row">
            <div class="col-md-6">
            <div class="card card-success card-outline"><div class="card-header"><h3 class="card-title">Give Survey Feedback</h3></div><div class="card-body">
              <form method="post"><input type="hidden" name="action" value="feedback" />
                <div class="form-group"><label>Survey</label><select name="survey_id" class="form-control" required><option value="">Choose</option><?php foreach ($surveys as $survey): ?><option value="<?= (int)$survey['eer_survey_id'] ?>"><?= htmlspecialchars($survey['title']) ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Comment</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
                <?php if (!$employeeId): ?><div class="form-group"><label>Employee</label><select name="employee_id" class="form-control" required><option value="">Choose</option><?php foreach ($employees as $emp): ?><option value="<?= (int)$emp['eer_employee_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option><?php endforeach; ?></select></div><?php endif; ?>
                <button type="submit" class="btn btn-success">Submit Feedback</button>
              </form>
            </div></div>
          </div>
        </div>
        <div class="card card-info card-outline"><div class="card-header"><h3 class="card-title">Open Surveys</h3></div><div class="card-body">
          <?php if (!empty($surveys)): ?>
            <ul class="list-group">
              <?php foreach ($surveys as $survey): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong><?= htmlspecialchars($survey['title'] ?? '(No title)') ?></strong><br>
                    <small>By <?= htmlspecialchars($survey['employee_name'] ?? 'Unknown') ?> at <?= htmlspecialchars($survey['created_at'] ?? $survey['date_created'] ?? '') ?></small>
                  </div>
                  <button type="button" class="btn btn-sm btn-primary answer-survey-btn" data-survey-id="<?= (int)$survey['eer_survey_id'] ?>" data-survey-title="<?= htmlspecialchars($survey['title']) ?>">
                    <i class="fas fa-edit"></i> Answer
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No surveys yet.</p>
          <?php endif; ?>
        </div></div>
        <div class="card card-secondary card-outline"><div class="card-header"><h3 class="card-title">Feedback Log</h3></div><div class="card-body">
          <?php if (!empty($feedbacks)): ?>
            <ul class="list-group">
              <?php foreach ($feedbacks as $fb): ?>
                <li class="list-group-item">
                  <strong><?= htmlspecialchars($fb['employee_name'] ?? 'Unknown') ?></strong> on <em><?= htmlspecialchars($fb['survey_title'] ?? 'Survey') ?></em>: <?= htmlspecialchars($fb['comment']) ?><br>
                  <small><?= htmlspecialchars($fb['created_at']) ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No feedback yet.</p>
          <?php endif; ?>
        </div></div>
      </div></section>
    </div>
    <?php include "../../layout/global_modal.php"; ?>
    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  
  <!-- Answer Survey Modal -->
  <div class="modal fade" id="answerSurveyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Answer Survey: <span id="surveyTitle"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post" id="surveyAnswerForm">
          <input type="hidden" name="action" value="answer_survey">
          <input type="hidden" name="survey_id" id="modalSurveyId">
          <div class="modal-body">
            <div id="surveyQuestionsContainer"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Survey</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  document.querySelectorAll('.answer-survey-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const surveyId = this.dataset.surveyId;
      const surveyTitle = this.dataset.surveyTitle;
      
      // Correct API path from employee_portal to api
      const apiUrl = '../api/survey.php?action=view&id=' + surveyId;
      
      console.log('Fetching survey from:', apiUrl);
      
      fetch(apiUrl)
        .then(r => {
          console.log('Response status:', r.status);
          return r.json();
        })
        .then(data => {
          console.log('Survey data received:', data);
          
          if (data.error) {
            alert('Error: ' + data.error);
            return;
          }
          
          // Populate modal
          document.getElementById('surveyTitle').textContent = surveyTitle;
          document.getElementById('modalSurveyId').value = surveyId;
          
          let html = '';
          if (data.questions && data.questions.length > 0) {
            console.log('Found ' + data.questions.length + ' questions');
            
            data.questions.forEach((q, i) => {
              console.log('Adding question:', q.question_text, 'Type:', q.type);
              html += '<div class="form-group">';
              html += '<label><strong>' + (i+1) + '. ' + (q.question_text || 'Question') + '</strong></label>';
              
              if (q.type === 'text') {
                html += '<textarea name="answers[' + q.id + ']" class="form-control" rows="2" required placeholder="Your answer here..."></textarea>';
              } else if (q.type === 'rating') {
                html += '<select name="answers[' + q.id + ']" class="form-control" required>';
                html += '<option value="">-- Select Rating --</option>';
                for (let r = 1; r <= 5; r++) {
                  html += '<option value="' + r + '">' + r + ' ⭐' + '</option>';
                }
                html += '</select>';
              } else {
                html += '<input type="text" name="answers[' + q.id + ']" class="form-control" placeholder="Your answer here..." required>';
              }
              
              html += '</div>';
            });
          } else {
            console.log('No questions found');
            html = '<p class="alert alert-warning">This survey has no questions yet.</p>';
          }
          
          document.getElementById('surveyQuestionsContainer').innerHTML = html;
          
          // Show modal
          const answerModal = document.getElementById('answerSurveyModal');
          const modal = new (window.bootstrap?.Modal || window.Modal)(answerModal);
          modal.show();
        })
        .catch(err => {
          console.error('Fetch error:', err);
          alert('Error loading survey: ' + err.message);
        });
    });
  });
  </script>
</body>
</html>
