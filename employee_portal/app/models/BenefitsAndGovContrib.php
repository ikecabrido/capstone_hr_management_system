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
    public function all()
    {
        $sql = "SELECT
                b.*,
                e.first_name, e.last_name
            FROM {$this->table} b
            INNER JOIN employees e
                ON b.employee_id = e.employee_id
            ORDER BY b.uploaded_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE benefit_id = :id LIMIT 1"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
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
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
            SET
                record_type = :record_type,
                period = :period,
                description = :description";

        if (!empty($data['file_name']) && !empty($data['file_path'])) {
            $sql .= ",
                file_name = :file_name,
                file_path = :file_path";
        }

        $sql .= " WHERE benefit_id = :benefit_id";

        $stmt = $this->conn->prepare($sql);

        $params = [
            ':record_type' => $data['record_type'],
            ':period' => $data['period'],
            ':description' => $data['description'],
            ':benefit_id' => $id
        ];

        if (!empty($data['file_name']) && !empty($data['file_path'])) {
            $params[':file_name'] = $data['file_name'];
            $params[':file_path'] = $data['file_path'];
        }

        return $stmt->execute($params);
    }
    public function delete($id)
    {
        $stmt = $this->conn->prepare("
        DELETE FROM {$this->table}
        WHERE benefit_id = :id
    ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
    public function getEmployeeBenefits($userId)
    {
        $sql = "SELECT
                b.*,
                e.first_name, last_name
            FROM {$this->table} b
            INNER JOIN employees e
                ON e.employee_id = b.employee_id
            WHERE e.user_id = :user_id
            ORDER BY b.uploaded_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEmployeeByUserId($userId)
    {
        $sql = "SELECT 
                employee_id,
                first_name, last_name
            FROM employees
            WHERE user_id = :user_id
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
