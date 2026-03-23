<?php
require_once 'auth/user.php';
require_once 'learning_development/models/Course.php';

$userModel = new User();
$users = $userModel->getAllUsers();
echo 'Users count: ' . count($users) . PHP_EOL;
foreach($users as $user) {
    echo 'ID: ' . $user['id'] . ', Name: ' . $user['full_name'] . PHP_EOL;
}

$courseModel = new Course();
$courses = $courseModel->getAllCourses();
echo 'Courses count: ' . count($courses) . PHP_EOL;
foreach($courses as $course) {
    echo 'ID: ' . $course['id'] . ', Title: ' . $course['title'] . PHP_EOL;
}
?>