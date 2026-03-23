<?php
require_once __DIR__ . "/../../auth/database.php";

class Enrollment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllEnrollments() {
        $stmt = $this->db->prepare("SELECT e.*, c.title as course_title, c.instructor, tp.title as program_title, u.full_name as employee_name FROM ld_enrollments e JOIN ld_courses c ON e.ld_courses_id = c.ld_courses_id JOIN ld_training_programs tp ON c.ld_training_programs_id = tp.ld_training_programs_id JOIN users u ON e.employee_user_id = u.id ORDER BY e.enrolled_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEnrollmentsByEmployee($employeeId) {
        $stmt = $this->db->prepare("SELECT e.*, c.title as course_title, c.instructor, tp.title as program_title FROM ld_enrollments e JOIN ld_courses c ON e.ld_courses_id = c.ld_courses_id JOIN ld_training_programs tp ON c.ld_training_programs_id = tp.ld_training_programs_id WHERE e.employee_user_id = ? ORDER BY e.enrolled_at DESC");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramsWithEnrollmentDetails() {
        $stmt = $this->db->prepare("SELECT tp.ld_training_programs_id, tp.title as program_title, tp.status as program_status, COUNT(DISTINCT ln.ld_enrollment_id) as enrolled_students, COUNT(DISTINCT c.ld_courses_id) as course_count FROM ld_training_programs tp LEFT JOIN ld_courses c ON c.ld_training_programs_id = tp.ld_training_programs_id LEFT JOIN ld_enrollments ln ON ln.ld_courses_id = c.ld_courses_id GROUP BY tp.ld_training_programs_id ORDER BY enrolled_students DESC, tp.title ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEnrollmentsByProgram($programId) {
        $stmt = $this->db->prepare("SELECT e.*, u.full_name as employee_name, c.title as course_title, c.instructor, tp.title as program_title FROM ld_enrollments e JOIN users u ON e.employee_user_id = u.id JOIN ld_courses c ON e.ld_courses_id = c.ld_courses_id JOIN ld_training_programs tp ON c.ld_training_programs_id = tp.ld_training_programs_id WHERE tp.ld_training_programs_id = ? ORDER BY e.enrolled_at DESC");
        $stmt->execute([$programId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function enrollEmployee($data) {
        $stmt = $this->db->prepare("INSERT INTO ld_enrollments (employee_user_id, ld_courses_id, status, enrolled_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([
            $data['employee_id'],
            $data['course_id'],
            $data['status'] ?? 'enrolled'
        ]);
    }

    public function updateProgress($enrollmentId, $progress) {
        $stmt = $this->db->prepare("UPDATE ld_enrollments SET progress_percentage = ?, status = ? WHERE ld_enrollment_id = ?");
        $status = $progress >= 100 ? 'completed' : 'in-progress';
        return $stmt->execute([$progress, $status, $enrollmentId]);
    }

    public function getEnrollmentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ld_enrollments WHERE ld_enrollment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>