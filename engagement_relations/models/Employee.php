<?php

class Employee {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new employee
    public function create($name, $department_id, $email, $role = 'employee', $status = 'active') {
        $sql = "INSERT INTO employees (name, department_id, email, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $department_id, $email, $role, $status]);
    }

    // Get all employees (with optional role-based filtering)
    public function getAll($userRole = null, $userId = null) {
        if ($userRole === 'employee') {
            // Employees only see their own record
            $stmt = $this->pdo->prepare('SELECT id, name, email, department_id, role, status FROM employees WHERE id = ?');
            $stmt->execute([$userId]);
            return [$stmt->fetch()];
        }
        // Admins and managers see all employees
        $stmt = $this->pdo->query('SELECT id, name, email, department_id, role, status FROM employees');
        return $stmt->fetchAll();
    }

    // Get employee by ID (with role-based access control)
    public function getById($id, $userRole = null, $userId = null) {
        // Employees can only access their own record
        if ($userRole === 'employee' && $userId != $id) {
            return null; // Access denied
        }
        $stmt = $this->pdo->prepare('SELECT id, name, email, department_id, role, status FROM employees WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update employee
    public function update($id, $name, $department_id, $email, $role, $status) {
        $sql = "UPDATE employees SET name = ?, department_id = ?, email = ?, role = ?, status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $department_id, $email, $role, $status, $id]);
    }

    // Delete employee
    public function delete($id) {
        $sql = "DELETE FROM employees WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get employees by department
    public function getByDepartment($department_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM employees WHERE department_id = ?');
        $stmt->execute([$department_id]);
        return $stmt->fetchAll();
    }
}
