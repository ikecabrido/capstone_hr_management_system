<?php
session_start();
require_once "../auth/User.php";

if (!isset($_SESSION['user'])) {
    exit;
}

$userModel = new User();
$userId = $_SESSION['user']['id'];
$fullName = $_POST['full_name'] ?? '';

if ($fullName) {
    $userModel->updateProfile($userId, $fullName);
    $_SESSION['user']['full_name'] = $fullName;
}

header("Location: profile.php");
exit;
