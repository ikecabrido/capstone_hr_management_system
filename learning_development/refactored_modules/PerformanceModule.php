<?php

/**
 * Refactored Performance Management Module
 * 
 * Shows how to handle performance reviews
 */

require_once __DIR__ . '/../autoload.php';

use HRManagement\Services\PerformanceService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Validator;
use HRManagement\Utils\ResponseHandler;

AuthManager::startSession();

class PerformanceModule
{
    private PerformanceService $performanceService;
    private Validator $validator;

    public function __construct()
    {
        $this->performanceService = new PerformanceService();
        $this->validator = new Validator();
    }

    /**
     * Create performance review
     */
    public function handleCreateReview(array $data): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        if (!$this->validator->validatePerformanceReview($data)) {
            ResponseHandler::validationError($this->validator->getErrors())->send();
        }

        try {
            $userId = AuthManager::getCurrentUserId();
            $reviewId = $this->performanceService->createReview($data, $userId);
            
            ResponseHandler::created(['id' => $reviewId], 'Review created')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Submit review
     */
    public function handleSubmitReview(int $reviewId, array $data): void
    {
        try {
            $this->performanceService->submitReview($reviewId, $data);
            ResponseHandler::success(null, 'Review submitted')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Complete review
     */
    public function handleCompleteReview(int $reviewId): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->performanceService->completeReview($reviewId);
            ResponseHandler::success(null, 'Review completed')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }

    /**
     * Get review details
     */
    public function getReviewDetails(int $reviewId): ?array
    {
        return $this->performanceService->getReviewDetails($reviewId);
    }

    /**
     * Get employee's reviews
     */
    public function getEmployeeReviews(int $employeeId): array
    {
        $reviews = $this->performanceService->getEmployeeReviews($employeeId);
        
        return array_map(fn($r) => $r->toArray(), $reviews);
    }

    /**
     * Get pending reviews
     */
    public function getPendingReviews(int $reviewerId): array
    {
        return $this->performanceService->getPendingReviews($reviewerId);
    }

    /**
     * Get performance statistics
     */
    public function getPerformanceStats(int $employeeId): array
    {
        return $this->performanceService->getEmployeePerformanceStats($employeeId);
    }

    /**
     * Get all reviews with details
     */
    public function getAllReviews(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
        return $this->performanceService->getAllReviewsWithDetails($limit, $offset);
    }

    /**
     * Delete review
     */
    public function handleDeleteReview(int $reviewId): void
    {
        if (!AuthManager::isLearningAdmin()) {
            ResponseHandler::forbidden()->send();
        }

        try {
            $this->performanceService->deleteReview($reviewId);
            ResponseHandler::success(null, 'Review deleted')->send();
        } catch (Exception $e) {
            ResponseHandler::error($e->getMessage())->send();
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module = new PerformanceModule();
    $action = $_POST['action'] ?? '';

    match ($action) {
        'create_review' => $module->handleCreateReview($_POST),
        'edit_review' => $module->handleSubmitReview($_POST['review_id'] ?? 0, $_POST),
        'complete_review' => $module->handleCompleteReview($_POST['review_id'] ?? 0),
        'delete_review' => $module->handleDeleteReview($_POST['review_id'] ?? 0),
        default => ResponseHandler::error('Invalid action')->send(),
    };
}
