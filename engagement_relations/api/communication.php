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

try {
    switch ($action) {
        case 'announcements':
            jsonResponse($ctrl->getAnnouncements());
            break;
        case 'post':
            foreach (['title','content'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $employeeId = (int)($_SESSION['user']['employee_id'] ?? 0);
            if (!$employeeId) {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->postAnnouncement($data['title'], $data['content'], $employeeId);
            jsonResponse(['id' => $id], 201);
            break;
        case 'messages':
            if (empty($data['employee_id'])) jsonResponse(['error' => 'employee_id required'], 400);
            jsonResponse($ctrl->messageThreads((int)$data['employee_id']));
            break;
        case 'send-message':
            foreach (['receiver_id','message'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $employeeId = (int)($_SESSION['user']['employee_id'] ?? 0);
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
