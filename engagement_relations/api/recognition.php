<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\RecognitionController;

session_start();
$action = $_GET['action'] ?? 'list';
if (!isset($_SESSION['user']) && $action !== 'list') {
   jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new RecognitionController();
$action = $_GET['action'] ?? 'list';
$data = inputData();

try {
    switch ($action) {
        case 'list':
            $data = $ctrl->getRecognitions();
            jsonResponse(['success' => true, 'data' => $data]);
            break;
        case 'send':
            foreach (['receiver_id', 'message', 'points'] as $f) {
                if (!isset($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $senderEmployeeId = $_SESSION['user']['employee_id'] ?? null;
            if (!$senderEmployeeId) {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->sendRecognition($senderEmployeeId, $data['receiver_id'], $data['message'], (int)$data['points']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'history':
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            if (!$employeeId) jsonResponse(['error' => 'Unauthorized'], 401);
            jsonResponse(['success' => true, 'data' => $ctrl->getRecognitionHistory($employeeId)]);
            break;

        case 'rewards':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $data['action'] ?? null;
                $result = $ctrl->manageRewardsCatalog($action, $data);
                jsonResponse(['success' => true, 'data' => $result]);
            }
            break;

        case 'assign_badge':
            if (!isset($data['employee_id'], $data['badge_id'])) jsonResponse(['error' => 'employee_id and badge_id are required'], 400);
            $result = $ctrl->assignAchievementBadge($data['employee_id'], $data['badge_id']);
            jsonResponse(['success' => true, 'data' => $result]);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
