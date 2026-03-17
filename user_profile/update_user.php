<?php
session_start();
require_once "../auth/User.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

$userModel = new User();
$userId = $_SESSION['user']['id'];

$messages = [];

// Update name if provided
$fullName = $_POST['full_name'] ?? '';
if ($fullName) {
    $userModel->updateProfile($userId, $fullName);
    $_SESSION['user']['name'] = $fullName;
    $messages[] = 'Profile updated successfully.';
}

// Update password if all fields are filled
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($current || $new || $confirm) {
    if (!$current || !$new || !$confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all password fields to change password.']);
        exit;
    }

    if ($new !== $confirm) {
        echo json_encode(['status' => 'error', 'message' => 'New password and confirm password do not match.']);
        exit;
    }

    $user = $userModel->findById($userId);
    if (!password_verify($current, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    $userModel->updatePassword($userId, $new);
    $messages[] = 'Password changed successfully.';
}

// If password and name was changed
echo json_encode(['status' => 'success', 'message' => $messages]);
exit;
