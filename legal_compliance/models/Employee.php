<?php
/**
 * Employee Model
 * Handles employee-related database operations
 */

require_once __DIR__ . '/../../auth/database.php';

class Employee
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get employee by ID
     */
    public function getById(int $id): ?array
    {    
        try {
            $sql = "SELECT e.*, 
                           e.department as category_name,
                           e.position as position_name,
                           'Regular' as employment_type_name
                    FROM employees e
                    WHERE e.employee_id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result['first_name'] = $result['full_name'];
                $result['last_name'] = '';
            }

            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error getting employee by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get employee by employee number
     */
    public function getByEmployeeNo(string $employeeNo): ?array
    {
        try {
            $sql = "SELECT e.*, 
                           e.department as category_name,
                           e.position as position_name,
                           'Regular' as employment_type_name
                    FROM employees e
                    WHERE e.employee_no = :employee_no";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':employee_no' => $employeeNo]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $result['first_name'] = $result['full_name'];
                $result['last_name'] = '';
            }

            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error getting employee by number: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search employees
     */
    public function search(string $query, int $limit = 10): array
    {
        try {
            $sql = "SELECT MIN(e.employee_id) as id, e.employee_no, e.full_name as first_name, '' as last_name, 
                           e.department as category_name, e.position as position_name
                    FROM employees e
                    WHERE e.full_name LIKE :query 
                       OR e.employee_no LIKE :query
                    GROUP BY e.full_name, e.employee_no, e.department, e.position
                    ORDER BY e.full_name
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching employees: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all employees
     */
    public function getAll(int $limit = 100, int $offset = 0): array
    {
        try {
            $sql = "SELECT MIN(e.employee_id) as id, e.employee_no, e.full_name as first_name, '' as last_name, 
                           e.employment_status as status, e.department as category_name, e.position as position_name
                    FROM employees e
                    WHERE e.employment_status = 'active'
                    GROUP BY e.full_name, e.employee_no, e.department, e.position
                    ORDER BY e.full_name
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all employees: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get employees by category (department)
     */
    public function getByCategory(string $department): array
    {
        try {
            $sql = "SELECT MIN(e.employee_id) as id, e.employee_no, e.full_name as first_name, '' as last_name, 
                           e.department as category_name, e.position as position_name
                    FROM employees e
                    WHERE e.department = :department AND e.employment_status = 'active'
                    GROUP BY e.full_name, e.employee_no, e.department, e.position
                    ORDER BY e.full_name";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':department' => $department]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employees by category: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get HR/Admin employees
     */
    public function getHREmployees(): array
    {
        try {
            $sql = "SELECT MIN(e.employee_id) as id, e.employee_no, e.full_name as first_name, '' as last_name, 
                           e.department as category_name, e.position as position_name
                    FROM employees e
                    WHERE (e.position LIKE '%HR%' OR e.position LIKE '%Human Resource%' 
                           OR e.position LIKE '%Admin%' OR e.position LIKE '%Manager%'
                           OR e.department = 'HR' OR e.department = 'Human Resource')
                      AND e.employment_status = 'active'
                    GROUP BY e.full_name, e.employee_no, e.department, e.position
                    ORDER BY e.full_name";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting HR employees: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get employee's disciplinary history
     */
    public function getDisciplinaryHistory(int $employeeId): array
    {
        $sql = "SELECT da.*, i.incident_id, i.title as incident_title, i.incident_type
                FROM lc_disciplinary_actions da
                LEFT JOIN lc_incidents i ON da.incident_id = i.id
                WHERE da.employee_id = :employee_id
                ORDER BY da.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee's incident history (as respondent)
     */
    public function getIncidentHistory(int $employeeId): array
    {
        $sql = "SELECT i.*, 
                    COUNT(DISTINCT da.id) as disciplinary_count
                FROM lc_incidents i
                LEFT JOIN lc_disciplinary_actions da ON i.id = da.incident_id
                WHERE i.respondent_id = :employee_id
                GROUP BY i.id
                ORDER BY i.incident_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get categories
     */
    public function getCategories(): array
    {
        try {
            // Use the correct table name lc_employee_categories
            $sql = "SELECT * FROM lc_employee_categories ORDER BY name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get positions
     */
    public function getPositions(): array
    {
        try {
            $sql = "SELECT * FROM lc_positions ORDER BY position_name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If table doesn't exist, return empty array
            error_log("Error getting positions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get full name by ID
     */
    public function getFullName(int $id): string
    {
        $employee = $this->getById($id);
        if ($employee) {
            return $employee['full_name'];
        }
        return 'Unknown';
    }

    /**
     * Check if employee exists
     */
    public function exists(int $id): bool
    {
        $sql = "SELECT COUNT(*) as count FROM employees WHERE employee_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'] > 0;
    }

    /**
     * Get employee count
     */
    public function getActiveEmployeeCount(): int
    {
        try {
            $sql = "SELECT COUNT(DISTINCT full_name, employee_no, department, position) as count FROM employees WHERE employment_status = 'active'";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log("Error getting active employee count: " . $e->getMessage());
            return 0;
        }
    }
}
