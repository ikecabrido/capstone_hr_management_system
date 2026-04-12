<?php

require_once __DIR__ . '/../models/TrainingProgram.php';

class TrainingProgramController
{
    private $trainingModel;
    public function __construct()
    {
        $this->trainingModel = new TrainingProgram();
    }
    public function index()
    {
        Auth::requireAuth();

        $title = "Training Programs";
        $content = __DIR__ . '/../views/learning-development/training/main-content.php';
        require __DIR__ . '/../views/learning-development/index.php';
    }
    public function adminIndex()
    {
        $items = $this->trainingModel->all();

        $enrollmentDetails = [];
        $enrollmentCounts = [];

        foreach ($items as $program) {

            $programId = $program['ld_training_programs_id'];

            $data = $this->trainingModel->getEnrollmentDetailsByProgram($programId);

            $enrollmentDetails[$programId] = $data;
            $enrollmentCounts[$programId] = count($data);
        }

        $searchQuery = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $programPage = intval($_GET['program_page'] ?? 1);

        $filteredItems = $items;

        if (!empty($searchQuery)) {
            $filteredItems = filterBySearch($filteredItems, $searchQuery, ['title', 'description']);
        }

        if (!empty($statusFilter)) {
            $filteredItems = filterByStatus($filteredItems, $statusFilter, 'status');
        }

        require_once __DIR__ . '/../helpers/pagination.php';

        $paginatedPrograms = paginateItems($filteredItems, $programPage, 12);

        $title = "Training Programs";
        $content = __DIR__ . '/../views/admin/learning-development/training/training.php';
        require __DIR__ . '/../views/admin/learning-development/index.php';
    }
}
