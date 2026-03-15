<?php

class GrievanceAction {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($grievance_id, $action_taken, $action_by) {
        $sql = "INSERT INTO grievance_actions (grievance_id, action_taken, action_by, action_date) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$grievance_id, $action_taken, $action_by]);
    }

    public function getByGrievance($grievance_id) {
        $stmt = $this->pdo->prepare('SELECT id, action_taken, action_by, action_date FROM grievance_actions WHERE grievance_id = ? ORDER BY action_date DESC');
        $stmt->execute([$grievance_id]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, grievance_id, action_taken, action_by, action_date FROM grievance_actions ORDER BY action_date DESC');
        return $stmt->fetchAll();
    }
}
?>
