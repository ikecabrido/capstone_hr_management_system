<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\SurveyController;

session_start();
if (!isset($_SESSION['user'])) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new SurveyController();
$action = $_GET['action'] ?? 'list';
$data = inputData();

try {
    switch ($action) {
        case 'list':
            jsonResponse($ctrl->index());
            break;
        case 'view':
            if (empty($data['id'])) jsonResponse(['error' => 'id is required'], 400);
            jsonResponse($ctrl->show((int)$data['id']));
            break;
        case 'create':
            if (empty($data['title'])) jsonResponse(['error' => 'title is required'], 400);
            $questions = [];
            if (!empty($data['questions'])) {
                $questions = $data['questions'];
            }
            $id = $ctrl->store($data['title'], $_SESSION['user']['id'], $questions);
            jsonResponse(['id' => $id], 201);
            break;
        case 'submit':
            if (empty($data['survey_id']) || empty($data['answers'])) {
                jsonResponse(['error' => 'survey_id and answers are required'], 400);
            }
            $id = $ctrl->submit((int)$data['survey_id'], $_SESSION['user']['id'], $data['answers']);
            jsonResponse(['id' => $id], 201);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
