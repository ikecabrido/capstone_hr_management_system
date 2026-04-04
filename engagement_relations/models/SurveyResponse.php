<?php
namespace App\Models;

class SurveyResponse extends BaseModel
{
    public function submit($data)
    {
        $sql = 'INSERT INTO eer_survey_responses (survey_id, employee_id, answers) VALUES (:survey_id, :employee_id, :answers)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function getBySurvey($survey_id)
    {
        $nameSql = $this->getEmployeeNameSql('e', 'employee_name');
        $sql = "SELECT r.*, $nameSql FROM eer_survey_responses r LEFT JOIN employees e ON r.employee_id = e.employee_id WHERE r.survey_id = :survey_id ORDER BY r.eer_survey_response_id DESC";
        return $this->execute($sql, ['survey_id' => $survey_id])->fetchAll();
    }

    public function addFeedback($surveyId, $userId, $comment)
    {
        $sql = 'INSERT INTO eer_survey_feedback (survey_id, user_id, comment) VALUES (:survey_id, :user_id, :comment)';
        $this->execute($sql, [
            'survey_id' => $surveyId,
            'user_id' => $userId,
            'comment' => $comment
        ]);
    }

    public function getResponses($surveyId)
    {
        $sql = 'SELECT * FROM eer_survey_responses WHERE survey_id = :survey_id';
        return $this->execute($sql, ['survey_id' => $surveyId])->fetchAll();
    }
}
