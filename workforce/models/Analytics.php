<?php
/**
 * Analytics Model Class
 * Handles analytics and reporting operations
 */

require_once __DIR__ . '/../config/Database.php';

class Analytics {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get dashboard metrics
     */
    public function getDashboardMetrics() {
        $metrics = [];

        // Total employees (active)
        $query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'";
        $metrics['total_employees'] = $this->db->fetchOne($query)['count'];

        // Total teachers/faculty
        $query = "SELECT COUNT(*) as count FROM employees WHERE (position LIKE '%Teacher%' OR position LIKE '%Faculty%') AND employment_status = 'Active'";
        $metrics['total_teachers'] = $this->db->fetchOne($query)['count'];

        // Total staff
        $query = "SELECT COUNT(*) as count FROM employees WHERE (position NOT LIKE '%Teacher%' AND position NOT LIKE '%Faculty%') AND employment_status = 'Active'";
        $metrics['total_staff'] = $this->db->fetchOne($query)['count'];

        // New hires this year
        $currentYear = date('Y');
        $query = "SELECT COUNT(*) as count FROM employees WHERE YEAR(date_hired) = ? AND employment_status = 'Active'";
        $metrics['new_hires'] = $this->db->fetchOne($query, [$currentYear], 'i')['count'];

        // Average performance
        $query = "SELECT AVG(rating) as avg_performance FROM performance_reviews WHERE rating IS NOT NULL AND status = 'completed'";
        $perfData = $this->db->fetchOne($query);
        $metrics['avg_performance'] = round($perfData['avg_performance'] ?? 0, 2);

        // Total active reviews
        $query = "SELECT COUNT(*) as count FROM performance_reviews WHERE status = 'completed'";
        $metrics['total_reviews'] = $this->db->fetchOne($query)['count'];

        return $metrics;
    }

