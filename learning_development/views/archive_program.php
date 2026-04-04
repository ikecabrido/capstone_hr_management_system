<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once "../../auth/database.php";
require_once "../../auth/auth.php";
require_once "../controllers/TrainingProgramController.php";
require_once "../controllers/ArchiveController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['program_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Verify session
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $programId = (int)$_POST['program_id'];
    if ($programId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid program ID']);
        exit;
    }

    $programController = new TrainingProgramController();
    $archiveController = new ArchiveController();

    // Check if user owns this program or is learning/admin
    $program = $programController->show($programId);
    if (!$program) {
        echo json_encode(['success' => false, 'message' => 'Program not found']);
        exit;
    }

    if (!in_array($_SESSION['user']['role'], ['learning', 'admin']) && $program['created_by'] != $_SESSION['user']['id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this program']);
        exit;
    }

    // Archive the program to ld_archive table
    $archiveResult = $archiveController->archiveProgram($programId, $_SESSION['user']['id']);
    
    if ($archiveResult['success']) {
        // Also set status to inactive
        $programController->update($programId, ['status' => 'inactive']);
    }

    echo json_encode($archiveResult);
    exit;

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}
?>