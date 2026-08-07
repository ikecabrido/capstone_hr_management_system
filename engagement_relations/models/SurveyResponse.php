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
        $sql = 'INSERT INTO eer_survey_feedback_id (employee_id, comment, rating, survey_id) VALUES (:employee_id, :comment, :rating, :survey_id)';
        $this->execute($sql, [
            'employee_id' => $userId,
            'rating' => 3,
            'comment' => $comment,
            'survey_id' => $surveyId
        ]);
    }

    public function getResponses($surveyId)
    {
        $sql = 'SELECT * FROM eer_survey_responses WHERE survey_id = :survey_id';
        return $this->execute($sql, ['survey_id' => $surveyId])->fetchAll();
    }
}
