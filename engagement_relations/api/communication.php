<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\CommunicationController;

session_start();
$action = $_GET['action'] ?? 'list';
if (!isset($_SESSION['user']) && $action !== 'list') {
   jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new CommunicationController();
$action = $_GET['action'] ?? 'announcements';
$data = inputData();

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
            $empId = (int)($data['employee_id'] ?? ($_SESSION['user']['id'] ?? 0));
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
            $id = $ctrl->sendMessage($employeeId, $data['receiver_id'], $data['message']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'notifications':
            jsonResponse($ctrl->getNotifications());
            break;
        case 'post_event':
            foreach (['title', 'date', 'description'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $eventId = $ctrl->postEvent($data['title'], $data['date'], $data['description']);
            jsonResponse(['event_id' => $eventId], 201);
            break;
        case 'policy_updates':
            jsonResponse($ctrl->getPolicyUpdates());
            break;
        case 'delete_policy':
            if (empty($data['policy_id'])) {
                jsonResponse(['error' => 'Policy ID is required'], 400);
            }
            $ctrl->deletePolicy((int)$data['policy_id']);
            jsonResponse(['success' => true], 200);
            break;
        case 'mark_notification_read':
            if (empty($data['notification_id'])) {
                jsonResponse(['error' => 'Notification ID is required'], 400);
            }
            $ctrl->markNotificationAsRead($data['notification_id']);
            jsonResponse(['success' => true], 200);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
