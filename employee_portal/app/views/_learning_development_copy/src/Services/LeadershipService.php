<?php

namespace HRManagement\Services;

use HRManagement\Models\LeadershipProgram;
use HRManagement\Repositories\LeadershipProgramRepository;
use HRManagement\Repositories\Database;

/**
 * Leadership Development Service
 * 
 * Handles leadership programs and enrollments
 */
class LeadershipService
{
    private LeadershipProgramRepository $programRepository;
    private Database $db;

    public function __construct()
    {
        $this->programRepository = new LeadershipProgramRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Create leadership program
     */
    public function createProgram(array $data, int $createdBy): int
    {
        $program = new LeadershipProgram();
        $program->setName($data['name'])
            ->setDescription($data['description'] ?? '')
            ->setLevel($data['level'] ?? '')
            ->setFocusArea($data['focus_area'] ?? '')
            ->setDurationWeeks($data['duration_weeks'] ?? 0)
            ->setTargetAudience($data['target_audience'] ?? '')
            ->setOutcomes($data['outcomes'] ?? [])
            ->setCreatedBy($createdBy)
            ->setStatus($data['status'] ?? 'active');

        return $this->programRepository->create($program);
    }

    /**
     * Update leadership program
     */
    public function updateProgram(int $programId, array $data): bool
    {
        $program = $this->programRepository->findById($programId);
        
        if (!$program) {
            throw new \Exception('Program not found');
        }

        $program->setName($data['name'] ?? $program->getName())
            ->setDescription($data['description'] ?? $program->getDescription())
            ->setLevel($data['level'] ?? $program->getLevel())
            ->setFocusArea($data['focus_area'] ?? $program->getFocusArea())
            ->setDurationWeeks($data['duration_weeks'] ?? $program->getDurationWeeks())
            ->setTargetAudience($data['target_audience'] ?? $program->getTargetAudience())
            ->setOutcomes($data['outcomes'] ?? $program->getOutcomes())
            ->setStatus($data['status'] ?? $program->getStatus());

        return $this->programRepository->update($program);
    }

    /**
     * Get program details with statistics
     */
    public function getProgramDetails(int $programId): ?array
    {
        $program = $this->programRepository->findById($programId);
        
        if (!$program) {
            return null;
        }

        $stats = $this->programRepository->getProgramStats($programId);

        return array_merge($program->toArray(), $stats ?? []);
    }

    /**
     * Enroll user in leadership program
     */
    public function enrollUser(int $userId, int $programId, ?string $startDate = null): int
    {
        // Check if already enrolled
        $query = "SELECT id FROM leadership_enrollments WHERE user_id = ? AND program_id = ?";
        $existing = $this->db->fetchOne($query, [$userId, $programId]);
        
        if ($existing) {
            throw new \Exception('User already enrolled in this program');
        }

        $query = "INSERT INTO leadership_enrollments (user_id, program_id, start_date, status) VALUES (?, ?, ?, ?)";
        $this->db->execute($query, [$userId, $programId, $startDate, 'pending']);
        
        return $this->db->lastInsertId();
    }

    /**
     * Complete enrollment
     */
    public function completeEnrollment(int $enrollmentId, string $feedback = ''): bool
    {
        $query = "UPDATE leadership_enrollments SET status = ?, completion_date = ?, feedback = ? WHERE id = ?";
        $this->db->execute($query, ['completed', date('Y-m-d H:i:s'), $feedback, $enrollmentId]);
        return true;
    }

    /**
     * Remove user from program
     */
    public function unenrollUser(int $userId, int $programId): bool
    {
        $query = "DELETE FROM leadership_enrollments WHERE user_id = ? AND program_id = ?";
        $this->db->execute($query, [$userId, $programId]);
        return true;
    }

    /**
     * Get user's enrolled programs
     */
    public function getUserEnrollments(int $userId): array
    {
        $query = "SELECT le.*, lp.name, lp.level, lp.duration_weeks
                  FROM leadership_enrollments le
                  LEFT JOIN leadership_programs lp ON le.program_id = lp.id
                  WHERE le.user_id = ?
                  ORDER BY le.enrollment_date DESC";
        
        return $this->db->fetchAll($query, [$userId]);
    }

    /**
     * Get active programs
     */
    public function getActivePrograms(int $limit = 20): array
    {
        return $this->programRepository->findActive($limit);
    }

    /**
     * Search programs
     */
    public function searchPrograms(string $searchTerm, int $limit = 20): array
    {
        return $this->programRepository->search($searchTerm, $limit);
    }

    /**
     * Get programs by level
     */
    public function getProgramsByLevel(string $level): array
    {
        return $this->programRepository->findByLevel($level);
    }

    /**
     * Delete program with cascade
     */
    public function deleteProgram(int $programId): bool
    {
        // Delete enrollments
        $query = "DELETE FROM leadership_enrollments WHERE program_id = ?";
        $this->db->execute($query, [$programId]);

        return $this->programRepository->delete($programId);
    }

    /**
     * Deactivate program
     */
    public function deactivateProgram(int $programId): bool
    {
        $program = $this->programRepository->findById($programId);
        
        if (!$program) {
            throw new \Exception('Program not found');
        }

        $program->setStatus('inactive');
        return $this->programRepository->update($program);
    }
}
