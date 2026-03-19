<?php

class Recognition {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create recognition
    public function create($from_employee_id, $to_employee_id, $type, $message, $reward_id = null) {
        $sql = "INSERT INTO recognitions (from_employee_id, to_employee_id, type, message, reward_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$from_employee_id, $to_employee_id, $type, $message, $reward_id]);
    }

    // Get all recognitions
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM recognitions');
        return $stmt->fetchAll();
    }

    // Get recognition by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM recognitions WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Delete recognition
    public function delete($id) {
        $sql = "DELETE FROM recognitions WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get recognitions for an employee
    public function getForEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM recognitions WHERE to_employee_id = ?');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }
}
