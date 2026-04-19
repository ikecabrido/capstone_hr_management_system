<?php
require_once __DIR__ . '/../config/Database.php';
class EnrollmentProgram
{
    private $conn;

    public function __construct($pdo)
    {
        $this->conn = $pdo;
    }

    public function isEnrolled($userId, $programId)
    {
        $stmt = $this->conn->prepare("
            SELECT ld_enrollment_id 
            FROM ld_enrollments 
            WHERE employee_user_id = ? 
            AND ld_courses_id = ?
            AND status != 'cancelled'
        ");
        $stmt->execute([$userId, $programId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function enroll($userId, $programId)
    {
        // check existing
        if ($this->isEnrolled($userId, $programId)) {
            return ['status' => false, 'message' => 'Already enrolled'];
        }

        $stmt = $this->conn->prepare("
            INSERT INTO ld_enrollments (employee_user_id, ld_courses_id, status)
            VALUES (?, ?, 'enrolled')
        ");
        $stmt->execute([$userId, $programId]);

        return ['status' => true, 'message' => 'Enrolled successfully'];
    }

    public function unenroll($userId, $programId)
    {
        $stmt = $this->conn->prepare("
            UPDATE ld_enrollments 
            SET status = 'cancelled'
            WHERE employee_user_id = ? 
            AND ld_courses_id = ?
        ");
        $stmt->execute([$userId, $programId]);

        return ['status' => true, 'message' => 'Unenrolled successfully'];
    }
}
