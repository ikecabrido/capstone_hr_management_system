<?php

namespace HRManagement\Services;

use HRManagement\Models\TrainingProgram;
use HRManagement\Models\TrainingEnrollment;
use HRManagement\Repositories\TrainingProgramRepository;
use HRManagement\Repositories\TrainingEnrollmentRepository;

/**
 * Training Service
 * 
 * Handles training program and enrollment operations
 */
class TrainingService
{
    private TrainingProgramRepository $programRepository;
    private TrainingEnrollmentRepository $enrollmentRepository;

    public function __construct()
    {
        $this->programRepository = new TrainingProgramRepository();
        $this->enrollmentRepository = new TrainingEnrollmentRepository();
    }

    /**
     * Create a new training program
     */
    public function createProgram(array $data, int $createdBy): int
    {
        $program = new TrainingProgram();
        $program->setName($data['name'])
            ->setDescription($data['description'] ?? '')
            ->setCategory($data['category'] ?? 'General')
            ->setType($data['type'] ?? '')
            ->setDuration($data['duration'] ?? 0)
            ->setCreatedBy($createdBy)
            ->setStatus($data['status'] ?? 'active');

        return $this->programRepository->create($program);
    }

    /**
     * Update training program
     */
    public function updateProgram(int $programId, array $data): bool
    {
        $program = $this->programRepository->findById($programId);
        
        if (!$program) {
            throw new \Exception('Program not found');
        }

        $program->setName($data['name'] ?? $program->getName())
            ->setDescription($data['description'] ?? $program->getDescription())
            ->setCategory($data['category'] ?? $program->getCategory())
            ->setType($data['type'] ?? $program->getType())
            ->setDuration($data['duration'] ?? $program->getDuration())
            ->setStatus($data['status'] ?? $program->getStatus());

        return $this->programRepository->update($program);
    }

    /**
     * Get program details
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
     * Enroll user in program
     */
    public function enrollUser(int $userId, int $programId, ?string $startDate = null): int
    {
        // Check if already enrolled
        if ($this->enrollmentRepository->isEnrolled($userId, $programId)) {
            throw new \Exception('User already enrolled in this program');
        }

        $enrollment = new TrainingEnrollment();
        $enrollment->setUserId($userId)
            ->setProgramId($programId)
            ->setStatus('pending')
            ->setProgressPercentage(0)
            ->setStartDate($startDate);

        return $this->enrollmentRepository->create($enrollment);
    }

    /**
     * Update enrollment progress
     */
    public function updateEnrollmentProgress(int $enrollmentId, int $percentage, string $status, ?float $score = null): bool
    {
        $enrollment = $this->enrollmentRepository->findById($enrollmentId);
        
        if (!$enrollment) {
            throw new \Exception('Enrollment not found');
        }

        $enrollment->setProgressPercentage($percentage)
            ->setStatus($status);

        if ($score !== null) {
            $enrollment->setScore($score);
        }

        if ($status === 'completed') {
            $enrollment->setCompletionDate(date('Y-m-d H:i:s'))
                ->setCertificateIssued(true);
        }

        return $this->enrollmentRepository->update($enrollment);
    }

    /**
     * Remove user from program
     */
    public function unenrollUser(int $userId, int $programId): bool
    {
        $enrollment = $this->enrollmentRepository->getUserEnrollment($userId, $programId);
        
        if (!$enrollment) {
            throw new \Exception('Enrollment not found');
        }

        return $this->enrollmentRepository->delete($enrollment->getId());
    }

    /**
     * Get user's enrolled programs
     */
    public function getUserEnrollments(int $userId, int $limit = null): array
    {
        return $this->enrollmentRepository->findByUser($userId, $limit);
    }

    /**
     * Get program enrollments
     */
    public function getProgramEnrollments(int $programId): array
    {
        return $this->enrollmentRepository->findByProgram($programId);
    }

    /**
     * Get available programs for enrollment
     */
    public function getAvailablePrograms(int $limit = 20): array
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
     * Delete program (with cascade)
     */
    public function deleteProgram(int $programId): bool
    {
        // Delete all enrollments first
        $enrollments = $this->enrollmentRepository->findByProgram($programId);
        foreach ($enrollments as $enrollment) {
            $this->enrollmentRepository->delete($enrollment->getId());
        }

        return $this->programRepository->delete($programId);
    }

    /**
     * Get completion statistics
     */
    public function getProgramCompletionStats(int $programId): array
    {
        $stats = $this->programRepository->getProgramStats($programId);
        $completionRate = $this->enrollmentRepository->getCompletionRate($programId);

        return [
            'total_enrollments' => $stats['total_enrollments'] ?? 0,
            'completed_count' => $stats['completed_count'] ?? 0,
            'avg_progress' => $stats['avg_progress'] ?? 0,
            'completion_rate' => $completionRate,
        ];
    }
}
