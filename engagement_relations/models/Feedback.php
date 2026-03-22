<?php
namespace App\Models;

class Feedback extends BaseModel
{
    public function getFeedback($survey_id = null)
    {
        $empNameSql = $this->getEmployeeNameSql('e', 'employee_name');

        if ($survey_id !== null) {
            $sql = "SELECT f.*, $empNameSql, s.title as survey_title FROM eer_survey_feedback f 
                    JOIN employees e ON f.employee_id = e.eer_employee_id 
                    LEFT JOIN eer_surveys s ON f.survey_id = s.eer_survey_id 
                    WHERE f.survey_id = :survey_id 
                    ORDER BY f.created_at DESC";
            return $this->execute($sql, ['survey_id' => $survey_id])->fetchAll();
        }

        $sql = "SELECT f.*, $empNameSql, s.title as survey_title FROM eer_survey_feedback f 
                JOIN employees e ON f.employee_id = e.eer_employee_id 
                LEFT JOIN eer_surveys s ON f.survey_id = s.eer_survey_id 
                ORDER BY f.created_at DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function createFeedback($survey_id, $employee_id, $comment, $rating = null)
    {
        $sql = 'INSERT INTO eer_survey_feedback (survey_id, employee_id, comment, rating, created_at) VALUES (:survey_id, :employee_id, :comment, :rating, NOW())';
        $params = [
            'survey_id' => $survey_id,
            'employee_id' => $employee_id,
            'comment' => $comment,
            'rating' => $rating,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
