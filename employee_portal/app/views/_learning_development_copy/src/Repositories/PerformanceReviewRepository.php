<?php

namespace HRManagement\Repositories;

use HRManagement\Models\PerformanceReview;

/**
 * Performance Review Repository
 */
class PerformanceReviewRepository extends BaseRepository
{
    protected string $tableName = 'performance_reviews';
    protected string $modelClass = PerformanceReview::class;

    /**
     * Find reviews for employee
     */
    public function findByEmployee(int $employeeId, int $limit = null): array
    {
        return $this->findBy(['employee_id' => $employeeId], $limit);
    }

    /**
     * Find reviews by reviewer
     */
    public function findByReviewer(int $reviewerId, int $limit = null): array
    {
        return $this->findBy(['reviewer_id' => $reviewerId], $limit);
    }

    /**
     * Find reviews by status
     */
    public function findByStatus(string $status, int $limit = null): array
    {
        return $this->findBy(['status' => $status], $limit);
    }

    /**
     * Get review with user details
     */
    public function getReviewWithDetails(int $reviewId): ?array
    {
        $query = "SELECT 
                    pr.*,
                    u1.full_name as employee_name,
                    u1.email as employee_email,
                    u2.full_name as reviewer_name,
                    u2.email as reviewer_email
                  FROM {$this->tableName} pr
                  LEFT JOIN users u1 ON pr.employee_id = u1.id
                  LEFT JOIN users u2 ON pr.reviewer_id = u2.id
                  WHERE pr.id = ?";
        
        return $this->db->fetchOne($query, [$reviewId]);
    }

    /**
     * Get all reviews with employee and reviewer details
     */
    public function findAllWithDetails(int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT 
                    pr.*,
                    u1.full_name as employee_name,
                    u1.email as employee_email,
                    u2.full_name as reviewer_name,
                    u2.email as reviewer_email
                  FROM {$this->tableName} pr
                  LEFT JOIN users u1 ON pr.employee_id = u1.id
                  LEFT JOIN users u2 ON pr.reviewer_id = u2.id
                  ORDER BY pr.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($query, [$limit, $offset]);
    }

    /**
     * Get average rating by employee
     */
    public function getEmployeeAverageRating(int $employeeId): ?float
    {
        $query = "SELECT AVG(rating) as avg_rating 
                  FROM {$this->tableName} 
                  WHERE employee_id = ? AND rating IS NOT NULL AND status = 'completed'";
        
        $result = $this->db->fetchOne($query, [$employeeId]);
        return $result['avg_rating'] ? (float)$result['avg_rating'] : null;
    }

    /**
     * Get reviews for period
     */
    public function findByPeriod(string $startDate, string $endDate, int $limit = null): array
    {
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE review_period_start >= ? AND review_period_end <= ?
                  ORDER BY review_period_end DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
            $data = $this->db->fetchAll($query, [$startDate, $endDate, $limit]);
        } else {
            $data = $this->db->fetchAll($query, [$startDate, $endDate]);
        }

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }
}
