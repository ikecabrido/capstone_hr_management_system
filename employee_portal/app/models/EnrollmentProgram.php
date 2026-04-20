<?php
require_once __DIR__ . '/../config/Database.php';

class EnrollmentProgram
{
    private $conn;
    private $table = 'ld_enrollments';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function isEnrolled($userId, $programId)
    {
        $stmt = $this->conn->prepare("
            SELECT ld_enrollment_id 
            FROM {$this->table} 
            WHERE employee_user_id = ? 
            AND ld_courses_id = ?
            AND status != 'cancelled'
        ");
        $stmt->execute([$userId, $programId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function enroll($userId, $programId)
    {
        if ($this->isEnrolled($userId, $programId)) {
            return ['status' => false, 'message' => 'Already enrolled'];
        }

        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (employee_user_id, ld_courses_id, status)
            VALUES (?, ?, 'enrolled')
        ");
        $stmt->execute([$userId, $programId]);

        return ['status' => true, 'message' => 'Enrolled successfully'];
    }

    public function unenroll($userId, $programId)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET status = 'cancelled'
            WHERE employee_user_id = ? 
            AND ld_courses_id = ?
        ");
        $stmt->execute([$userId, $programId]);

        return ['status' => true, 'message' => 'Unenrolled successfully'];
    }

    public function getUserEnrollments($userId)
{
    $stmt = $this->conn->prepare("
        SELECT p.*
        FROM {$this->table} e
        INNER JOIN ld_training_programs p 
            ON e.ld_courses_id = p.ld_training_programs_id
        WHERE e.employee_user_id = ?
        AND e.status != 'cancelled'
        ORDER BY e.enrolled_at DESC
    ");

    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}