<?php
require_once __DIR__ . '/../config/Database.php';

class TrainingProgram
{
    private $conn;
    private $tableTP = "ld_training_programs";
    private $tableTE = "ld_enrollments";
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function all()
    {
        $query = "SELECT * FROM {$this->tableTP} ORDER BY created_at DESC LIMIT 100";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getEnrollmentDetailsByProgram($programId)
    {
        $query = "
        SELECT 
            te.ld_enrollment_id AS id,
            te.employee_user_id AS user_id,
            te.status,

            u.username,
            u.full_name,
            u.email,

            e.position_id,
            e.department

        FROM {$this->tableTE} te

        JOIN ld_courses c 
            ON te.ld_courses_id = c.ld_courses_id

        JOIN ld_training_programs tp 
            ON c.ld_training_programs_id = tp.ld_training_programs_id

        JOIN users u 
            ON te.employee_user_id = u.id

        LEFT JOIN employees e 
            ON e.user_id = u.id

        WHERE tp.ld_training_programs_id = :program_id
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':program_id' => $programId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
