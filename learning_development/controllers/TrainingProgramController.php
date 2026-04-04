<?php
require_once __DIR__ . "/../models/TrainingProgram.php";

class TrainingProgramController {
    private $model;

    public function __construct() {
        $this->model = new TrainingProgram();
    }

    public function index() {
        $programs = $this->model->getAllPrograms();
        return $programs;
    }

    public function getByCreator($creatorId) {
        return $this->model->getProgramsByCreator($creatorId);
    }

    public function show($id) {
        return $this->model->getProgramById($id);
    }

    public function store($data) {
        if ($this->model->createProgram($data)) {
            return ['success' => true, 'message' => 'Training program created successfully'];
        }
        return ['success' => false, 'message' => 'Failed to create training program'];
    }

    public function update($id, $data) {
        if ($this->model->updateProgram($id, $data)) {
            return ['success' => true, 'message' => 'Training program updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update training program'];
    }

    public function destroy($id) {
        if ($this->model->deleteProgram($id)) {
            return ['success' => true, 'message' => 'Training program deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete training program'];
    }
}
?>