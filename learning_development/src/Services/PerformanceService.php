<?php

namespace HRManagement\Services;

use HRManagement\Models\PerformanceReview;
use HRManagement\Repositories\PerformanceReviewRepository;

/**
 * Performance Management Service
 * 
 * Handles performance reviews and ratings
 */
class PerformanceService
{
    private PerformanceReviewRepository $reviewRepository;

    public function __construct()
    {
        $this->reviewRepository = new PerformanceReviewRepository();
    }

    /**
     * Create a performance review
     */
    public function createReview(array $data, int $reviewerId): int
    {
        $review = new PerformanceReview();
        $review->setEmployeeId($data['employee_id'])
            ->setReviewerId($reviewerId)
            ->setReviewPeriodStart($data['review_period_start'])
            ->setReviewPeriodEnd($data['review_period_end'])
            ->setRating($data['rating'] ?? null)
            ->setComments($data['comments'] ?? '')
            ->setStatus($data['status'] ?? 'draft');

        return $this->reviewRepository->create($review);
    }

    /**
     * Submit review
     */
    public function submitReview(int $reviewId, array $data): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new \Exception('Review not found');
        }

        $review->setRating($data['rating'] ?? $review->getRating())
            ->setComments($data['comments'] ?? $review->getComments())
            ->setStatus('submitted')
            ->setReviewedDate(date('Y-m-d H:i:s'));

        return $this->reviewRepository->update($review);
    }

    /**
     * Complete review
     */
    public function completeReview(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        
        if (!$review) {
            throw new \Exception('Review not found');
        }

        $review->setStatus('completed');
        return $this->reviewRepository->update($review);
    }

    /**
     * Get review details with user information
     */
    public function getReviewDetails(int $reviewId): ?array
    {
        return $this->reviewRepository->getReviewWithDetails($reviewId);
    }

    /**
     * Get employee's reviews
     */
    public function getEmployeeReviews(int $employeeId): array
    {
        return $this->reviewRepository->findByEmployee($employeeId);
    }

    /**
     * Get reviewer's assigned reviews
     */
    public function getReviewerAssignments(int $reviewerId): array
    {
        return $this->reviewRepository->findByReviewer($reviewerId);
    }

    /**
     * Get pending reviews for reviewer
     */
    public function getPendingReviews(int $reviewerId): array
    {
        $reviews = $this->reviewRepository->findByReviewer($reviewerId);
        return array_filter($reviews, fn($r) => $r->getStatus() === 'draft');
    }

    /**
     * Get review cycle reviews
     */
    public function getReviewCycleReviews(string $startDate, string $endDate): array
    {
        return $this->reviewRepository->findByPeriod($startDate, $endDate);
    }

    /**
     * Get employee's average rating
     */
    public function getEmployeeAverageRating(int $employeeId): ?float
    {
        return $this->reviewRepository->getEmployeeAverageRating($employeeId);
    }

    /**
     * Get all reviews with details
     */
    public function getAllReviewsWithDetails(int $limit = 20, int $offset = 0): array
    {
        return $this->reviewRepository->findAllWithDetails($limit, $offset);
    }

    /**
     * Delete review
     */
    public function deleteReview(int $reviewId): bool
    {
        return $this->reviewRepository->delete($reviewId);
    }

    /**
     * Get performance statistics for employee
     */
    public function getEmployeePerformanceStats(int $employeeId): array
    {
        $reviews = $this->getEmployeeReviews($employeeId);
        $averageRating = $this->getEmployeeAverageRating($employeeId);
        $totalReviews = count($reviews);
        $completedReviews = count(array_filter($reviews, fn($r) => $r->isCompleted()));

        return [
            'total_reviews' => $totalReviews,
            'completed_reviews' => $completedReviews,
            'pending_reviews' => $totalReviews - $completedReviews,
            'average_rating' => $averageRating,
        ];
    }
}
