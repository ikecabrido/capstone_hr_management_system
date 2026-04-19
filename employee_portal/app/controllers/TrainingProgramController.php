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
        $filters = $this->getSearchFilters();
        $page = $_GET['page'] ?? 1;

        $data = $this->trainingModel->searchPrograms($filters, $page);

        $programs = $data['programs'];
        $totalPages = $data['totalPages'];
        $currentPage = $data['currentPage'];

        $searchQuery = $filters['search'];
        $statusFilter = $filters['status'];

        $title = "Training Programs";
        $content = __DIR__ . '/../views/learning-development/training/main-content.php';

        require __DIR__ . '/../views/index.php';
    }
    public function adminIndex()
    {
        $data = $this->getPaginatedPrograms();

        extract($data);

        $title = "Training Programs";
        $content = __DIR__ . '/../views/admin/learning-development/training/main-content.php';

        require __DIR__ . '/../views/admin/learning-development/index.php';
    }
    public function paginate()
    {
        $this->adminIndex();
    }
    private function getPaginatedPrograms()
    {
        $allPrograms = $this->trainingModel->all();

        $limit = 9;

        $page = isset($_GET['page']) && is_numeric($_GET['page'])
            ? (int) $_GET['page']
            : 1;

        $page = max(1, $page);

        $searchQuery = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';

        $filteredPrograms = array_filter($allPrograms, function ($program) use ($searchQuery, $statusFilter) {

            $matchesSearch = empty($searchQuery) ||
                stripos($program['title'], $searchQuery) !== false ||
                stripos($program['description'], $searchQuery) !== false;

            $matchesStatus = empty($statusFilter) ||
                strtolower($program['status']) === strtolower($statusFilter);

            return $matchesSearch && $matchesStatus;
        });

        $filteredPrograms = array_values($filteredPrograms);

        $totalPrograms = count($filteredPrograms);
        $totalPages = max(1, ceil($totalPrograms / $limit));

        $offset = ($page - 1) * $limit;
        $programs = array_slice($filteredPrograms, $offset, $limit);

        return [
            'programs' => $programs,
            'page' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $searchQuery,
            'statusFilter' => $statusFilter
        ];
    }
    public function clearBtn()
    {
        header("Location: index.php?url=training-program-index");
        exit;
    }
    private function getSearchFilters()
    {
        return [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
    }
}
