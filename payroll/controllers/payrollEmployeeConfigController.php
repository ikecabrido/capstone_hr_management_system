<?php
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../models/payrollEmployeeConfigModel.php';

class PayrollEmployeeConfigController
{
    private PDO $db;
    private PayrollEmployeeConfigModel $model;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->model = new PayrollEmployeeConfigModel($this->db);
    }

    // Get all employees with config
    public function getAllEmployees(): array
    {
        return $this->model->getAllEmployeesWithConfig();
    }

    // Get single employee
    public function getEmployee(string $employeeId): ?array
    {
        return $this->model->getEmployeeConfig($employeeId);
    }

    // Save employee configuration
    public function saveEmployee(array $data): array
    {
        try {
            $employeeId = $data['employee_id'] ?? null;
            $baseSalary = (float)($data['base_salary'] ?? 0);
            $positionType = $data['position_type'] ?? 'Admin';
            $teacherQual = $data['teacher_qualification'] ?? 'ProfEd';
            $teachingUnits = (float)($data['teaching_units'] ?? 0);
            $hasSss = isset($data['has_sss']) && $data['has_sss'] == 1;
            $hasPhilHealth = isset($data['has_philhealth']) && $data['has_philhealth'] == 1;
            $hasPagIbig = isset($data['has_pagibig']) && $data['has_pagibig'] == 1;

            if (!$employeeId) {
                return ['success' => false, 'message' => 'Employee ID is required'];
            }

            // Save details
            $this->model->saveEmployeeDetails($employeeId, $baseSalary, $positionType, $teacherQual, $teachingUnits);

            // Save benefits
            $this->model->saveEmployeeBenefits($employeeId, $hasSss, $hasPhilHealth, $hasPagIbig);

            return ['success' => true, 'message' => 'Employee configuration saved successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Delete employee config
    public function deleteEmployee(string $employeeId): array
    {
        try {
            $this->model->deleteEmployeeConfig($employeeId);
            return ['success' => true, 'message' => 'Employee configuration deleted'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Get reference data
    public function getReferenceData(): array
    {
        return [
            'position_types' => $this->model->getPositionTypes(),
            'teacher_qualifications' => $this->model->getTeacherQualificationRates(),
            'summary' => $this->model->getConfigurationSummary()
        ];
    }

    // Get position rates
    public function getPositionRates(string $positionType): ?array
    {
        return $this->model->getPositionRates($positionType);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $controller = new PayrollEmployeeConfigController();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $result = $controller->saveEmployee($_POST);
        echo json_encode($result);
    } elseif ($action === 'delete') {
        $result = $controller->deleteEmployee($_POST['employee_id'] ?? '');
        echo json_encode($result);
    } elseif ($action === 'get_employee') {
        $employee = $controller->getEmployee($_POST['employee_id'] ?? '');
        echo json_encode(['employee' => $employee]);
    }
    exit;
}
