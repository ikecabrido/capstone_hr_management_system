<?php

require_once __DIR__ . '/../models/SurveyModel.php';

class SurveyController extends ExitManagementController
{
    private SurveyModel $surveyModel;

    public function __construct()
    {
        parent::__construct();
        $this->surveyModel = new SurveyModel();
    }

    /**
     * Create survey
     */
    public function createSurvey(array $data): array
    {
        try {
            // Validate required fields
            $required = ['title', 'start_date', 'end_date'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $surveyId = $this->surveyModel->createSurvey($data);

            return [
                'success' => true,
                'message' => 'Survey created successfully',
                'survey_id' => $surveyId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit survey response
     */
    public function submitSurveyResponse(int $surveyId, int $employeeId, array $responses): array
    {
        try {
            $success = $this->surveyModel->submitSurveyResponse($surveyId, $employeeId, $responses);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Survey response submitted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to submit survey response'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get survey details
     */
    public function getSurvey(int $surveyId): array
    {
        $survey = $this->surveyModel->getSurveyById($surveyId);

        if (!$survey) {
            return ['error' => 'Survey not found'];
        }

        // Get questions
        $questions = $this->surveyModel->getSurveyQuestions($surveyId);
        $survey['questions'] = $questions;

        return $survey;
    }

    /**
     * Get active surveys for employee
     */
    public function getActiveSurveysForEmployee(int $employeeId): array
    {
        return $this->surveyModel->getActiveSurveysForEmployee($employeeId);
    }

    /**
     * Generate survey report
     */
    public function generateSurveyReport(int $surveyId): array
    {
        return $this->surveyModel->generateSurveyReport($surveyId);
    }

    /**
     * Get survey responses
     */
    public function getSurveyResponses(int $surveyId): array
    {
        return $this->surveyModel->getSurveyResponses($surveyId);
    }

    /**
     * Get survey response details
     */
    public function getSurveyResponseDetails(int $responseId): array
    {
        return $this->surveyModel->getSurveyResponseDetails($responseId);
    }

    /**
     * Get all surveys
     */
    public function getSurveys(): array
    {
        return $this->surveyModel->getAllSurveys();
    }

    /**
     * Duplicate survey
     */
    public function duplicateSurvey(int $surveyId): array
    {
        try {
            $newSurveyId = $this->surveyModel->duplicateSurvey($surveyId);

            if ($newSurveyId) {
                return [
                    'success' => true,
                    'message' => 'Survey duplicated successfully',
                    'survey_id' => $newSurveyId
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to duplicate survey'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle AJAX requests for surveys
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'create_survey':
                return $this->createSurvey($data);

            case 'submit_survey_response':
                return $this->submitSurveyResponse(
                    $data['survey_id'] ?? 0,
                    $data['employee_id'] ?? 0,
                    $data['responses'] ?? []
                );

            case 'get_survey':
                return $this->getSurvey($data['survey_id'] ?? 0);

            case 'get_active_surveys':
                return $this->getActiveSurveysForEmployee($data['employee_id'] ?? 0);

            case 'generate_survey_report':
                return $this->generateSurveyReport($data['survey_id'] ?? 0);

            case 'get_survey_responses':
                return $this->getSurveyResponses($data['survey_id'] ?? 0);

            case 'get_response_details':
                return $this->getSurveyResponseDetails($data['response_id'] ?? 0);

            case 'get_surveys':
                return $this->getSurveys();

            case 'duplicate_survey':
                return $this->duplicateSurvey($data['survey_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}