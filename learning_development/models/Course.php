<?php
require_once __DIR__ . "/../../auth/database.php";

class Course {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllCourses() {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   tp.title as program_title,
                   u.full_name as creator_name
            FROM ld_courses c
            LEFT JOIN ld_training_programs tp ON c.ld_training_programs_id = tp.ld_training_programs_id
            LEFT JOIN users u ON c.created_by_user_id = u.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCoursesByCreator($creatorId) {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   tp.title as program_title,
                   u.full_name as creator_name
            FROM ld_courses c
            LEFT JOIN ld_training_programs tp ON c.ld_training_programs_id = tp.ld_training_programs_id
            LEFT JOIN users u ON c.created_by_user_id = u.id
            WHERE c.created_by_user_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$creatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ld_courses WHERE ld_courses_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCourse($data) {
        $stmt = $this->db->prepare("INSERT INTO ld_courses (title, description, instructor, duration_hours, ld_training_programs_id, content_type, status, created_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['instructor'],
            $data['duration_hours'],
            $data['training_program_id'],
            $data['content_type'] ?? 'in-person',
            $data['status'] ?? 'active',
            $data['created_by']
        ]);
    }

    public function updateCourse($id, $data) {
        $stmt = $this->db->prepare("UPDATE ld_courses SET title = ?, description = ?, instructor = ?, duration_hours = ?, ld_training_programs_id = ?, content_type = ?, status = ? WHERE ld_courses_id = ?");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['instructor'],
            $data['duration_hours'],
            $data['training_program_id'],
            $data['content_type'],
            $data['status'],
            $id
        ]);
    }

    public function deleteCourse($id) {
        $stmt = $this->db->prepare("DELETE FROM ld_courses WHERE ld_courses_id = ?");
        return $stmt->execute([$id]);
    }
}
?>