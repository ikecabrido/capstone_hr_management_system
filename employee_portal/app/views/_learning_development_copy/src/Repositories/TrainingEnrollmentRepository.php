<?php

namespace HRManagement\Repositories;

use HRManagement\Models\TrainingEnrollment;

/**
 * Training Enrollment Repository
 */
class TrainingEnrollmentRepository extends BaseRepository
{
    protected string $tableName = 'training_enrollments';
    protected string $modelClass = TrainingEnrollment::class;

    /**
     * Find enrollments by user
     */
    public function findByUser(int $userId, int $limit = null): array
    {
        return $this->findBy(['user_id' => $userId], $limit);
    }

    /**
     * Find enrollments by program
     */
    public function findByProgram(int $programId, int $limit = null): array
    {
        return $this->findBy(['program_id' => $programId], $limit);
    }

    /**
     * Find enrollments by status
     */
    public function findByStatus(string $status, int $limit = null): array
    {
        return $this->findBy(['status' => $status], $limit);
    }

    /**
     * Check if user is enrolled in program
     */
    public function isEnrolled(int $userId, int $programId): bool
    {
        return $this->findOneBy([
            'user_id' => $userId,
            'program_id' => $programId
        ]) !== null;
    }

    /**
     * Get user's program enrollment (if exists)
     */
    public function getUserEnrollment(int $userId, int $programId): ?TrainingEnrollment
    {
        return $this->findOneBy([
            'user_id' => $userId,
            'program_id' => $programId
        ]);
    }

    /**
     * Get enrollments with user and program details
     */
    public function findWithDetails(int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT 
                    te.*,
                    u.full_name as user_name,
                    u.email as user_email,
                    tp.name as program_name,
                    tp.category as program_category
                  FROM {$this->tableName} te
                  LEFT JOIN users u ON te.user_id = u.id
                  LEFT JOIN training_programs tp ON te.program_id = tp.id
                  ORDER BY te.enrollment_date DESC
                  LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($query, [$limit, $offset]);
    }

    /**
     * Get completion rate for a program
     */
    public function getCompletionRate(int $programId): float
    {
        $query = "SELECT 
                    CAST(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS FLOAT) / COUNT(*) * 100 as rate
                  FROM {$this->tableName}
                  WHERE program_id = ? AND status IN ('completed', 'in_progress')";
        
        $result = $this->db->fetchOne($query, [$programId]);
        return $result['rate'] ?? 0;
    }
}
