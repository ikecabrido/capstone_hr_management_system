<?php

class SurveyAnswer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT id, response_id, question_id, answer FROM survey_answers ORDER BY response_id, question_id');
        return $stmt->fetchAll();
    }

    public function create($response_id, $question_id, $answer) {
        $sql = "INSERT INTO survey_answers (response_id, question_id, answer) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$response_id, $question_id, $answer]);
    }

    public function getByResponse($response_id) {
        $stmt = $this->pdo->prepare('SELECT id, question_id, answer FROM survey_answers WHERE response_id = ? ORDER BY question_id');
        $stmt->execute([$response_id]);
        return $stmt->fetchAll();
    }

    public function getByQuestion($question_id) {
        $stmt = $this->pdo->prepare('SELECT id, response_id, answer FROM survey_answers WHERE question_id = ? ORDER BY id');
        $stmt->execute([$question_id]);
        return $stmt->fetchAll();
    }
}
?>
