<?php
namespace App\Models;

class SurveyResponse extends BaseModel
{
    public function submit($data)
    {
        $sql = 'INSERT INTO survey_responses (survey_id, employee_id, answers, submitted_at) VALUES (:survey_id, :employee_id, :answers, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function getBySurvey($survey_id)
    {
        return $this->execute('SELECT * FROM survey_responses WHERE survey_id = :survey_id', ['survey_id' => $survey_id])->fetchAll();
    }
}
