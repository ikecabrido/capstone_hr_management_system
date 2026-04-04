<?php
session_start();
require_once "../../auth/auth_check.php";
if ($_SESSION['user']['role'] !== 'learning' && $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../learning_development.php");
    exit;
}

require_once "../controllers/CertificationController.php";
require_once "../../auth/user.php";
require_once "../models/Course.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id']) || empty($_POST['employee_id']) || empty($_POST['course_id']) || empty($_POST['certification_name']) || empty($_POST['issued_date'])) {
        $_SESSION['error_message'] = "All required fields must be filled.";
        header("Location: certification_management.php");
        exit;
    }

    $userModel = new User();
    $employee = $userModel->findById((int)$_POST['employee_id']);
    if (!$employee) {
        $_SESSION['error_message'] = "Selected employee does not exist.";
        header("Location: certification_management.php");
        exit;
    }

    $courseModel = new Course();
    $course = $courseModel->getCourseById((int)$_POST['course_id']);
    if (!$course) {
        $_SESSION['error_message'] = "Selected course does not exist.";
        header("Location: certification_management.php");
        exit;
    }

    $certificateController = new CertificationController();
    $data = [
        'id' => (int)$_POST['id'],
        'employee_id' => (int)$_POST['employee_id'],
        'course_id' => (int)$_POST['course_id'],
        'certification_name' => trim($_POST['certification_name']),
        'issued_date' => $_POST['issued_date'],
        'expiry_date' => $_POST['expiry_date'] ?: null,
        'issued_by' => (int)$_POST['issued_by'],
        'status' => $_POST['status'] ?? 'active'
    ];

    $result = $certificateController->update($data);
    if ($result['success']) {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    header("Location: certification_management.php");
    exit;
}
header("Location: certification_management.php");
exit;
?>