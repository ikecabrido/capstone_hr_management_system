<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once "../../auth/database.php";
require_once "../../auth/auth.php";
require_once "../controllers/CertificationController.php";
require_once "../controllers/ArchiveController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['certification_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Verify session
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    // Only learning role can archive certifications
    if ($_SESSION['user']['role'] !== 'learning') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Only admins can archive certifications']);
        exit;
    }

    $certificationId = (int)$_POST['certification_id'];
    if ($certificationId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid certification ID']);
        exit;
    }

    $certificationController = new CertificationController();
    $archiveController = new ArchiveController();

    // Check if certification exists
    $certification = $certificationController->show($certificationId);
    if (!$certification) {
        echo json_encode(['success' => false, 'message' => 'Certification not found']);
        exit;
    }

    // Archive the certification to ld_archive table
    $archiveResult = $archiveController->archiveCertification($certificationId, $_SESSION['user']['id']);
    
    if ($archiveResult['success']) {
        // Also revoke the certification
        $certificationController->revoke($certificationId);
    }

    echo json_encode($archiveResult);
    exit;

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}
?>
