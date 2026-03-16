<?php

namespace HRManagement\Services;

use HRManagement\Models\CareerPath;
use HRManagement\Models\IndividualDevelopmentPlan;
use HRManagement\Repositories\CareerPathRepository;
use HRManagement\Repositories\IndividualDevelopmentPlanRepository;

/**
 * Career Development Service
 * 
 * Handles career paths and individual development plans
 */
class CareerDevelopmentService
{
    private CareerPathRepository $careerPathRepository;
    private IndividualDevelopmentPlanRepository $idpRepository;

    public function __construct()
    {
        $this->careerPathRepository = new CareerPathRepository();
        $this->idpRepository = new IndividualDevelopmentPlanRepository();
    }

    /**
     * Create a career path
     */
    public function createCareerPath(array $data, int $createdBy): int
    {
        $careerPath = new CareerPath();
        $careerPath->setName($data['name'])
            ->setDescription($data['description'] ?? '')
            ->setTargetPosition($data['target_position'] ?? '')
            ->setPrerequisites($data['prerequisites'] ?? '')
            ->setSkillsRequired($data['skills_required'] ?? [])
            ->setDurationMonths($data['duration_months'] ?? 12)
            ->setStatus($data['status'] ?? 'active')
            ->setCreatedBy($createdBy);

        return $this->careerPathRepository->create($careerPath);
    }

    /**
     * Update career path
     */
    public function updateCareerPath(int $pathId, array $data): bool
    {
        $careerPath = $this->careerPathRepository->findById($pathId);
        
        if (!$careerPath) {
            throw new \Exception('Career path not found');
        }

        $careerPath->setName($data['name'] ?? $careerPath->getName())
            ->setDescription($data['description'] ?? $careerPath->getDescription())
            ->setTargetPosition($data['target_position'] ?? $careerPath->getTargetPosition())
            ->setPrerequisites($data['prerequisites'] ?? $careerPath->getPrerequisites())
            ->setSkillsRequired($data['skills_required'] ?? $careerPath->getSkillsRequired())
            ->setDurationMonths($data['duration_months'] ?? $careerPath->getDurationMonths())
            ->setStatus($data['status'] ?? $careerPath->getStatus());

        return $this->careerPathRepository->update($careerPath);
    }

    /**
     * Get career path details with statistics
     */
    public function getPathDetails(int $pathId): ?array
    {
        $careerPath = $this->careerPathRepository->findById($pathId);
        
        if (!$careerPath) {
            return null;
        }

        $stats = $this->careerPathRepository->getPathStats($pathId);

        return array_merge($careerPath->toArray(), $stats ?? []);
    }

    /**
     * Create individual development plan
     */
    public function createIDP(int $userId, int $createdBy, array $data): int
    {
        $idp = new IndividualDevelopmentPlan();
        $idp->setUserId($userId)
            ->setCareerPathId($data['career_path_id'] ?? null)
            ->setStartDate($data['start_date'] ?? date('Y-m-d'))
            ->setEndDate($data['end_date'] ?? date('Y-m-d', strtotime('+1 year')))
            ->setObjectives($data['objectives'] ?? '')
            ->setMilestones($data['milestones'] ?? [])
            ->setStatus($data['status'] ?? 'active')
            ->setCreatedBy($createdBy);

        return $this->idpRepository->create($idp);
    }

    /**
     * Update IDP
     */
    public function updateIDP(int $idpId, array $data): bool
    {
        $idp = $this->idpRepository->findById($idpId);
        
        if (!$idp) {
            throw new \Exception('IDP not found');
        }

        $idp->setObjectives($data['objectives'] ?? $idp->getObjectives())
            ->setMilestones($data['milestones'] ?? $idp->getMilestones())
            ->setStatus($data['status'] ?? $idp->getStatus());

        if (isset($data['start_date'])) {
            $idp->setStartDate($data['start_date']);
        }
        if (isset($data['end_date'])) {
            $idp->setEndDate($data['end_date']);
        }

        return $this->idpRepository->update($idp);
    }

    /**
     * Get user's current development plan
     */
    public function getUserCurrentPlan(int $userId): ?array
    {
        $idp = $this->idpRepository->getUserCurrentPlan($userId);
        
        if (!$idp) {
            return null;
        }

        $details = $this->idpRepository->getWithDetails($idp->getId());
        return $details;
    }

    /**
     * Get user's all development plans
     */
    public function getUserPlans(int $userId): array
    {
        return $this->idpRepository->findByUser($userId);
    }

    /**
     * Get available career paths
     */
    public function getAvailableCareerPaths(int $limit = 20): array
    {
        return $this->careerPathRepository->findActive($limit);
    }

    /**
     * Search career paths
     */
    public function searchCareerPaths(string $searchTerm, int $limit = 20): array
    {
        return $this->careerPathRepository->search($searchTerm, $limit);
    }

    /**
     * Get IDPs by status
     */
    public function getIDPsByStatus(string $status): array
    {
        return $this->idpRepository->findByStatus($status);
    }

    /**
     * Delete career path with cascade
     */
    public function deleteCareerPath(int $pathId): bool
    {
        // Delete associated IDPs
        $idps = $this->idpRepository->findByCareerPath($pathId);
        foreach ($idps as $idp) {
            $this->idpRepository->delete($idp->getId());
        }

        return $this->careerPathRepository->delete($pathId);
    }

    /**
     * Complete IDP
     */
    public function completeIDP(int $idpId): bool
    {
        $idp = $this->idpRepository->findById($idpId);
        
        if (!$idp) {
            throw new \Exception('IDP not found');
        }

        $idp->setStatus('completed');
        return $this->idpRepository->update($idp);
    }
}
