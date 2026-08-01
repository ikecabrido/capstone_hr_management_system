<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/EnrollmentController.php";
require_once "../controllers/CourseController.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$enrollmentController = new EnrollmentController();
$courseController = new CourseController();
$employeeId = $_SESSION['user']['id'];

$courseId = null;

if (!empty($_POST['course_id'])) {
    $courseId = intval($_POST['course_id']);
} elseif (!empty($_POST['program_id'])) {
    $programId = intval($_POST['program_id']);
    // Enroll in first active course in program
    $allCourses = $courseController->index();
    foreach ($allCourses as $course) {
        if ($course['ld_training_programs_id'] == $programId && $course['status'] === 'active') {
            $courseId = $course['ld_courses_id'];
            break;
        }
    }
    if (!$courseId) {
        echo json_encode(['success' => false, 'message' => 'No active course found in this program']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing course or program id']);
    exit;
}

$result = $enrollmentController->enroll(['employee_id' => $employeeId, 'course_id' => $courseId]);
if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Enrollment successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to enroll - possible duplicate or system error']);
}
