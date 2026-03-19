<?php

class Event {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create event
    public function create($title, $description, $event_date, $created_by) {
        $sql = "INSERT INTO events (title, description, event_date, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $event_date, $created_by]);
    }

    // Get all events
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM events');
        return $stmt->fetchAll();
    }

    // Get event by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update event
    public function update($id, $title, $description, $event_date) {
        $sql = "UPDATE events SET title = ?, description = ?, event_date = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $event_date, $id]);
    }

    // Delete event
    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Register for event
    public function register($event_id, $employee_id) {
        $sql = "INSERT INTO event_registrations (event_id, employee_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$event_id, $employee_id]);
    }

    // Mark attendance
    public function markAttendance($registration_id) {
        $sql = "UPDATE event_registrations SET attended = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$registration_id]);
    }

    // Get registrations for event
    public function getRegistrations($event_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM event_registrations WHERE event_id = ?');
        $stmt->execute([$event_id]);
        return $stmt->fetchAll();
    }
}
