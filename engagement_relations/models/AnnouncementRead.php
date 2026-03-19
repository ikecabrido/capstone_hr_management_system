<?php

class AnnouncementRead {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function markAsRead($announcement_id, $employee_id) {
        $sql = "INSERT INTO announcement_reads (announcement_id, employee_id, read_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE read_at = NOW()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$announcement_id, $employee_id]);
    }

    public function getReadsByAnnouncement($announcement_id) {
        $stmt = $this->pdo->prepare('SELECT id, employee_id, read_at FROM announcement_reads WHERE announcement_id = ? ORDER BY read_at DESC');
        $stmt->execute([$announcement_id]);
        return $stmt->fetchAll();
    }

    public function getReadsByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT id, announcement_id, read_at FROM announcement_reads WHERE employee_id = ? ORDER BY read_at DESC');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }
}
?>
