<?php

class Survey {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new survey
    public function create($title, $description, $created_by) {
        $sql = "INSERT INTO engagement_surveys (title, description, created_by) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $created_by]);
    }

    // Get all surveys
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM engagement_surveys');
        return $stmt->fetchAll();
    }

    // Get a survey by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM engagement_surveys WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update a survey
    public function update($id, $title, $description) {
        $sql = "UPDATE engagement_surveys SET title = ?, description = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $description, $id]);
    }

    // Delete a survey
    public function delete($id) {
        $sql = "DELETE FROM engagement_surveys WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get questions for a survey
    public function getQuestions($survey_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM survey_questions WHERE survey_id = ?');
        $stmt->execute([$survey_id]);
        return $stmt->fetchAll();
    }

    // Add a question to a survey
    public function addQuestion($survey_id, $question_text, $question_type) {
        $sql = "INSERT INTO survey_questions (survey_id, question_text, question_type) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$survey_id, $question_text, $question_type]);
    }

    // Get responses for a survey
    public function getResponses($survey_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM survey_responses WHERE survey_id = ?');
        $stmt->execute([$survey_id]);
        return $stmt->fetchAll();
    }

    // Add a response to a survey
    public function addResponse($survey_id, $employee_id) {
        $sql = "INSERT INTO survey_responses (survey_id, employee_id) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$survey_id, $employee_id]);
        return $this->pdo->lastInsertId();
    }

    // Add an answer to a survey response
    public function addAnswer($response_id, $question_id, $answer) {
        $sql = "INSERT INTO survey_answers (response_id, question_id, answer) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$response_id, $question_id, $answer]);
    }

    // Get answers for a response
    public function getAnswers($response_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM survey_answers WHERE response_id = ?');
        $stmt->execute([$response_id]);
        return $stmt->fetchAll();
    }
}
