<?php

class Reward {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $description, $points) {
        $sql = "INSERT INTO rewards (name, description, points) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $description, $points]);
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, name, description, points FROM rewards ORDER BY name');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT id, name, description, points FROM rewards WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $description, $points) {
        $sql = "UPDATE rewards SET name = ?, description = ?, points = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $description, $points, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM rewards WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
