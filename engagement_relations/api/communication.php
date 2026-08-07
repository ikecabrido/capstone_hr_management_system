<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/../../auth/User.php';

use App\Controllers\CommunicationController;
use App\Controllers\MessageController;

if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
            $employeeId = trim((string)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? $_SESSION['user']['employee_id'] ?? ''));
            if ($employeeId === '') {
                jsonResponse(['error' => 'Current user is not linked to an employee record'], 400);
            }
            $id = $ctrl->postAnnouncement($data['title'], $data['content'], $employeeId);
            jsonResponse(['id' => $id], 201);
            break;
        case 'messages':
            jsonResponse(['error' => 'Use /api/index.php?resource=message&action=threads instead'], 400);
            break;
        case 'send-message':
            jsonResponse(['error' => 'Use /api/index.php?resource=message&action=send instead'], 400);
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
        case 'shared_files':
            jsonResponse(['success' => true, 'data' => $ctrl->getSharedFiles()]);
            break;
        case 'share_file':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            if (empty($_FILES['shared_file']) || $_FILES['shared_file']['error'] !== UPLOAD_ERR_OK) {
                jsonResponse(['success' => false, 'message' => 'File missing or upload error'], 400);
            }

            $file = $_FILES['shared_file'];
            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'xlsx', 'xls'];
            $maxSize = 10 * 1024 * 1024;
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed, true)) {
                jsonResponse(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)], 400);
            }

            if ($file['size'] > $maxSize) {
                jsonResponse(['success' => false, 'message' => 'File too large. Max size is 10MB.'], 400);
            }

            $uploadDir = __DIR__ . '/../../uploads/social_files/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
            $targetFileName = time() . '_' . $safeName;
            $targetFile = $uploadDir . $targetFileName;

            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                jsonResponse(['success' => false, 'message' => 'Failed to store uploaded file.'], 500);
            }

            $createdBy = $_SESSION['user']['id'] ?? null;
            if (empty($createdBy)) {
                unlink($targetFile);
                jsonResponse(['success' => false, 'message' => 'Current user is not identified.'], 400);
            }

            $description = trim((string)($_POST['description'] ?? $data['description'] ?? ''));
            $content = trim((string)($_POST['content'] ?? $data['content'] ?? ''));
            $fileId = $ctrl->shareFile($createdBy, $safeName, 'uploads/social_files/' . $targetFileName, $file['size'], $ext, $description, $content);

            if (!$fileId) {
                unlink($targetFile);
                jsonResponse(['success' => false, 'message' => 'Failed to save file information.'], 500);
            }

            $fileUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']) . '/../../uploads/social_files/' . $targetFileName);

            jsonResponse([
                'success' => true,
                'message' => 'File shared successfully.',
                'file' => [
                    'id' => $fileId,
                    'name' => $safeName,
                    'path' => 'uploads/social_files/' . $targetFileName,
                    'url' => $fileUrl,
                    'size' => $file['size'],
                    'type' => $ext
                ]
            ], 201);
            break;
        case 'delete_shared_file':
            if (empty($data['id'])) {
                jsonResponse(['error' => 'File ID is required'], 400);
            }
            $file = $ctrl->getSharedFileById((int)$data['id']);
            if (!$file) {
                jsonResponse(['error' => 'File not found'], 404);
            }
            $filePath = __DIR__ . '/../../' . ltrim($file['file_path'], '/\\');
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $ctrl->deleteSharedFile((int)$data['id']);
            jsonResponse(['success' => true, 'message' => 'File deleted successfully'], 200);
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

