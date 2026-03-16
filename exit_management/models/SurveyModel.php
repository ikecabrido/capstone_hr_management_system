<?php

require_once 'ExitManagementModel.php';

class SurveyModel extends ExitManagementModel
{
    /**
     * Create a survey
     */
    public function createSurvey(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO exit_surveys (title, description, target_audience, start_date,
                                    end_date, status, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, 'active', ?, NOW())
        ");

        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['target_audience'] ?? 'all',
            $data['start_date'],
            $data['end_date'],
            $data['created_by']
        ]);

        $surveyId = (int)$this->db->lastInsertId();

        // Add questions if provided
        if (isset($data['questions']) && is_array($data['questions'])) {
            $this->addSurveyQuestions($surveyId, $data['questions']);
        }

        return $surveyId;
    }

    /**
     * Add questions to a survey
     */
    public function addSurveyQuestions(int $surveyId, array $questions): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO survey_questions (survey_id, question_text, question_type,
                                        options, required, order_num, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        foreach ($questions as $index => $question) {
            $options = null;
            if (in_array($question['type'], ['radio', 'checkbox', 'select']) && isset($question['options'])) {
                $options = json_encode($question['options']);
            }

            $stmt->execute([
                $surveyId,
                $question['text'],
                $question['type'],
                $options,
                $question['required'] ?? false,
                $index + 1
            ]);
        }

        return true;
    }

    /**
     * Get survey by ID
     */
    public function getSurveyById(int $surveyId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_surveys WHERE id = ? AND status = 'active'
        ");
        $stmt->execute([$surveyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get survey questions
     */
    public function getSurveyQuestions(int $surveyId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM survey_questions
            WHERE survey_id = ?
            ORDER BY order_num ASC
        ");
        $stmt->execute([$surveyId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode options JSON
        foreach ($questions as &$question) {
            if ($question['options']) {
                $question['options'] = json_decode($question['options'], true);
            }
        }

        return $questions;
    }

    /**
     * Submit survey response
     */
    public function submitSurveyResponse(int $surveyId, int $employeeId, array $responses): bool
    {
        // Start transaction
        $this->db->beginTransaction();

        try {
            // Insert response record
            $stmt = $this->db->prepare("
                INSERT INTO survey_responses (survey_id, employee_id, submitted_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$surveyId, $employeeId]);
            $responseId = (int)$this->db->lastInsertId();

            // Insert individual answers
            $stmt = $this->db->prepare("
                INSERT INTO survey_answers (response_id, question_id, answer_text, answer_value)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($responses as $questionId => $answer) {
                $answerText = is_array($answer) ? json_encode($answer) : $answer;
                $answerValue = is_array($answer) ? implode(', ', $answer) : $answer;

                $stmt->execute([$responseId, $questionId, $answerText, $answerValue]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get survey responses
     */
    public function getSurveyResponses(int $surveyId): array
    {
        $stmt = $this->db->prepare("
            SELECT sr.*, u.full_name, u.username as emp_id
            FROM survey_responses sr
            JOIN users u ON sr.employee_id = u.id
            WHERE sr.survey_id = ?
            ORDER BY sr.submitted_at DESC
        ");
        $stmt->execute([$surveyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get survey response details
     */
    public function getSurveyResponseDetails(int $responseId): array
    {
        // Get response info
        $stmt = $this->db->prepare("
            SELECT sr.*, s.title as survey_title, u.full_name
            FROM survey_responses sr
            JOIN exit_surveys s ON sr.survey_id = s.id
            JOIN users u ON sr.employee_id = u.id
            WHERE sr.id = ?
        ");
        $stmt->execute([$responseId]);
        $response = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$response) return [];

        // Get answers
        $stmt = $this->db->prepare("
            SELECT sa.*, sq.question_text, sq.question_type
            FROM survey_answers sa
            JOIN survey_questions sq ON sa.question_id = sq.id
            WHERE sa.response_id = ?
            ORDER BY sq.order_num ASC
        ");
        $stmt->execute([$responseId]);
        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode answer arrays
        foreach ($answers as &$answer) {
            if ($answer['question_type'] === 'checkbox' && $answer['answer_text']) {
                $answer['answer_array'] = json_decode($answer['answer_text'], true);
            }
        }

        return [
            'response' => $response,
            'answers' => $answers
        ];
    }

    /**
     * Get active surveys for an employee
     */
    public function getActiveSurveysForEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.* FROM exit_surveys s
            WHERE s.status = 'active'
            AND s.start_date <= CURDATE()
            AND s.end_date >= CURDATE()
            AND (s.target_audience = 'all' OR s.id NOT IN (
                SELECT survey_id FROM survey_responses WHERE employee_id = ?
            ))
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate survey report
     */
    public function generateSurveyReport(int $surveyId): array
    {
        $survey = $this->getSurveyById($surveyId);
        if (!$survey) return [];

        $questions = $this->getSurveyQuestions($surveyId);
        $responses = $this->getSurveyResponses($surveyId);

        $report = [
            'survey' => $survey,
            'total_responses' => count($responses),
            'questions' => []
        ];

        foreach ($questions as $question) {
            $questionReport = [
                'question' => $question,
                'responses' => []
            ];

            // Get answers for this question
            $stmt = $this->db->prepare("
                SELECT answer_value, COUNT(*) as count
                FROM survey_answers sa
                JOIN survey_responses sr ON sa.response_id = sr.id
                WHERE sa.question_id = ? AND sr.survey_id = ?
                GROUP BY answer_value
                ORDER BY count DESC
            ");
            $stmt->execute([$question['id'], $surveyId]);
            $questionReport['responses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $report['questions'][] = $questionReport;
        }

        return $report;
    }
}