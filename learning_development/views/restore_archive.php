<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/ArchiveController.php";

// Check permissions
if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$archiveId = isset($_POST['archive_id']) ? (int)$_POST['archive_id'] : null;
$archiveType = isset($_POST['archive_type']) ? trim($_POST['archive_type']) : null;

// Debug logging when request fails
if (empty($archiveId) || empty($archiveType)) {
    error_log('restore_archive: missing params, POST=' . print_r($_POST, true));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters', 'post' => $_POST]);
    exit;
}

$archiveController = new ArchiveController();
$result = $archiveController->restore($archiveId, $_SESSION['user']['id']);

header('Content-Type: application/json');
echo json_encode($result);
?>
