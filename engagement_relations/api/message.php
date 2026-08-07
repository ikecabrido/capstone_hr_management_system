<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/../../auth/User.php';

use App\Controllers\MessageController;

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$action = $_GET['action'] ?? 'list';
$data = inputData();
$employeeParam = trim((string)($data['employee_id'] ?? $_GET['employee_id'] ?? ''));
$allowThreadsNoSession = $action === 'threads' && $employeeParam !== '';
if (!isset($_SESSION['user']) && !$allowThreadsNoSession && $action !== 'list') {
   jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new MessageController();
$action = $_GET['action'] ?? 'threads';

try {
    switch ($action) {
        case 'send':
            foreach (['receiver_id','message'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $senderId = trim((string)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? $_SESSION['user']['employee_id'] ?? ''));
            if ($senderId === '' && !empty($_SESSION['user']['username'])) {
                $userModel = new User();
                $userRow = $userModel->findByUsername($_SESSION['user']['username']);
                if ($userRow) {
                    $senderId = trim((string)($userRow['employee_id'] ?? $userRow['id'] ?? $userRow['user_id'] ?? ''));
                }
            }
            if ($senderId === '') {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->sendMessage($senderId, $data['receiver_id'], $data['message']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'threads':
            $empId = trim((string)($data['employee_id'] ?? $_GET['employee_id'] ?? $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? $_SESSION['user']['employee_id'] ?? ''));
            if ($empId === '') {
                if (!isset($_SESSION['user'])) {
                    jsonResponse(['error' => 'Unauthorized'], 401);
                }
                $empId = trim((string)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? $_SESSION['user']['employee_id'] ?? ''));
            }
            $messages = $ctrl->messageThreads($empId);
            jsonResponse($messages);
            break;
        case 'history':
            foreach (['sender_id','receiver_id'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $history = $ctrl->getMessageHistory($data['sender_id'], $data['receiver_id']);
            jsonResponse($history);
            break;
        case 'unread':
            $empId = trim((string)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? $_SESSION['user']['employee_id'] ?? ''));
            $unread = $ctrl->getUnreadMessages($empId);
            jsonResponse($unread);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
