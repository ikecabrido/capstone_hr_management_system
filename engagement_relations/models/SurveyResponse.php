<?php

class SurveyResponse {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, survey_id, employee_id, submitted_at FROM survey_responses ORDER BY submitted_at DESC');
        return $stmt->fetchAll();
    }

    public function create($survey_id, $employee_id) {
        $sql = "INSERT INTO survey_responses (survey_id, employee_id, submitted_at) VALUES (?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$survey_id, $employee_id]);
    }

    public function getBySurvey($survey_id) {
        $stmt = $this->pdo->prepare('SELECT id, employee_id, submitted_at FROM survey_responses WHERE survey_id = ? ORDER BY submitted_at DESC');
        $stmt->execute([$survey_id]);
        return $stmt->fetchAll();
    }

    public function getByEmployee($employee_id) {
        $stmt = $this->pdo->prepare('SELECT id, survey_id, submitted_at FROM survey_responses WHERE employee_id = ? ORDER BY submitted_at DESC');
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll();
    }

    public function getCount($survey_id) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM survey_responses WHERE survey_id = ?');
        $stmt->execute([$survey_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
?>
