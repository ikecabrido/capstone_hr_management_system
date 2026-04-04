<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Models\SharedFile;

session_start();
$action = $_GET['action'] ?? 'list';
if (!isset($_SESSION['user']) && $action !== 'list') {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$sharedFileModel = new SharedFile();

try {
    switch ($action) {
        case 'list':
            jsonResponse(['success' => true, 'data' => $sharedFileModel->getAllFiles()]);
            break;
        case 'delete':
            if (empty($_GET['id'])) jsonResponse(['error' => 'id required'], 400);
            $file = $sharedFileModel->getFileById((int)$_GET['id']);
            if (!$file) jsonResponse(['error' => 'File not found'], 404);
            
            // Check if user can delete (uploaded by them or admin)
            $canDelete = ($_SESSION['user']['employee_id'] ?? $_SESSION['user']['id']) === $file['uploaded_by'] || 
                        (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin');
            if (!$canDelete) jsonResponse(['error' => 'Unauthorized to delete this file'], 403);
            
            // Delete file from filesystem
            $filePath = __DIR__ . '/../../' . $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $sharedFileModel->deleteFile((int)$_GET['id']);
            jsonResponse(['success' => true, 'message' => 'File deleted successfully']);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
