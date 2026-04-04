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
}
