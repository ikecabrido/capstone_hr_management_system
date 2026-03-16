<?php

require_once __DIR__ . '/../models/ExitManagementModel.php';

class ExitManagementController
{
    protected ExitManagementModel $model;

    public function __construct()
    {
        $this->model = new ExitManagementModel();
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        // This would aggregate stats from all models
        // For now, return basic structure
        return [
            'total_employees' => 0,
            'pending_resignations' => 0,
            'scheduled_interviews' => 0,
            'active_transfers' => 0,
            'pending_settlements' => 0,
            'incomplete_documentation' => 0
        ];
    }

    /**
     * Get employee exit summary
     */
    public function getEmployeeExitSummary(int $employeeId): array
    {
        $employee = $this->model->getEmployeeById($employeeId);

        if (!$employee) {
            return ['error' => 'Employee not found'];
        }

        return [
            'employee' => $employee,
            'resignations' => [], // Would be populated from ResignationModel
            'interviews' => [], // Would be populated from ExitInterviewModel
            'transfers' => [], // Would be populated from KnowledgeTransferModel
            'settlements' => [], // Would be populated from SettlementModel
            'documents' => [], // Would be populated from DocumentationModel
            'surveys' => [] // Would be populated from SurveyModel
        ];
    }

    /**
     * Get eligible employees for exit management
     */
    public function getEligibleEmployees(): array
    {
        return $this->model->getEligibleEmployees();
    }

    /**
     * Handle AJAX requests
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        try {
            switch ($action) {
                case 'get_employee_details':
                    return $this->getEmployeeExitSummary($data['employee_id'] ?? 0);

                case 'get_dashboard_stats':
                    return $this->getDashboardStats();

                case 'get_eligible_employees':
                    return $this->getEligibleEmployees();

                default:
                    return ['error' => 'Unknown action'];
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}