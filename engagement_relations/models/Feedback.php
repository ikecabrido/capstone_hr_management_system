<?php

class Feedback {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create feedback
    public function create($employee_id, $feedback_text, $is_anonymous = false, $status = 'new') {
        $sql = "INSERT INTO feedback (employee_id, feedback_text, is_anonymous, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$employee_id, $feedback_text, $is_anonymous, $status]);
    }

    // Get all feedback (with role-based filtering)
    public function getAll($userRole = null, $userId = null) {
        if ($userRole === 'employee') {
            // Employees only see their own feedback submissions
            $stmt = $this->pdo->prepare('SELECT id, employee_id, feedback_text, is_anonymous, status FROM feedback WHERE employee_id = ? ORDER BY id DESC');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
        // Admins and HR see all feedback
        $stmt = $this->pdo->query('SELECT id, employee_id, feedback_text, is_anonymous, status FROM feedback ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    // Get feedback by ID (with role-based access control)
    public function getById($id, $userRole = null, $userId = null) {
        // Employees can only view their own feedback
        if ($userRole === 'employee') {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, feedback_text, is_anonymous, status FROM feedback WHERE id = ? AND employee_id = ?');
            $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, feedback_text, is_anonymous, status FROM feedback WHERE id = ?');
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    // Update feedback status
    public function updateStatus($id, $status) {
        $sql = "UPDATE feedback SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // Delete feedback
    public function delete($id) {
        $sql = "DELETE FROM feedback WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get feedback by employee
    public function getByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM feedback WHERE employee_id = ?');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }
}