    /**
     * Get department-wise distribution
     */
    public function getDepartmentDistribution() {
        $query = "SELECT department, COUNT(*) as count FROM employees WHERE employment_status = 'Active' AND department IS NOT NULL GROUP BY department ORDER BY count DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get gender distribution
     */
    public function getGenderDistribution() {
        // hr_management doesn't have gender field, so return empty or department-based
        $query = "SELECT department as category, COUNT(*) as count FROM employees WHERE employment_status = 'Active' GROUP BY department LIMIT 5";
        return $this->db->fetchAll($query);
    }

    /**
     * Get age group distribution
     */
    public function getAgeGroupDistribution() {
        // hr_management doesn't have age field, so return by hiring year instead
        $query = "SELECT 
                    CASE 
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) < 1 THEN '< 1 year'
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) BETWEEN 1 AND 3 THEN '1-3 years'
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) BETWEEN 4 AND 7 THEN '4-7 years'
                        ELSE '8+ years'
                    END as tenure_group,
                    COUNT(*) as count
                FROM employees
                WHERE employment_status = 'Active' AND date_hired IS NOT NULL
                GROUP BY tenure_group
                ORDER BY tenure_group ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get attrition data for chart
     */
    public function getAttritionData($year = null) {
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT 
                    DATE_FORMAT(notice_date, '%Y-%m') as month,
                    resignation_type,
                    COUNT(*) as count
                FROM resignations
                WHERE YEAR(notice_date) = ? AND status IN ('approved', 'pending')
                GROUP BY month, resignation_type
                ORDER BY month ASC";
        
        return $this->db->fetchAll($query, [$year], 'i');
    }

    /**
     * Get attrition rate
     */
    public function getAttritionRate($year = null) {
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'";
        $activeCount = $this->db->fetchOne($query)['count'];

        $query = "SELECT COUNT(*) as count FROM resignations WHERE YEAR(notice_date) = ? AND status IN ('approved')";
        $resignedCount = $this->db->fetchOne($query, [$year], 'i')['count'];

        if ($activeCount == 0) return 0;
        return round(($resignedCount / $activeCount) * 100, 2);
    }

    /**
     * Get separated employees
     */
    public function getSeparatedEmployees() {
        $query = "SELECT 
                    r.employee_id,
                    e.full_name as name,
                    e.position,
                    e.department,
                    r.notice_date as separation_date,
                    r.resignation_type as employment_status
                FROM resignations r
                JOIN employees e ON r.employee_id = e.employee_id
                WHERE r.status IN ('approved')
                ORDER BY r.notice_date DESC
                LIMIT 50";
        return $this->db->fetchAll($query);
    }

    /**
     * Get employees at risk
     */
    public function getEmployeesAtRisk() {
        // hr_management doesn't have absence_days or performance_score in employees table
        // Using performance_reviews table instead
        $query = "SELECT 
                    e.employee_id as id,
                    e.full_name as name,
                    e.department,
                    e.position,
                    COALESCE(pr.rating, 0) as performance_score,
                    YEAR(CURDATE()) - YEAR(e.date_hired) as tenure_years,
                    CASE 
                        WHEN pr.rating < 2.5 THEN 'High'
                        WHEN pr.rating < 3.5 THEN 'Medium'
                        ELSE 'Low'
                    END as risk_level
                FROM employees e
                LEFT JOIN performance_reviews pr ON e.employee_id = pr.employee_id OR e.user_id = pr.employee_id
                WHERE e.employment_status = 'Active'
                AND pr.rating IS NOT NULL
                AND pr.rating < 3.5
                ORDER BY risk_level DESC";
        
        return $this->db->fetchAll($query);
    }

    /**
     * Get performance distribution
     */
    public function getPerformanceDistribution() {
        $query = "SELECT 
                    CASE 
                        WHEN rating >= 4.5 THEN 'Excellent (4.5+)'
                        WHEN rating >= 4 THEN 'Very Good (4.0-4.5)'
                        WHEN rating >= 3 THEN 'Good (3.0-3.9)'
                        WHEN rating >= 2 THEN 'Fair (2.0-2.9)'
                        ELSE 'Poor (<2.0)'
                    END as performance_level,
                    COUNT(*) as count
                FROM performance_reviews
                WHERE rating IS NOT NULL AND status = 'completed'
                GROUP BY performance_level
                ORDER BY rating DESC";
        
        return $this->db->fetchAll($query);
    }

    /**
     * Generate custom report
     */
    public function generateCustomReport($filters = []) {
        $query = "SELECT * FROM employees WHERE employment_status = 'Active'";
        $params = [];
        $types = '';

        // Apply filters
        if (!empty($filters['department'])) {
            $query .= " AND department = ?";
            $params[] = $filters['department'];
            $types .= 's';
        }

        if (!empty($filters['employment_type'])) {
            $query .= " AND employment_status = ?";
            $params[] = $filters['employment_type'];
            $types .= 's';
        }

        if (!empty($filters['hire_date_from'])) {
            $query .= " AND date_hired >= ?";
            $params[] = $filters['hire_date_from'];
            $types .= 's';
        }

        if (!empty($filters['hire_date_to'])) {
            $query .= " AND date_hired <= ?";
            $params[] = $filters['hire_date_to'];
            $types .= 's';
        }

        $query .= " ORDER BY full_name ASC";

        if (empty($params)) {
            return $this->db->fetchAll($query);
        }

        return $this->db->fetchAll($query, $params, $types);
    }

    /**
     * Get salary statistics
     */
    public function getSalaryStatistics() {
        // hr_management doesn't have salary in employees table
        // Return department statistics instead
        $query = "SELECT 
                    department,
                    COUNT(*) as count,
                    COUNT(DISTINCT position) as positions
                FROM employees
                WHERE employment_status = 'Active' AND department IS NOT NULL
                GROUP BY department
                ORDER BY count DESC";
        
        return $this->db->fetchAll($query);
    }

    /**
     * Get tenure distribution
     */
    public function getTenureDistribution() {
        $query = "SELECT 
                    CASE 
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) < 1 THEN '< 1 year'
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) BETWEEN 1 AND 3 THEN '1-3 years'
                        WHEN YEAR(CURDATE()) - YEAR(date_hired) BETWEEN 4 AND 7 THEN '4-7 years'
                        ELSE '8+ years'
                    END as tenure_range,
                    COUNT(*) as count
                FROM employees
                WHERE employment_status = 'Active' AND date_hired IS NOT NULL
                GROUP BY tenure_range
                ORDER BY tenure_range ASC";
        
        return $this->db->fetchAll($query);
    }
}

?>
