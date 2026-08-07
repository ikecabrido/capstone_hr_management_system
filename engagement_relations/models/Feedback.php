<?php
namespace App\Models;

class Feedback extends BaseModel
{
    public function getFeedback($employee_id = null)
    {
        if ($employee_id !== null) {
            $sql = "SELECT f.*, COALESCE(e.full_name, u.full_name, u.username) AS employee_name FROM eer_survey_feedback_id f 
                LEFT JOIN employees e ON f.employee_id = e.employee_id 
                LEFT JOIN users u ON e.user_id = u.id 
                WHERE f.employee_id = :employee_id 
                ORDER BY f.eer_survey_feedback_id_id DESC";
            return $this->execute($sql, ['employee_id' => $employee_id])->fetchAll();
        }

        $sql = "SELECT f.*, COALESCE(e.full_name, u.full_name, u.username) AS employee_name FROM eer_survey_feedback_id f 
            LEFT JOIN employees e ON f.employee_id = e.employee_id 
            LEFT JOIN users u ON e.user_id = u.id 
            ORDER BY f.eer_survey_feedback_id_id DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function createFeedback(
        $employee_id,
        $comment,
        $rating = null,
        $survey_id = null,
        $category = 'Performance',
        $is_anonymous = 0,
        $evaluation_date = null,
        $evaluator_type = 'Self'
    ) {
        $sql = 'INSERT INTO eer_survey_feedback_id (employee_id, comment, rating, survey_id, category, is_anonymous, evaluator_type, evaluation_date) VALUES (:employee_id, :comment, :rating, :survey_id, :category, :is_anonymous, :evaluator_type, :evaluation_date)';
        $params = [
            'employee_id' => $employee_id,
            'rating' => $rating ?? 3,
            'comment' => $comment,
            'survey_id' => $survey_id,
            'category' => $category,
            'is_anonymous' => $is_anonymous,
            'evaluator_type' => $evaluator_type,
            'evaluation_date' => $evaluation_date ?? date('Y-m-d H:i:s'),
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
    