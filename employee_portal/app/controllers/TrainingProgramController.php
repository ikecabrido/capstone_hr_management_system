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
        $programs = $this->trainingModel->all();
        $title = "Training Programs";
        $content = __DIR__ . '/../views/admin/learning-development/training/main-content.php';
        require __DIR__ . '/../views/admin/learning-development/index.php';
    }

    // public function adminIndex()
    // {
    //     require_once __DIR__ . '/../helpers/pagination.php';
    //     require_once __DIR__ . '/../helpers/search_filter.php';

    //     $items = $this->trainingModel->all();

    //     foreach ($items as &$program) {
    //         $program['id'] = $program['ld_training_programs_id'];
    //     }
    //     unset($program);

    //     $searchQuery = $_GET['search'] ?? '';
    //     $statusFilter = $_GET['status'] ?? '';
    //     $programPage = intval($_GET['program_page'] ?? 1);

    //     $filteredItems = $items;

    //     if (!empty($searchQuery)) {
    //         $filteredItems = filterBySearch($filteredItems, $searchQuery, ['name', 'description']);
    //     }

    //     if (!empty($statusFilter)) {
    //         $filteredItems = filterByStatus($filteredItems, $statusFilter, 'status');
    //     }

    //     $paginatedPrograms = paginateItems($filteredItems, $programPage, 12);

    //     $enrollmentDetails = [];
    //     $enrollmentCounts = [];

    //     foreach ($items as $program) {
    //         $id = $program['id'];
    //         $data = $this->trainingModel->getEnrollmentDetailsByProgram($id);

    //         $enrollmentDetails[$id] = $data;
    //         $enrollmentCounts[$id] = count($data);
    //     }

    //     $currentUserId = $_SESSION['user']['id'] ?? null;
    //     $currentUsername = $_SESSION['user']['name'] ?? null;
    //     $isAuthorized = $this->canManage();

    //     $message = '';
    //     $messageType = 'info';

    //     $role = $this->currentRole();
    //     $title = "Training Programs";
    //     $content = __DIR__ . '/../views/admin/learning-development/training/main-content.php';

    //     $data = [
    //         'paginatedPrograms' => $paginatedPrograms,
    //         'searchQuery' => $searchQuery,
    //         'statusFilter' => $statusFilter,
    //         'currentUserId' => $currentUserId,
    //         'isAuthorized' => $isAuthorized,
    //         'enrollmentDetails' => $enrollmentDetails,
    //         'enrollmentCounts' => $enrollmentCounts
    //     ];
    //     extract($data);
    //     require __DIR__ . '/../views/admin/learning-development/index.php';
    // }
}
