<?php

class EngagementSurvey {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($title, $description, $created_by) {
        $sql = "INSERT INTO engagement_surveys (title, description, created_by, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $created_by]);
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, title, description, created_by, created_at FROM engagement_surveys ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT id, title, description, created_by, created_at FROM engagement_surveys WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $title, $description) {
        $sql = "UPDATE engagement_surveys SET title = ?, description = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM engagement_surveys WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
