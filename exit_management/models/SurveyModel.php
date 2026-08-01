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
            INSERT INTO exit_survey_questions (survey_id, question_text, question_type,
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
            SELECT * FROM exit_survey_questions
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
                INSERT INTO exit_survey_responses (survey_id, employee_id, submitted_at)
                VALUES (?, ?, NOW())
            ");
            if (!$stmt->execute([$surveyId, $employeeId])) {
                throw new Exception('Failed to insert survey response');
            }
            $responseId = (int)$this->db->lastInsertId();

            // Insert individual answers
            $stmt = $this->db->prepare("
                INSERT INTO exit_survey_answers (response_id, question_id, answer_text, answer_value)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($responses as $questionId => $answer) {
                $answerText = is_array($answer) ? json_encode($answer) : $answer;
                $answerValue = is_array($answer) ? implode(', ', $answer) : $answer;

                if (!$stmt->execute([$responseId, $questionId, $answerText, $answerValue])) {
                    throw new Exception('Failed to insert survey answer');
                }
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
            SELECT sa.*, sq.question_text, u.full_name as respondent_name, sr.submitted_at
            FROM exit_survey_answers sa
            JOIN exit_survey_questions sq ON sa.question_id = sq.id
            JOIN exit_survey_responses sr ON sa.response_id = sr.id
            JOIN users u ON sr.employee_id = u.id
            WHERE sr.survey_id = ?
            ORDER BY sr.submitted_at DESC, sq.order_num ASC
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
            FROM exit_survey_responses sr
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
            FROM exit_survey_answers sa
            JOIN exit_survey_questions sq ON sa.question_id = sq.id
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
                SELECT survey_id FROM exit_survey_responses WHERE employee_id = ?
            ))
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all surveys with optional status filter and pagination
     */
    public function getAllSurveys(string $status = null, int $page = 1, int $limit = 10, string $search = ''): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT
                id,
                title,
                description,
                status,
                created_at,
                updated_at
            FROM exit_surveys
        ";

        $countSql = "
            SELECT COUNT(*) as total
            FROM exit_surveys
        ";

        $params = [];
        $whereClause = "";

        if ($status && $status !== 'all') {
            $whereClause = " WHERE status = :status";
            $params['status'] = $status;
        }

        // Add search condition if provided
        if (!empty($search)) {
            $searchCondition = $whereClause ? " AND" : " WHERE";
            $searchCondition .= " (title LIKE :search0 OR description LIKE :search1)";
            $whereClause .= $searchCondition;
            $searchParam = "%$search%";
            $params['search0'] = $searchParam;
            $params['search1'] = $searchParam;
        }

        // Get total count
        $countStmt = $this->db->prepare($countSql . $whereClause);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated data
        $stmt = $this->db->prepare($sql . $whereClause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
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
                FROM exit_survey_answers sa
                JOIN exit_survey_responses sr ON sa.response_id = sr.id
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

    /**
     * Archive survey
     */
    public function archiveSurvey(int $surveyId, string $archiveReason = 'Manual archive'): bool
    {
        // Get the full survey data
        $stmt = $this->db->prepare("SELECT * FROM exit_surveys WHERE id = ?");
        $stmt->execute([$surveyId]);
        $survey = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$survey) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Insert into exit_archive
            $archiveStmt = $this->db->prepare("
                INSERT INTO exit_archive (
                    archive_type, original_id, employee_id, title, description, content,
                    status, original_created_by, archived_by, archive_reason, archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $title = "Survey - " . ($survey['title'] ?? 'Unknown Survey');
            $description = "Archived survey record";
            $content = json_encode($survey);
            $archivedBy = $_SESSION['user']['id'] ?? 1;

            $archiveStmt->execute([
                'survey',
                $surveyId,
                null, // surveys don't have specific employee_id
                $title,
                $description,
                $content,
                $survey['status'],
                $survey['created_by'],
                $archivedBy,
                $archiveReason,
                $content
            ]);

            // Delete from exit_surveys
            $deleteStmt = $this->db->prepare("DELETE FROM exit_surveys WHERE id = ?");
            $deleteStmt->execute([$surveyId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Survey archive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unarchive survey
     */
    public function unarchiveSurvey(int $surveyId): bool
    {
        // Get archived data
        $stmt = $this->db->prepare("SELECT * FROM exit_archive WHERE archive_type = 'survey' AND original_id = ?");
        $stmt->execute([$surveyId]);
        $archive = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$archive) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Decode the archived data
            $surveyData = json_decode($archive['archive_data'], true);
            if (!$surveyData) {
                return false;
            }

            // Insert back into exit_surveys
            $insertStmt = $this->db->prepare("
                INSERT INTO exit_surveys (
                    id, title, description, status, created_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStmt->execute([
                $surveyData['id'],
                $surveyData['title'],
                $surveyData['description'],
                $surveyData['status'] ?? 'draft',
                $surveyData['created_by'],
                $surveyData['created_at'],
                date('Y-m-d H:i:s')
            ]);

            // Update archive record to mark as restored
            $updateStmt = $this->db->prepare("
                UPDATE exit_archive
                SET restored = 1, restored_by = ?, restored_at = NOW()
                WHERE id = ?
            ");
            $restoredBy = $_SESSION['user']['id'] ?? 1;
            $updateStmt->execute([$restoredBy, $archive['id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Survey unarchive error: " . $e->getMessage());
            return false;
        }
    }
}