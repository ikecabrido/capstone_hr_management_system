<?php

class AuditLog {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create audit log
    public function create($action, $performed_by, $target_type, $target_id, $details) {
        $sql = "INSERT INTO audit_logs (action, performed_by, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$action, $performed_by, $target_type, $target_id, $details]);
    }

    // Get all audit logs
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM audit_logs');
        return $stmt->fetchAll();
    }

    // Get audit log by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM audit_logs WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Get logs by user
    public function getByUser($performed_by) {
        $stmt = $this->pdo->prepare('SELECT * FROM audit_logs WHERE performed_by = ?');
        $stmt->execute([$performed_by]);
        return $stmt->fetchAll();
    }
}
