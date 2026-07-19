<?php
require_once __DIR__ . '/../config/Database.php';

class BenefitsAndGovContrib
{
    private $conn;
    private $table = "ep_employee_benefits";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all records (Admin)
     */
public function all()
{
    $sql = "SELECT
                b.*,
                e.full_name
            FROM {$this->table} b
            INNER JOIN employees e
                ON b.employee_id = e.id
            ORDER BY b.uploaded_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /**
     * Get one record
     */
    public function find($id)
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE benefit_id = :id
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee records
     */
    public function getByEmployee($employeeId)
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE employee_id = :employee_id
                ORDER BY uploaded_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create record
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
                (
                    employee_id,
                    record_type,
                    period,
                    description,
                    file_name,
                    file_path,
                    uploaded_by
                )
                VALUES
                (
                    :employee_id,
                    :record_type,
                    :period,
                    :description,
                    :file_name,
                    :file_path,
                    :uploaded_by
                )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':record_type' => $data['record_type'],
            ':period' => $data['period'],
            ':description' => $data['description'],
            ':file_name' => $data['file_name'],
            ':file_path' => $data['file_path'],
            ':uploaded_by' => $data['uploaded_by']
        ]);
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET
                    record_type = :record_type,
                    period = :period,
                    description = :description,
                    file_name = :file_name,
                    file_path = :file_path
                WHERE benefit_id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':record_type' => $data['record_type'],
            ':period' => $data['period'],
            ':description' => $data['description'],
            ':file_name' => $data['file_name'],
            ':file_path' => $data['file_path'],
            ':id' => $id
        ]);
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table}
                WHERE benefit_id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}