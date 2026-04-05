<?php
require_once __DIR__ . '/../config/Database.php';

class Payslip
{
    private $conn;
    private $table = 'pr_payslips';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function all()
    {
        $query = "SELECT p.*, full_name
              FROM {$this->table} p
              JOIN employees e ON p.employee_id = e.id
              ORDER BY p.payslip_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $query = "SELECT p.*, 
                     CONCAT(e.first_name, ' ', e.last_name) AS full_name
              FROM {$this->table} p
              JOIN employees e ON p.employee_id = e.id
              WHERE p.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmployee($id)
    {
        $query = "SELECT p.*, e.full_name
              FROM {$this->table} p
              JOIN employees e ON p.employee_id = e.id
              WHERE p.employee_id = ?
              ORDER BY p.payslip_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function viewPayslip($id)
    {
        $query = "
        SELECT p.*, 
               e.full_name,  
               pos.title AS position, 
               et.name AS employment_type
        FROM {$this->table} p
        JOIN employees e ON p.employee_id = e.id
        LEFT JOIN positions pos ON e.position_id = pos.id
        LEFT JOIN employment_types et ON e.employment_type_id = et.id
        WHERE p.payslip_id = :id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $payslip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payslip) return false;

        $itemsQuery = "
    SELECT description, amount
    FROM payslip_items
    WHERE payslip_id = :id
";

        $stmt = $this->conn->prepare($itemsQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $payslip['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $payslip;
    }








    // public function create($data)
    // {
    //     $query = "INSERT INTO {$this->table} 
    //               (employee_id, leave_type_id, start_date, end_date, reason, status) 
    //               VALUES (?, ?, ?, ?, ?, ?)";
    //     $stmt = $this->conn->prepare($query);
    //     return $stmt->execute([
    //         $data['employee_id'],
    //         $data['leave_type_id'],
    //         $data['start_date'],
    //         $data['end_date'],
    //         $data['reason'],
    //         $data['status'] ?? 'Pending'
    //     ]);
    // }

    // public function update($id, $data)
    // {
    //     $query = "UPDATE {$this->table} SET
    //               employee_id = ?,
    //               leave_type_id = ?,
    //               start_date = ?,
    //               end_date = ?,
    //               reason = ?,
    //               status = ?
    //               WHERE id = ?";
    //     $stmt = $this->conn->prepare($query);
    //     return $stmt->execute([
    //         $data['employee_id'],
    //         $data['leave_type_id'],
    //         $data['start_date'],
    //         $data['end_date'],
    //         $data['reason'],
    //         $data['status'],
    //         $id
    //     ]);
    // }

    // public function delete($id)
    // {
    //     $query = "DELETE FROM {$this->table} WHERE id = ?";
    //     $stmt = $this->conn->prepare($query);
    //     return $stmt->execute([$id]);
    // }
}
