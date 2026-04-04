<?php
require_once __DIR__ . "/../models/Enrollment.php";

class EnrollmentController {
    private $model;

    public function __construct() {
        $this->model = new Enrollment();
    }

    public function index() {
        $enrollments = $this->model->getAllEnrollments();
        return $enrollments;
    }

    public function getByEmployee($employeeId) {
        return $this->model->getEnrollmentsByEmployee($employeeId);
    }

    public function getProgramsWithEnrollmentDetails() {
        return $this->model->getProgramsWithEnrollmentDetails();
    }

    public function getEnrollmentsByProgram($programId) {
        return $this->model->getEnrollmentsByProgram($programId);
    }

    public function getEnrollmentsByCourse($courseId) {
        return $this->model->getEnrollmentsByCourse($courseId);
    }

    public function enroll($data) {
        if ($this->model->enrollEmployee($data)) {
            return ['success' => true, 'message' => 'Employee enrolled successfully'];
        }
        return ['success' => false, 'message' => 'Failed to enroll employee'];
    }

    public function updateProgress($id, $progress) {
        if ($this->model->updateProgress($id, $progress)) {
            return ['success' => true, 'message' => 'Progress updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update progress'];
    }

    public function show($id) {
        return $this->model->getEnrollmentById($id);
    }
}
?>