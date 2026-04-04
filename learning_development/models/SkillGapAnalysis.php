<?php
require_once __DIR__ . "/../../auth/database.php";

class SkillGapAnalysis {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllAnalyses() {
        $stmt = $this->db->prepare("SELECT s.*, u.full_name as employee_name FROM skill_gap_analyses s JOIN users u ON s.employee_id = u.id ORDER BY s.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAnalysis($data) {
        $stmt = $this->db->prepare("INSERT INTO skill_gap_analyses (employee_id, required_skill, current_level, required_level, gap_description, recommendations, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['employee_id'],
            $data['required_skill'],
            $data['current_level'],
            $data['required_level'],
            $data['gap_description'],
            $data['recommendations'],
            $data['created_by']
        ]);
    }

    public function getAnalysisByEmployee($employeeId) {
        $stmt = $this->db->prepare("SELECT * FROM skill_gap_analyses WHERE employee_id = ? ORDER BY created_at DESC");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calculate skill gap score
    public function calculateGapScore($current, $required) {
        return max(0, $required - $current);
    }
}
?>