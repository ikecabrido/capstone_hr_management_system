<?php

class Department {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name) {
        $sql = "INSERT INTO departments (name) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, name FROM departments');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT id, name FROM departments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name) {
        $sql = "UPDATE departments SET name = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM departments WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
