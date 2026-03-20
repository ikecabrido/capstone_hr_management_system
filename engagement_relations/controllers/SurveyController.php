<?php
namespace App\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;

class SurveyController
{
    private $survey;
    private $surveyResponse;

    public function __construct()
    {
        $this->survey = new Survey();
        $this->surveyResponse = new SurveyResponse();
    }

    public function create()
    {
        // Return data required for form (if any) to create survey
        return [
            'title' => '',
            'questions' => [],
        ];
    }

    public function store($title, $created_by, $questions = [])
    {
        $surveyId = $this->survey->createSurvey($title, $created_by);

        foreach ($questions as $q) {
            $this->survey->addQuestion($surveyId, $q['question_text'], $q['type'] ?? 'text');
        }

        return $surveyId;
    }

    public function index()
    {
        return $this->survey->getSurveys();
    }

    public function show($id)
    {
        return $this->survey->getWithQuestions($id);
    }

    public function delete($id)
    {
        return $this->survey->deleteSurvey($id);
    }

    public function getResponses($survey_id)
    {
        return $this->surveyResponse->getBySurvey($survey_id);
    }

    public function submit($survey_id, $employee_id, $answers)
    {
        return $this->survey->submitResponse($survey_id, $employee_id, $answers);
    }
}

