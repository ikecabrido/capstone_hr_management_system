<?php

namespace HRManagement\Repositories;

use HRManagement\Models\IndividualDevelopmentPlan;

/**
 * Individual Development Plan Repository
 */
class IndividualDevelopmentPlanRepository extends BaseRepository
{
    protected string $tableName = 'individual_development_plans';
    protected string $modelClass = IndividualDevelopmentPlan::class;

    /**
     * Find IDPs by user
     */
    public function findByUser(int $userId, int $limit = null): array
    {
        return $this->findBy(['user_id' => $userId], $limit);
    }

    /**
     * Find IDPs by career path
     */
    public function findByCareerPath(int $careerPathId, int $limit = null): array
    {
        return $this->findBy(['career_path_id' => $careerPathId], $limit);
    }

    /**
     * Find IDPs by status
     */
    public function findByStatus(string $status, int $limit = null): array
    {
        return $this->findBy(['status' => $status], $limit);
    }

    /**
     * Get active IDPs for user
     */
    public function findActiveForUser(int $userId): array
    {
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE user_id = ? AND status = 'active'
                  ORDER BY created_at DESC";
        
        $data = $this->db->fetchAll($query, [$userId]);

        $models = [];
        foreach ($data as $row) {
            $model = new $this->modelClass();
            $models[] = $model->fromArray($row);
        }

        return $models;
    }

    /**
     * Get IDP with details
     */
    public function getWithDetails(int $idpId): ?array
    {
        $query = "SELECT 
                    idp.*,
                    u.full_name as user_name,
                    u.email as user_email,
                    cp.name as career_path_name
                  FROM {$this->tableName} idp
                  LEFT JOIN users u ON idp.user_id = u.id
                  LEFT JOIN career_paths cp ON idp.career_path_id = cp.id
                  WHERE idp.id = ?";
        
        return $this->db->fetchOne($query, [$idpId]);
    }

    /**
     * Get user's current IDP
     */
    public function getUserCurrentPlan(int $userId): ?IndividualDevelopmentPlan
    {
        $query = "SELECT * FROM {$this->tableName} 
                  WHERE user_id = ? AND status = 'active'
                  ORDER BY created_at DESC
                  LIMIT 1";
        
        $data = $this->db->fetchOne($query, [$userId]);
        
        if (!$data) {
            return null;
        }

        $model = new $this->modelClass();
        return $model->fromArray($data);
    }
}
