<?php
require_once __DIR__ . '/../Core/Database.php';

class Employee extends Database
{
    protected $table = 'employees';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all(): array
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    employee_id,
                    full_name AS name,
                    address,
                    contact_number,
                    email,
                    department,
                    position,
                    date_hired,
                    employment_status
                FROM employees
                ORDER BY full_name ASC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching employees: " . $e->getMessage());
            return [];
        }
    }

    public function update(array $data): bool
    {
        $stmt = $this->conn->prepare("
        UPDATE employees
        SET 
            full_name = :full_name,
            contact_number = :contact_number,
            email = :email,
            department = :department,
            position = :position,
            employment_status = :employment_status,
            address = :address
        WHERE employee_id = :employee_id
    ");

        return $stmt->execute([
            'full_name' => $data['full_name'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],
            'department' => $data['department'],
            'position' => $data['position'],
            'employment_status' => $data['employment_status'],
            'address' => $data['address'],
            'employee_id' => $data['employee_id']
        ]);
    }
}
