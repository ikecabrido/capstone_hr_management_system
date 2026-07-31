<?php
require_once __DIR__ . '/../config/Database.php';

class PerformanceFeedback
{
    private $conn;
    private $table = "pm_360_feedback";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getByEmployee($employee_id)
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE employee_id = ?
                ORDER BY evaluation_date DESC, created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$employee_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $query = "INSERT INTO {$this->table}  
        (employee_id, evaluator_type, rating, category, comments, is_anonymous, evaluation_date, created_at)
        VALUES 
        (:employee_id, :evaluator_type, :rating, :category, :comments, :is_anonymous, :evaluation_date, :created_at)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':evaluator_type' => $data['evaluator_type'],
            ':rating' => $data['rating'],
            ':category' => $data['category'],
            ':comments' => $data['comments'],
            ':is_anonymous' => $data['is_anonymous'],
            ':evaluation_date' => $data['evaluation_date'],
            ':created_at' => $data['created_at']
        ]);
    }
    public function getAll()
    {
        $sql = "SELECT
                f.*,
                e.first_name,
                e.last_name,
                e.employee_code
            FROM pm_360_feedback f
            LEFT JOIN employees e
                ON f.employee_id = e.employee_id
            ORDER BY f.evaluation_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
