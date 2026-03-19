<?php

class Suggestion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($employee_id, $suggestion_text) {
        $sql = "INSERT INTO suggestions (employee_id, suggestion_text, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$employee_id, $suggestion_text]);
    }

    public function getAll($userRole = null, $userId = null) {
        if ($userRole === 'employee') {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, suggestion_text, status, created_at FROM suggestions WHERE employee_id = ? ORDER BY created_at DESC');
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->pdo->query('SELECT id, employee_id, suggestion_text, status, created_at FROM suggestions ORDER BY created_at DESC');
        }
        return $stmt->fetchAll();
    }

    public function getById($id, $userRole = null, $userId = null) {
        if ($userRole === 'employee') {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, suggestion_text, status, created_at FROM suggestions WHERE id = ? AND employee_id = ?');
            $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, suggestion_text, status, created_at FROM suggestions WHERE id = ?');
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE suggestions SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM suggestions WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
