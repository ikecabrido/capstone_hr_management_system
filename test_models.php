<?php
require_once 'auth/user.php';
require_once 'learning_development/models/Course.php';

$userModel = new User();
$users = $userModel->getAllUsers();
echo 'Users found: ' . count($users) . PHP_EOL;

$courseModel = new Course();
$courses = $courseModel->getAllCourses();
echo 'Courses found: ' . count($courses) . PHP_EOL;
?>