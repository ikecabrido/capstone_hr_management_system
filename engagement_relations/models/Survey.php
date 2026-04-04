<?php
namespace App\Models;

class Survey extends BaseModel
{
    public function createSurvey($title, $created_by)
    {
        $sql = 'INSERT INTO eer_surveys (title, created_by) 
                VALUES (:title, :created_by)';

        $params = [
            'title' => $title,
            'created_by' => $created_by,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function addQuestion($survey_id, $question_text, $type = 'text')
    {
        $sql = 'INSERT INTO eer_survey_questions (survey_id, question_text, type) 
                VALUES (:survey_id, :question_text, :type)';

        $params = [
            'survey_id' => $survey_id,
            'question_text' => $question_text,
            'type' => $type,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function getSurveys()
    {
        $sql = 'SELECT s.*, '
             . 'COALESCE(e.full_name, u.full_name, u.username, s.created_by) AS created_by_name '
             . 'FROM eer_surveys s '
             . 'LEFT JOIN employees e ON s.created_by = e.employee_id '
             . 'LEFT JOIN users u ON s.created_by = u.user_id '
             . 'ORDER BY s.eer_survey_id DESC';
        return $this->execute($sql)->fetchAll();
    }

    // ✅ FIX: Added missing method
    public function getWithQuestions($survey_id)
    {
        // Get survey details
        $survey = $this->execute(
            'SELECT * FROM eer_surveys WHERE eer_survey_id = :id',
            ['id' => $survey_id]
        )->fetch();

        if (!$survey) {
            return null;
        }

        // Get related questions
        $questions = $this->execute(
            'SELECT * FROM eer_survey_questions WHERE survey_id = :survey_id ORDER BY eer_survey_question_id ASC',
            ['survey_id' => $survey_id]
        )->fetchAll();

        // Attach questions to survey
        $survey['questions'] = $questions;

        return $survey;
    }

    public function getSurveyById($surveyId)
    {
        $sql = 'SELECT * FROM eer_surveys WHERE eer_survey_id = :survey_id';
        $params = ['survey_id' => $surveyId];
        return $this->execute($sql, $params)->fetch();
    }

    public function submitResponse($survey_id, $employee_id, $answers)
    {
        $sql = 'INSERT INTO eer_survey_responses 
                (survey_id, employee_id, answers, submitted_at) 
                VALUES (:survey_id, :employee_id, :answers, NOW())';

        $params = [
            'survey_id' => $survey_id,
            'employee_id' => $employee_id,
            'answers' => is_array($answers) 
                ? json_encode($answers, JSON_UNESCAPED_UNICODE) 
                : $answers,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function generateResults($surveyId)
    {
        $responses = $this->execute(
            'SELECT answers FROM eer_survey_responses WHERE survey_id = :survey_id',
            ['survey_id' => $surveyId]
        )->fetchAll();

        $results = [];
        foreach ($responses as $response) {
            $answers = json_decode($response['answers'], true);
            foreach ($answers as $question => $answer) {
                if (!isset($results[$question])) {
                    $results[$question] = [];
                }
                if (!isset($results[$question][$answer])) {
                    $results[$question][$answer] = 0;
                }
                $results[$question][$answer]++;
            }
        }
        return $results;
    }

    public function getSurveyResponses($surveyId)
    {
        $sql = 'SELECT * FROM eer_survey_responses WHERE survey_id = :survey_id';
        $params = ['survey_id' => $surveyId];
        return $this->execute($sql, $params)->fetchAll();
    }
}