<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\CommunicationController;

session_start();
if (!isset($_SESSION['user'])) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new CommunicationController();
$action = $_GET['action'] ?? 'announcements';
$data = inputData();

// For messages action, employee_id should come from GET params
if ($action === 'messages' && empty($data['employee_id'])) {
    $data['employee_id'] = $_GET['employee_id'] ?? null;
}

try {
    switch ($action) {
        case 'announcements':
            jsonResponse($ctrl->getAnnouncements());
            break;
        case 'post':
            foreach (['title','content'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $employeeId = (int)($_SESSION['user']['id'] ?? 0);
            if (!$employeeId) {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->postAnnouncement($data['title'], $data['content'], $employeeId);
            jsonResponse(['id' => $id], 201);
            break;
        case 'messages':
            $empId = (int)($data['employee_id'] ?? 0);
            if ($empId <= 0) {
                jsonResponse(['error' => 'employee_id required and must be positive'], 400);
            }
            $messages = $ctrl->messageThreads($empId);
            jsonResponse($messages);
            break;
        case 'send-message':
            foreach (['receiver_id','message'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $employeeId = (int)($_SESSION['user']['id'] ?? 0);
            if (!$employeeId) {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->sendMessage($employeeId, (int)$data['receiver_id'], $data['message']);
            jsonResponse(['id' => $id], 201);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
