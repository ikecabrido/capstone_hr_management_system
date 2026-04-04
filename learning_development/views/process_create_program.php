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

require_once "../controllers/TrainingProgramController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $programController = new TrainingProgramController();

    $data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'trainer' => $_POST['trainer'] ?? '',
        'start_date' => $_POST['start_date'] ?? '',
        'end_date' => $_POST['end_date'] ?? '',
        'max_participants' => $_POST['max_participants'] ?? '',
        'status' => $_POST['status'] ?? 'active',
        'created_by' => $_SESSION['user']['id']
    ];

    $result = $programController->store($data);

    // Check if this is an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Training program created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Failed to create training program.']);
        }
        exit;
    }

    // Regular form submission
    if ($result['success']) {
        $_SESSION['success_message'] = "Training program created successfully!";
        header("Location: browse.php?section=programs");
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? "Failed to create training program. Please try again.";
        header("Location: browse.php?section=programs");
        exit;
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
    header("Location: create_training_program.php");
    exit;
}
?>