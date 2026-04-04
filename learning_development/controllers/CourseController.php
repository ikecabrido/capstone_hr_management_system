<?php
require_once __DIR__ . "/../models/Course.php";

class CourseController {
    private $model;

    public function __construct() {
        $this->model = new Course();
    }

    public function index() {
        $courses = $this->model->getAllCourses();
        return $courses;
    }

    public function getByCreator($creatorId) {
        return $this->model->getCoursesByCreator($creatorId);
    }

    public function show($id) {
        return $this->model->getCourseById($id);
    }

    public function store($data) {
        if ($this->model->createCourse($data)) {
            return ['success' => true, 'message' => 'Course created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create course'];
    }

    public function update($id, $data) {
        if ($this->model->updateCourse($id, $data)) {
            return ['success' => true, 'message' => 'Course updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update course'];
    }

    public function destroy($id) {
        if ($this->model->deleteCourse($id)) {
            return ['success' => true, 'message' => 'Course deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete course'];
    }
}
?>