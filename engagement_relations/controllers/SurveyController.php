<?php
namespace App\Controllers;

use App\Models\Survey;

class SurveyController
{
    private $surveyModel;

    public function __construct()
    {
        $this->surveyModel = new Survey();
    }

    public function show($surveyId)
    {
        return $this->surveyModel->getSurveyById($surveyId);
    }

    public function getSurveyResults($surveyId)
    {
        return $this->surveyModel->getSurveyResponses($surveyId);
    }

    public function calculateAverageRating($surveyId)
    {
        $responses = $this->getSurveyResults($surveyId);
        $total = 0;
        $count = 0;

        foreach ($responses as $response) {
            $answers = json_decode($response['answers'], true);
            foreach ($answers as $answer) {
                if (is_numeric($answer)) {
                    $total += $answer;
                    $count++;
                }
            }
        }

        return $count > 0 ? $total / $count : null;
    }

    public function index()
    {
        return $this->surveyModel->getSurveys();
    }

    public function store($title, $created_by, $questions)
    {
        $surveyId = $this->surveyModel->createSurvey($title, $created_by);
        foreach ($questions as $question) {
            $this->surveyModel->addQuestion($surveyId, $question['question_text']);
        }
        return $surveyId;
    }
}
