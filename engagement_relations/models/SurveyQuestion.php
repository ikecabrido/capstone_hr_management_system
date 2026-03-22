<?php

class SurveyQuestion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, survey_id, question_text, question_type FROM survey_questions ORDER BY survey_id, id');
        return $stmt->fetchAll();
    }

    public function create($survey_id, $question_text, $question_type) {
        $sql = "INSERT INTO survey_questions (survey_id, question_text, question_type) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$survey_id, $question_text, $question_type]);
    }

    public function getBySurvey($survey_id) {
        $stmt = $this->pdo->prepare('SELECT id, question_text, question_type FROM survey_questions WHERE survey_id = ? ORDER BY id');
        $stmt->execute([$survey_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT id, survey_id, question_text, question_type FROM survey_questions WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $sql = "DELETE FROM survey_questions WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
