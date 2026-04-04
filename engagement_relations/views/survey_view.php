<?php
session_start();
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../../auth/auth_check.php';

require_once __DIR__ . '/../autoload.php';

use App\Controllers\SurveyController;

$theme = $_SESSION['user']['theme'] ?? 'light';

function getSurveyById($id) {
    $surveyCtrl = new SurveyController();
    return $surveyCtrl->show($id);
}

function submitSurveyResponses($surveyId, $employeeId, $answers) {
    $surveyCtrl = new SurveyController();
    return $surveyCtrl->submit($surveyId, $employeeId, $answers);
}

$surveyCtrl = new SurveyController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Survey submission disabled on this view page. Redirect back to the same survey page.
    $surveyId = (int) ($_POST['survey_id'] ?? 0);
    $_SESSION['flash_error'] = 'Survey response submission is disabled on this page.';
    $redirectUrl = 'survey_view.php?module=survey&action=view&id=' . $surveyId;
    header('Location: ' . $redirectUrl);
    exit;
}

$requestAction = $_GET['action'] ?? '';
$surveyId = (int) ($_GET['id'] ?? 0);
$payload = ['survey' => null];
$surveyResponses = [];
$isAdmin = (strtolower($_SESSION['user']['role'] ?? '') === 'admin');

if ($surveyId > 0 && $requestAction === 'view') {
    $payload['survey'] = $surveyCtrl->show($surveyId);
    if ($payload['survey']) {
        $surveyResponses = $surveyCtrl->getSurveyResults($surveyId);
    }
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
    <title>Survey: <?=htmlspecialchars($payload['survey']['title'] ?? 'Not found')?></title>
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../../layout/toast.css" />
    <link rel="stylesheet" href="css/survey_view.css" />
</head>
<body class="hold-transition layout-top-nav <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h3>Survey Response</h3>
                            <p>Please fill out the survey questions below and submit your responses. Your feedback is valuable to us!</p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="card survey-view-module">
                                <div class="card-header">
                                    <h2 class="card-title">Survey: <?=htmlspecialchars($payload['survey']['title'] ?? 'Not found')?></h2>
                                    <a href="survey.php" class="btn btn-sm btn-secondary float-right">&laquo; Back to Surveys</a>
                                </div>
                                <div class="card-body">

    <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?=htmlspecialchars($flashSuccess)?></div>
    <?php endif; ?>
    <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($flashError)?></div>
    <?php endif; ?>

    <?php if (empty($payload['survey'])): ?>
        <div class="alert alert-warning">Survey not found. Please go back to the list and try again.</div>
    <?php else: ?>
        <div class="alert alert-info">Survey response submission is disabled on this page. Showing responses only.</div>

        <?php if (!empty($surveyResponses)): ?>
            <div class="mt-4">
                <h4>Survey Responses (<?=count($surveyResponses)?>)</h4>
                <?php foreach ($surveyResponses as $response): ?>
                    <div class="response-item">
                        <p><strong>Response ID:</strong> <?= isset($response['id']) ? htmlspecialchars($response['id']) : 'N/A' ?></p>
                        <p><strong>Submitted By:</strong> <?= htmlspecialchars($response['employee_id']) ?></p>
                        <p><strong>Answers:</strong></p>
                        <ul>
                            <?php foreach (json_decode($response['answers'], true) as $question => $answer): ?>
                                <li><strong>Question <?= htmlspecialchars($question) ?>:</strong> <?= htmlspecialchars($answer) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No responses found for this survey.</div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    </div>
    </div>
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="../../assets/dist/js/adminlte.min.js"></script>
    <script src="js/survey_view.js"></script>
</body>
</html>
