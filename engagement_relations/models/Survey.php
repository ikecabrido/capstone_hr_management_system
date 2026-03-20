<?php
namespace App\Models;

class Survey extends BaseModel
{
    public function createSurvey($title, $created_by)
    {
        $sql = 'INSERT INTO surveys (title, created_by, date_created) VALUES (:title, :created_by, NOW())';
        $params = [
            'title' => $title,
            'created_by' => $created_by,
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function addQuestion($survey_id, $question_text, $type = 'text')
    {
        $sql = 'INSERT INTO survey_questions (survey_id, question_text, type) VALUES (:survey_id, :question_text, :type)';
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
        return $this->execute('SELECT * FROM surveys ORDER BY date_created DESC')->fetchAll();
    }

    public function submitResponse($survey_id, $employee_id, $answers)
    {
        $sql = 'INSERT INTO survey_responses (survey_id, employee_id, answers, submitted_at) VALUES (:survey_id, :employee_id, :answers, NOW())';
        $params = [
            'survey_id' => $survey_id,
            'employee_id' => $employee_id,
            'answers' => is_array($answers) ? json_encode($answers, JSON_UNESCAPED_UNICODE) : $answers,
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function getWithQuestions($id)
    {
        $survey = $this->execute('SELECT * FROM surveys WHERE id = :id', ['id' => $id])->fetch();

        if (!$survey) {
            return null;
        }

        $survey['questions'] = $this->execute('SELECT * FROM survey_questions WHERE survey_id = :id', ['id' => $id])->fetchAll();
        return $survey;
    }

    public function deleteSurvey($id)
    {
        // Use ON DELETE CASCADE for related survey_questions and survey_responses if DB schema supports it.
        return $this->execute('DELETE FROM surveys WHERE id = :id', ['id' => $id])->rowCount() > 0;
    }
}

