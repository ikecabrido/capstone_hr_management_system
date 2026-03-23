<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/TrainingProgramController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['program_id'])) {
    $controller = new TrainingProgramController();
    $programId = (int)$_POST['program_id'];

    // Check if user owns this program
    $program = $controller->show($programId);
    if (!$program || $program['created_by'] !== $_SESSION['user']['id']) {
        $_SESSION['error_message'] = "Unauthorized to edit this program.";
        header("Location: create_training_program.php");
        exit;
    }

    // Prepare update data
    $updateData = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'trainer' => $_POST['trainer'] ?? '',
        'start_date' => $_POST['start_date'] ?? '',
        'end_date' => $_POST['end_date'] ?? '',
        'max_participants' => (int)($_POST['max_participants'] ?? 0),
        'status' => $_POST['status'] ?? 'active'
    ];

    // Validate required fields
    if (empty($updateData['title']) || empty($updateData['description']) || empty($updateData['trainer']) ||
        empty($updateData['start_date']) || empty($updateData['end_date']) || $updateData['max_participants'] <= 0) {
        $_SESSION['error_message'] = "All fields are required and max participants must be greater than 0.";
        header("Location: create_training_program.php?id=" . $programId);
        exit;
    }

    // Update the program
    $result = $controller->update($programId, $updateData);

    if ($result['success']) {
        $_SESSION['success_message'] = "Training program updated successfully!";
        header("Location: create_training_program.php");
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'] ?? "Failed to update training program.";
        header("Location: create_training_program.php?id=" . $programId);
        exit;
    }
}

// If not a POST request or missing program_id, redirect
header("Location: create_training_program.php");
exit;
?>