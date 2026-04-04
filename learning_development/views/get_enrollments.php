<?php
header('Content-Type: application/json');
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/EnrollmentController.php";

$enrollmentController = new EnrollmentController();

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id || !in_array($type, ['program', 'course'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters.', 'enrollments' => []]);
    exit;
}

try {
    if ($type === 'program') {
        $enrollments = $enrollmentController->getEnrollmentsByProgram($id);
    } else {
        $enrollments = $enrollmentController->getEnrollmentsByCourse($id);
    }

    echo json_encode(['success' => true, 'enrollments' => $enrollments]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'enrollments' => []]);
    exit;
}
