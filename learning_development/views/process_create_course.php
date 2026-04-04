<?php
session_start();
require_once "../../auth/auth_check.php";
// Check permissions
if ($_SESSION['user']['role'] !== 'learning') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    header("Location: ../learning_development.php");
    exit;
}

require_once "../controllers/CourseController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseController = new CourseController();

    $data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'duration_hours' => $_POST['duration_hours'] ?? '',
        'training_program_id' => $_POST['training_program_id'] ?? '',
        'content_type' => $_POST['content_type'] ?? 'in-person',
        'created_by' => $_SESSION['user']['id']
    ];

    $result = $courseController->store($data);

    // Check if this is an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Course created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Failed to create course.']);
        }
        exit;
    }

    // Regular form submission
    if ($result['success']) {
        $_SESSION['success_message'] = "Course created successfully!";
        header("Location: browse.php?section=courses");
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? "Failed to create course. Please try again.";
        header("Location: browse.php?section=courses");
        exit;
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
    header("Location: create_course.php");
    exit;
}
?>