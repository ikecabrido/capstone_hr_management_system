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

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($new !== $confirm) {
    echo json_encode(['status' => 'error', 'message' => 'New password and confirm password do not match.']);
    exit;
}

// verify current password
$user = $userModel->findById($userId);

if (!password_verify($current, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
    exit;
}

// update password
$userModel->updatePassword($userId, $new);

echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
exit;
