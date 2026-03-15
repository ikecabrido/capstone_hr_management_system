<?php

class Grievance {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create grievance
    public function create($employee_id, $subject, $description, $status = 'open', $assigned_to = null) {
        $sql = "INSERT INTO grievances (employee_id, subject, description, status, assigned_to) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$employee_id, $subject, $description, $status, $assigned_to]);
    }

    // Get all grievances (with role-based filtering)
    public function getAll($userRole = null, $userId = null) {
        if ($userRole === 'employee') {
            // Employees only see their own grievances
            $stmt = $this->pdo->prepare('SELECT id, employee_id, subject, description, status, assigned_to FROM grievances WHERE employee_id = ? ORDER BY id DESC');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }
        // Admins and HR see all grievances
        $stmt = $this->pdo->query('SELECT id, employee_id, subject, description, status, assigned_to FROM grievances ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    // Get grievance by ID (with role-based access control)
    public function getById($id, $userRole = null, $userId = null) {
        // Employees can only view their own grievances
        if ($userRole === 'employee') {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, subject, description, status, assigned_to FROM grievances WHERE id = ? AND employee_id = ?');
            $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id, employee_id, subject, description, status, assigned_to FROM grievances WHERE id = ?');
            $stmt->execute([$id]);
        }
        return $stmt->fetch();
    }

    // Update grievance status
    public function updateStatus($id, $status) {
        $sql = "UPDATE grievances SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    // Assign grievance
    public function assign($id, $assigned_to) {
        $sql = "UPDATE grievances SET assigned_to = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$assigned_to, $id]);
    }

    // Delete grievance
    public function delete($id) {
        $sql = "DELETE FROM grievances WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get grievances by employee
    public function getByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM grievances WHERE employee_id = ?');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }
}
