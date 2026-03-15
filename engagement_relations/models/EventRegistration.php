<?php

class EventRegistration {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($event_id, $employee_id) {
        $sql = "INSERT INTO event_registrations (event_id, employee_id, registered_at) VALUES (?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$event_id, $employee_id]);
    }

    public function getByEvent($event_id) {
        $stmt = $this->pdo->prepare('SELECT id, employee_id, registered_at, attended FROM event_registrations WHERE event_id = ? ORDER BY registered_at DESC');
        $stmt->execute([$event_id]);
        return $stmt->fetchAll();
    }

    public function getByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT id, event_id, registered_at, attended FROM event_registrations WHERE employee_id = ? ORDER BY registered_at DESC');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }

    public function markAttended($registration_id) {
        $sql = "UPDATE event_registrations SET attended = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$registration_id]);
    }

    public function getCount($event_id) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?');
        $stmt->execute([$event_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
?>
