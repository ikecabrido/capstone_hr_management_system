<?php

/**
 * Refactored Career Development Module
 * 
 * Shows how to handle career paths and individual development plans
 */

require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\CareerDevelopmentService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Validator;
use HRManagement\Utils\ResponseHandler;

AuthManager::startSession();

class CareerDevelopmentModule
{
    private CareerDevelopmentService $careerService;
    private Validator $validator;

    public function __construct()
    {
        $this->careerService = new CareerDevelopmentService();
        $this->validator = new Validator();
    }

    /**
     * Create career path
     */
    public function handleCreateCareerPath(array $data): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $pathId = $this->careerService->createCareerPath($data, $userId);
            
            ResponseHandler::created(['id' => $pathId], 'Career path created')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Create individual development plan
     */
    public function handleCreateIDP(array $data): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $employeeId = (int)($data['employee_id'] ?? 0);
            
            if ($employeeId <= 0) {
                throw new Exception('Invalid employee ID');
            }

            $idpId = $this->careerService->createIDP($employeeId, $userId, $data);
            
            ResponseHandler::created(['id' => $idpId], 'Development plan created')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Update IDP
     */
    public function handleUpdateIDP(int $idpId, array $data): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->careerService->updateIDP($idpId, $data);
            ResponseHandler::success(null, 'Plan updated')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Get user's current development plan
     */
    public function getUserCurrentPlan(int $userId): ?array
    {
        return $this->careerService->getUserCurrentPlan($userId);
    }

    /**
     * Get all available career paths
     */
    public function getAvailableCareerPaths(int $limit = 20): array
    {
        $paths = $this->careerService->getAvailableCareerPaths($limit);
        
        return array_map(fn($p) => $p->toArray(), $paths);
    }

    /**
     * Search career paths
     */
    public function searchCareerPaths(string $searchTerm, int $limit = 20): array
    {
        $results = $this->careerService->searchCareerPaths($searchTerm, $limit);
        
        return array_map(fn($p) => $p->toArray(), $results);
    }

    /**
     * Get career path details
     */
    public function getCareerPathDetails(int $pathId): ?array
    {
        return $this->careerService->getPathDetails($pathId);
    }

    /**
     * Complete development plan
     */
    public function handleCompletePlan(int $idpId): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->careerService->completeIDP($idpId);
            ResponseHandler::success(null, 'Plan marked as completed')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Delete career path
     */
    public function handleDeleteCareerPath(int $pathId): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->careerService->deleteCareerPath($pathId);
            ResponseHandler::success(null, 'Career path deleted')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = new CareerDevelopmentModule();
    $action = $_POST['action'] ?? '';

    match ($action) {
        'create_path' => $module->handleCreateCareerPath($_POST),
        'update_path' => $module->handleUpdateCareerPath($_POST['path_id'] ?? 0, $_POST),
        'delete_path' => $module->handleDeleteCareerPath($_POST['path_id'] ?? 0),
        'create_idp' => $module->handleCreateIDP($_POST),
        'update_idp' => $module->handleUpdateIDP($_POST['idp_id'] ?? 0, $_POST),
        'complete_idp' => $module->handleCompletePlan($_POST['idp_id'] ?? 0),
        default => ResponseHandler::error('Invalid action')->send(),
    };
}
