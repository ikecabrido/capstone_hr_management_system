<?php
require_once "../app/Core/Database.php";

class EmployeeRequest extends Database
{
    protected $table = 'employee_requests';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (employee_id, approver_id, request_type, title, description, status, start_date, end_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['employee_id'],
            $data['approver_id'] ?? null,
            $data['request_type'],
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'pending',
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['created_by'] ?? null
        ]);
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table} ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table} WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $stmt = $this->conn->prepare("
            UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE id = ?
        ");

        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table} WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
}