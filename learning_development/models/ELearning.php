<?php
require_once __DIR__ . "/../../auth/database.php";

class ELearning {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllModules() {
        $stmt = $this->db->prepare("SELECT * FROM ld_elearning_modules ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createModule($data) {
        $stmt = $this->db->prepare("INSERT INTO ld_elearning_modules (title, description, content_url, ld_courses_id, created_by_user_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['content_url'],
            $data['course_id'],
            $data['created_by']
        ]);
    }

    // Placeholder for virtual training features
    public function scheduleVirtualSession($data) {
        // This would integrate with video conferencing APIs
        $stmt = $this->db->prepare("INSERT INTO ld_virtual_sessions (ld_courses_id, session_url, scheduled_at, created_by_user_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['course_id'],
            $data['session_url'],
            $data['scheduled_at'],
            $data['created_by']
        ]);
    }
}
?>