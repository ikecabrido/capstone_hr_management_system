<?php
namespace App\Controllers;

use App\Models\Feedback;

class FeedbackController
{
    private $feedback;

    public function __construct()
    {
        $this->feedback = new Feedback();
    }

    public function index($survey_id = null)
    {
        return $this->feedback->getFeedback($survey_id);
    }

    public function store($survey_id, $employee_id, $comment, $rating = null)
    {
        return $this->feedback->createFeedback($survey_id, $employee_id, $comment, $rating);
    }

    public function generateSurveyResults($surveyId)
    {
        return $this->feedback->getSurveyResults($surveyId);
    }

    public function performSentimentAnalysis($surveyId)
    {
        return $this->feedback->analyzeSentiment($surveyId);
    }
}
