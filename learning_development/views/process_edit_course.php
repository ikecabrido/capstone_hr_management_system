<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/CourseController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $controller = new CourseController();
    $courseId = (int)$_POST['course_id'];

    // Check if user owns this course
    $course = $controller->show($courseId);
    if (!$course || $course['created_by'] !== $_SESSION['user']['id']) {
        $_SESSION['error_message'] = "Unauthorized to edit this course.";
        header("Location: create_course.php");
        exit;
    }

    // Prepare update data
    $updateData = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'instructor' => $_POST['instructor'] ?? '',
        'duration_hours' => (int)($_POST['duration_hours'] ?? 0),
        'training_program_id' => (int)($_POST['training_program_id'] ?? 0),
        'content_type' => $_POST['content_type'] ?? 'in-person',
        'status' => $_POST['status'] ?? 'active'
    ];

    // Validate required fields
    if (empty($updateData['title']) || empty($updateData['description']) || empty($updateData['instructor']) ||
        $updateData['duration_hours'] <= 0 || $updateData['training_program_id'] <= 0) {
        $_SESSION['error_message'] = "All fields are required and duration must be greater than 0.";
        header("Location: create_course.php?id=" . $courseId);
        exit;
    }

    // Update the course
    $result = $controller->update($courseId, $updateData);

    if ($result['success']) {
        $_SESSION['success_message'] = "Course updated successfully!";
        header("Location: create_course.php");
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? "Failed to update course.";
        header("Location: create_course.php?id=" . $courseId);
        exit;
    }
}

// If not a POST request or missing course_id, redirect
header("Location: create_course.php");
exit;
?>