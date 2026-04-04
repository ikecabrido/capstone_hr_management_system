<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once "../../auth/database.php";
require_once "../../auth/auth.php";
require_once "../controllers/CourseController.php";
require_once "../controllers/ArchiveController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Verify session
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }

    $courseId = (int)$_POST['course_id'];
    if ($courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
        exit;
    }

    $courseController = new CourseController();
    $archiveController = new ArchiveController();

    // Check if user owns this course or is learning/admin
    $course = $courseController->show($courseId);
    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    if (!in_array($_SESSION['user']['role'], ['learning', 'admin']) && $course['created_by'] != $_SESSION['user']['id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this course']);
        exit;
    }

    // Archive the course to ld_archive table
    $archiveResult = $archiveController->archiveCourse($courseId, $_SESSION['user']['id']);
    
    if ($archiveResult['success']) {
        // Also set status to inactive
        $courseController->update($courseId, ['status' => 'inactive']);
    }

    echo json_encode($archiveResult);
    exit;

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}
?>