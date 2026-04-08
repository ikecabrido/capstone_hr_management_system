<?php

/**
 * Refactored Training Module
 * 
 * Shows how to use the OOP architecture with training programs and enrollments
 */

require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Validator;
use HRManagement\Utils\ResponseHandler;

// Start session
AuthManager::startSession();

class TrainingModule
{
    private TrainingService $trainingService;
    private Validator $validator;

    public function __construct()
    {
        $this->trainingService = new TrainingService();
        $this->validator = new Validator();
    }

    /**
     * Handle training program creation
     */
    public function handleCreateProgram(array $data): void
    {
        // Check authorization
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden('Only learning admins can create programs')->send();
        }

        // Validate input
        if (!$this->validator->validateTrainingProgram($data)) {
            ResponseHandler::validationError($this->validator->getErrors())->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $programId = $this->trainingService->createProgram($data, $userId);
            
            ResponseHandler::created(['id' => $programId], 'Program created successfully')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Handle program update
     */
    public function handleUpdateProgram(int $programId, array $data): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden('Only learning admins can update programs')->send();
        }

        try {
            $this->trainingService->updateProgram($programId, $data);
            ResponseHandler::success(null, 'Program updated successfully')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Handle user enrollment
     */
    public function handleEnrollment(int $programId): void
    {
        if (!AuthManager::isLoggedIn()) {
            ResponseHandler::unauthorized('You must be logged in to enroll')->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $enrollmentId = $this->trainingService->enrollUser($userId, $programId);
            
            ResponseHandler::created(['id' => $enrollmentId], 'Enrolled successfully')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage(), 400)->send();
        }
    }

    /**
     * Handle unenrollment
     */
    public function handleUnenrollment(int $programId): void
    {
        if (!AuthManager::isLoggedIn()) {
            ResponseHandler::unauthorized()->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $this->trainingService->unenrollUser($userId, $programId);
            
            ResponseHandler::success(null, 'Unenrolled successfully')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Get available programs
     */
    public function getAvailablePrograms(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        return $this->trainingService->getAvailablePrograms($limit);
    }

    /**
     * Get user's enrollments
     */
    public function getUserEnrollments(int $userId): array
    {
        $enrollments = $this->trainingService->getUserEnrollments($userId);
        
        return array_map(fn($e) => $e->toArray(), $enrollments);
    }

    /**
     * Search programs
     */
    public function searchPrograms(string $searchTerm, int $limit = 20): array
    {
        $results = $this->trainingService->searchPrograms($searchTerm, $limit);
        
        return array_map(fn($p) => $p->toArray(), $results);
    }

    /**
     * Get program details with stats
     */
    public function getProgramDetails(int $programId): ?array
    {
        return $this->trainingService->getProgramDetails($programId);
    }

    /**
     * Delete program
     */
    public function handleDeleteProgram(int $programId): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->trainingService->deleteProgram($programId);
            ResponseHandler::success(null, 'Program deleted')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = new TrainingModule();
    $action = $_POST['action'] ?? '';
    $data = $_POST;

    match ($action) {
        'create' => $module->handleCreateProgram($data),
        'update' => $module->handleUpdateProgram($_POST['id'] ?? 0, $data),
        'enroll' => $module->handleEnrollment($_POST['id'] ?? 0),
        'unenroll' => $module->handleUnenrollment($_POST['id'] ?? 0),
        'delete' => $module->handleDeleteProgram($_POST['id'] ?? 0),
        default => ResponseHandler::error('Invalid action')->send(),
    };
}
