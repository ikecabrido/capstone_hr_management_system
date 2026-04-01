<?php

require_once __DIR__ . '/../models/ExitInterviewModel.php';

class ExitInterviewController extends ExitManagementController
{
    private ExitInterviewModel $interviewModel;

    public function __construct()
    {
        parent::__construct();
        $this->interviewModel = new ExitInterviewModel();
    }

    /**
     * Schedule exit interview
     */
    public function scheduleInterview(array $data): array
    {
        try {
            // Validate required fields
            $required = ['employee_id', 'interviewer_id', 'scheduled_date', 'scheduled_time'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $interviewId = $this->interviewModel->scheduleInterview($data);

            return [
                'success' => true,
                'message' => 'Exit interview scheduled successfully',
                'interview_id' => $interviewId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit interview feedback
     */
    public function submitFeedback(int $interviewId, array $feedback): array
    {
        try {
            // Validate required fields
            $required = ['overall_satisfaction', 'work_environment_rating', 'management_rating',
                        'compensation_rating', 'work_life_balance_rating', 'reason_for_leaving', 'would_recommend'];
            foreach ($required as $field) {
                if (!isset($feedback[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $success = $this->interviewModel->submitFeedback($interviewId, $feedback);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Feedback submitted successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to submit feedback'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get interview details
     */
    public function getInterview(int $interviewId): array
    {
        $interview = $this->interviewModel->getInterviewById($interviewId);

        if (!$interview) {
            return ['error' => 'Interview not found'];
        }

        // Get feedback if exists
        $feedback = $this->interviewModel->getFeedbackByInterview($interviewId);
        $interview['feedback'] = $feedback;

        return $interview;
    }

    /**
     * Get scheduled interviews
     */
    public function getScheduledInterviews(): array
    {
        return $this->interviewModel->getScheduledInterviews();
    }

    /**
     * Get all interviews (support status filter)
     */
    public function getInterviews(string $status = null): array
    {
        return $this->interviewModel->getAllInterviews($status);
    }

    /**
     * Complete interview
     */
    public function completeInterview(int $interviewId): array
    {
        try {
            $success = $this->interviewModel->updateInterviewStatus($interviewId, 'completed');

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Interview marked as completed'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to update interview status'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle AJAX requests for interviews
     */
    public function handleAjaxRequest(string $action, array $data = []): array
    {
        switch ($action) {
            case 'submit_interview':
            case 'update_interview':
                return $this->scheduleInterview($data);

            case 'submit_feedback':
            case 'update_feedback':
                return $this->submitFeedback(
                    $data['interview_id'] ?? 0,
                    $data['feedback'] ?? []
                );

            case 'get_interview':
                return $this->getInterview($data['interview_id'] ?? 0);

            case 'get_scheduled_interviews':
                return $this->getScheduledInterviews();

            case 'get_interviews':
                return $this->getInterviews($data['status'] ?? null);

            case 'complete_interview':
                return $this->completeInterview($data['interview_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }
}