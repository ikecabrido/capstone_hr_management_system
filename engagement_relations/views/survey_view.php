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
    $surveyId = (int) ($_POST['survey_id'] ?? 0);
    $answers = $_POST['answers'] ?? [];
    $employeeId = (int) ($_SESSION['user']['employee_id'] ?? $_SESSION['user']['id'] ?? 0);

    if ($surveyId <= 0 || $employeeId <= 0) {
        $_SESSION['flash_error'] = 'Unable to submit survey. Missing survey or user details.';
    } elseif (!is_array($answers) || empty($answers)) {
        $_SESSION['flash_error'] = 'Please answer all survey questions before submitting.';
    } else {
        $cleanAnswers = [];
        foreach ($answers as $questionId => $answer) {
            $answerText = trim((string)$answer);
            if ($answerText === '') {
                $cleanAnswers = null;
                break;
            }
            $cleanAnswers[$questionId] = $answerText;
        }

        if ($cleanAnswers === null) {
            $_SESSION['flash_error'] = 'All questions are required. Please complete the form.';
        } else {
            try {
                submitSurveyResponses($surveyId, $employeeId, $cleanAnswers);
                $_SESSION['flash_success'] = 'Thank you! Survey submitted successfully.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Error submitting survey: ' . $e->getMessage();
            }
        }
    }

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
        $surveyResponses = $surveyCtrl->getResponses($surveyId);
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
                            <h1>Survey Answer</h1>
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
        <form method="post" action="">
            <input type="hidden" name="survey_id" value="<?=htmlspecialchars($payload['survey']['id'])?>">
            <?php foreach ($payload['survey']['questions'] as $q): ?>
                <div class="form-group">
                    <label for="answer-<?=$q['id']?>"><?=htmlspecialchars($q['question_text'])?></label>
                    <?php $type = strtolower(trim($q['type'] ?? 'text')); ?>
                    <?php $defaultValue = $q['default'] ?? ''; ?>
                    <?php if ($isAdmin): ?>
                        <?php if ($type === 'textarea'): ?>
                            <textarea id="answer-<?=$q['id']?>" class="form-control" rows="4" readonly><?=htmlspecialchars($defaultValue)?></textarea>
                        <?php elseif ($type === 'rating'): ?>
                            <input type="text" class="form-control" readonly value="<?=htmlspecialchars($defaultValue ?: 'N/A')?>">
                        <?php else: ?>
                            <input type="text" class="form-control" readonly value="<?=htmlspecialchars($defaultValue)?>">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($type === 'textarea'): ?>
                            <textarea id="answer-<?=$q['id']?>" name="answers[<?=$q['id']?>]" class="form-control" rows="4" placeholder="Enter your response" required><?=htmlspecialchars($defaultValue)?></textarea>
                        <?php elseif ($type === 'rating'): ?>
                            <select id="answer-<?=$q['id']?>" name="answers[<?=$q['id']?>]" class="form-control" required>
                                <option value="">Choose a rating</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?=$i?>" <?=($i == ($defaultValue ?: 3)) ? 'selected' : ''?>><?=$i?> / 5</option>
                                <?php endfor; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" id="answer-<?=$q['id']?>" name="answers[<?=$q['id']?>]" class="form-control" value="<?=htmlspecialchars($defaultValue)?>" placeholder="Type your answer" required>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (!$isAdmin): ?>
                <div class="form-group mt-4 d-flex justify-content-between align-items-center">
                    <a href="survey.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Responses</button>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Admin view:</strong> questions are read-only. This page shows submitted responses below.
                </div>
            <?php endif; ?>
        </form>

        <?php if (!empty($surveyResponses)): ?>
            <div class="mt-4">
                <h4>Survey Responses (<?=count($surveyResponses)?>)</h4>
                <?php foreach ($surveyResponses as $response): ?>
                    <?php $answers = json_decode($response['answers'], true); ?>
                    <div class="survey-response-card">
                        <div class="card-title">
                            Respondent: <?=htmlspecialchars($response['employee_id'])?>
                            <span class="text-muted" style="font-size:0.85rem;">&#8226;</span>
                            <small><?=htmlspecialchars($response['submitted_at'])?></small>
                        </div>
                        <div class="card-body">
                            <?php if (empty($answers)): ?>
                                <p class="text-muted">No answers recorded.</p>
                            <?php else: ?>
                                <?php foreach ($payload['survey']['questions'] as $q): ?>
                                    <div class="response-item">
                                        <strong><?=htmlspecialchars($q['question_text'])?></strong>
                                        <small><?=nl2br(htmlspecialchars($answers[$q['id']] ?? '(not answered)'))?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
