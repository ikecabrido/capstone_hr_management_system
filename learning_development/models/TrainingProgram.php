<?php
require_once __DIR__ . "/../../auth/database.php";

class TrainingProgram {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllPrograms() {
        $stmt = $this->db->prepare("SELECT tp.*, u.full_name as creator_name FROM ld_training_programs tp JOIN users u ON tp.created_by_user_id = u.id ORDER BY tp.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramsByCreator($creatorId) {
        $stmt = $this->db->prepare("SELECT tp.*, u.full_name as creator_name FROM ld_training_programs tp JOIN users u ON tp.created_by_user_id = u.id WHERE tp.created_by_user_id = ? ORDER BY tp.created_at DESC");
        $stmt->execute([$creatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ld_training_programs WHERE ld_training_programs_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProgram($data) {
        $stmt = $this->db->prepare("INSERT INTO ld_training_programs (title, description, trainer, start_date, end_date, max_participants, status, created_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['trainer'],
            $data['start_date'],
            $data['end_date'],
            $data['max_participants'],
            $data['status'] ?? 'active',
            $data['created_by']
        ]);
    }

    public function updateProgram($id, $data) {
        $stmt = $this->db->prepare("UPDATE ld_training_programs SET title = ?, description = ?, trainer = ?, start_date = ?, end_date = ?, max_participants = ?, status = ? WHERE ld_training_programs_id = ?");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['trainer'],
            $data['start_date'],
            $data['end_date'],
            $data['max_participants'],
            $data['status'],
            $id
        ]);
    }

    public function deleteProgram($id) {
        $stmt = $this->db->prepare("DELETE FROM ld_training_programs WHERE ld_training_programs_id = ?");
        return $stmt->execute([$id]);
    }
}
?>