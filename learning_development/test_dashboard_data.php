<?php
session_start();
require_once "../auth/database.php";
require_once "models/TrainingProgram.php";
require_once "models/Course.php";
require_once "models/Enrollment.php";
require_once "models/Certification.php";

header('Content-Type: application/json');

try {
    $programController = new TrainingProgram();
    $programs = $programController->getAllPrograms();
    
    $courseController = new Course();
    $courses = $courseController->getAllCourses();
    
    $enrollmentController = new Enrollment();
    $enrollments = $enrollmentController->getAllEnrollments();
    
    $certificationController = new Certification();
    $certifications = $certificationController->getAllCertifications();
    
    echo json_encode([
        'programs' => [
            'count' => count($programs),
            'data' => $programs
        ],
        'courses' => [
            'count' => count($courses),
            'data' => $courses
        ],
        'enrollments' => [
            'count' => count($enrollments),
            'data' => $enrollments
        ],
        'certifications' => [
            'count' => count($certifications),
            'data' => $certifications
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
