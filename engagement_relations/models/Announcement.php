<?php

class Announcement {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create announcement
    public function create($title, $content, $created_by) {
        $sql = "INSERT INTO announcements (title, content, created_by) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $content, $created_by]);
    }

    // Get all announcements
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM announcements');
        return $stmt->fetchAll();
    }

    // Get announcement by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM announcements WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update announcement
    public function update($id, $title, $content) {
        $sql = "UPDATE announcements SET title = ?, content = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $content, $id]);
    }

    // Delete announcement
    public function delete($id) {
        $sql = "DELETE FROM announcements WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Mark as read
    public function markAsRead($announcement_id, $employee_id) {
        $sql = "INSERT INTO announcement_reads (announcement_id, employee_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$announcement_id, $employee_id]);
    }

    // Get read status
    public function getReadStatus($announcement_id, $employee_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM announcement_reads WHERE announcement_id = ? AND employee_id = ?');
        $stmt->execute([$announcement_id, $employee_id]);
        return $stmt->fetch();
    }
}
