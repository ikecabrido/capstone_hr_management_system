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
        try {
            // Query actual data from database
            $db = $this->model->getConnection();

            // Count pending resignations
            $stmt = $db->query("SELECT COUNT(*) as count FROM exit_resignations WHERE status = 'pending'");
            $pendingResignations = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Count scheduled interviews
            $stmt = $db->query("SELECT COUNT(*) as count FROM exit_interviews WHERE status = 'scheduled'");
            $scheduledInterviews = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Count active transfers
            $stmt = $db->query("SELECT COUNT(*) as count FROM exit_knowledge_transfer_plans WHERE status = 'active'");
            $activeTransfers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Count pending settlements
            $stmt = $db->query("SELECT COUNT(*) as count FROM exit_employee_settlements WHERE status = 'draft'");
            $pendingSettlements = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Count total active employees
            $stmt = $db->query("SELECT COUNT(*) as count FROM users");
            $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            return [
                'total_employees' => $totalEmployees,
                'pending_resignations' => $pendingResignations,
                'scheduled_interviews' => $scheduledInterviews,
                'active_transfers' => $activeTransfers,
                'pending_settlements' => $pendingSettlements,
                'incomplete_documentation' => 0
            ];
        } catch (Exception $e) {
            // Return default stats if query fails
            return [
                'total_employees' => 0,
                'pending_resignations' => 0,
                'scheduled_interviews' => 0,
                'active_transfers' => 0,
                'pending_settlements' => 0,
                'incomplete_documentation' => 0
            ];
        }
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
     * Get recent resignations
     */
    public function getRecentResignations(int $limit = 10): array
    {
        try {
            $db = $this->model->getConnection();
            $query = "SELECT r.*, 
                             e.full_name AS full_name,
                             e.department AS department,
                             e.email AS employee_email,
                             p.full_name AS preclearance_desk_person_name,
                             DATEDIFF(r.last_working_date, CURDATE()) AS days_left
                      FROM exit_resignations r
                      LEFT JOIN employees e ON r.employee_id = e.employee_id
                      LEFT JOIN users p ON r.preclearance_desk_person = p.id
                      ORDER BY r.id DESC
                      LIMIT ?";

            $stmt = $db->prepare($query);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            // Fallback for databases that do not have preclearance_desk_person
            try {
                $query = "SELECT r.*, 
                                 e.full_name AS full_name,
                                 e.department,
                                 e.email,
                                 DATEDIFF(r.last_working_date, CURDATE()) AS days_left
                          FROM exit_resignations r
                          LEFT JOIN employees e ON r.employee_id = e.employee_id
                          ORDER BY r.id DESC
                          LIMIT ?";

                $stmt = $db->prepare($query);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

                // add placeholder for preclearance
                foreach ($results as &$row) {
                    if (!isset($row['preclearance_desk_person_name'])) {
                        $row['preclearance_desk_person_name'] = null;
                    }

                    if (!isset($row['full_name']) && isset($row['employee_name'])) {
                        $row['full_name'] = $row['employee_name'];
                    }

                    if (!isset($row['department']) && isset($row['department_id'])) {
                        $row['department'] = $row['department_id'];
                    }
                }

                return $results;
            } catch (Exception $e2) {
                error_log('ExitManagementController::getRecentResignations error: ' . $e2->getMessage());
                return [];
            }
        }
    }

    /**
     * Get resignation trend data (last 6 months)
     */
    public function getResignationTrend(): array
    {
        try {
            $db = $this->model->getConnection();
            $query = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                      FROM exit_resignations
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY month";
            
            $stmt = $db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $months = [];
            $counts = [];
            foreach ($results as $row) {
                $months[] = $row['month'];
                $counts[] = (int)$row['count'];
            }
            
            return [
                'labels' => $months,
                'data' => $counts
            ];
        } catch (Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get resignation reasons distribution
     */
    public function getResignationReasons(): array
    {
        try {
            $db = $this->model->getConnection();
            $query = "SELECT reason, COUNT(*) as count
                      FROM exit_resignations
                      WHERE reason IS NOT NULL AND reason != ''
                      GROUP BY reason
                      ORDER BY count DESC";
            
            $stmt = $db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $reasons = [];
            $counts = [];
            foreach ($results as $row) {
                $reasons[] = $row['reason'];
                $counts[] = (int)$row['count'];
            }
            
            return [
                'labels' => $reasons,
                'data' => $counts
            ];
        } catch (Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get exit status distribution
     */
    public function getExitStatusDistribution(): array
    {
        try {
            $db = $this->model->getConnection();
            $query = "SELECT status, COUNT(*) as count
                      FROM exit_resignations
                      GROUP BY status";
            
            $stmt = $db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $statuses = [];
            $counts = [];
            foreach ($results as $row) {
                $statuses[] = ucfirst($row['status']);
                $counts[] = (int)$row['count'];
            }
            
            return [
                'labels' => $statuses,
                'data' => $counts
            ];
        } catch (Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get resignation type distribution
     */
    public function getResignationTypeDistribution(): array
    {
        try {
            $db = $this->model->getConnection();
            $query = "SELECT resignation_type, COUNT(*) as count
                      FROM exit_resignations
                      WHERE resignation_type IS NOT NULL AND resignation_type != ''
                      GROUP BY resignation_type
                      ORDER BY count DESC";
            
            $stmt = $db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $types = [];
            $counts = [];
            foreach ($results as $row) {
                $types[] = ucfirst($row['resignation_type']);
                $counts[] = (int)$row['count'];
            }
            
            return [
                'labels' => $types,
                'data' => $counts
            ];
        } catch (Exception $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Get dashboard metrics
     */
    public function getDashboardMetrics(): array
    {
        try {
            $db = $this->model->getConnection();
            
            // Total exited this year
            $stmt = $db->query("SELECT COUNT(*) as count FROM exit_resignations WHERE YEAR(last_working_date) = YEAR(NOW())");
            $totalExited = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Average notice period
            $stmt = $db->query("SELECT AVG(DATEDIFF(last_working_date, notice_date)) as avg_days FROM exit_resignations WHERE last_working_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)");
            $avgNotice = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_days'] ?? 0);
            
            // Top resignation reason
            $stmt = $db->query("SELECT reason FROM exit_resignations WHERE reason IS NOT NULL AND reason != '' GROUP BY reason ORDER BY COUNT(*) DESC LIMIT 1");
            $topReason = $stmt->fetch(PDO::FETCH_ASSOC)['reason'] ?? 'N/A';
            
            // Interviews completion rate
            $stmt = $db->query("SELECT COUNT(DISTINCT r.id) as total FROM exit_resignations r WHERE r.status IN ('approved', 'completed')");
            $totalResignations = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $stmt = $db->query("SELECT COUNT(DISTINCT ei.employee_id) as count FROM exit_interviews ei WHERE ei.status IN ('completed', 'scheduled')");
            $completedInterviews = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            $interviewRate = $totalResignations > 0 ? round(($completedInterviews / $totalResignations) * 100) : 0;
            
            return [
                'total_exited' => $totalExited,
                'avg_notice' => $avgNotice,
                'top_reason' => $topReason,
                'interview_rate' => $interviewRate
            ];
        } catch (Exception $e) {
            return [
                'total_exited' => 0,
                'avg_notice' => 0,
                'top_reason' => 'N/A',
                'interview_rate' => 0
            ];
        }
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

                case 'get_employees_with_resignations':
                    return $this->model->getEmployeesWithResignations();

                case 'get_employee_salary_components':
                    return $this->model->getEmployeeSalaryComponents($data['employee_id'] ?? '');

                case 'get_eligible_interviewers':
                    return $this->model->getEligibleInterviewers();

                case 'get_recent_resignations':
                    return $this->getRecentResignations($data['limit'] ?? 10);

                case 'get_resignation_trend':
                    return $this->getResignationTrend();

                case 'get_resignation_reasons':
                    return $this->getResignationReasons();

                case 'get_exit_status':
                    return $this->getExitStatusDistribution();

                case 'get_resignation_types':
                    return $this->getResignationTypeDistribution();

                case 'get_dashboard_metrics':
                    return $this->getDashboardMetrics();

                default:
                    return ['error' => 'Unknown action'];
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}