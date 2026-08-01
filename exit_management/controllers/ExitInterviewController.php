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
            $required = ['exit_case_type', 'exit_case_id', 'employee_id', 'interviewer_id', 'scheduled_date', 'scheduled_time'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }

            $exitCaseType = $data['exit_case_type'];
            $exitCaseId = (int)$data['exit_case_id'];
            $approvedCase = $this->interviewModel->getApprovedExitCase($exitCaseType, $exitCaseId);
            $employeeId = (string)$data['employee_id'];

            if (!$approvedCase || (string)$approvedCase['employee_id'] !== $employeeId) {
                return ['success' => false, 'message' => 'Selected exit case is not approved or does not match the employee'];
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
    public function getInterviews(?string $status = null): array
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
                return $this->scheduleInterview($data);

            case 'update_interview':
                $interviewId = (int)($data['interview_id'] ?? 0);
                if ($interviewId <= 0) {
                    return ['success' => false, 'message' => 'Interview ID is required for update'];
                }

                $required = ['exit_case_type', 'exit_case_id', 'employee_id', 'interviewer_id', 'scheduled_date', 'scheduled_time'];
                foreach ($required as $field) {
                    if (empty($data[$field])) {
                        return ['success' => false, 'message' => "Field '$field' is required for update"];
                    }
                }

                $exitCaseType = $data['exit_case_type'];
                $exitCaseId = (int)$data['exit_case_id'];
                $approvedCase = $this->interviewModel->getApprovedExitCase($exitCaseType, $exitCaseId);
                $employeeId = (string)$data['employee_id'];

                if (!$approvedCase || (string)$approvedCase['employee_id'] !== $employeeId) {
                    return ['success' => false, 'message' => 'Selected exit case is not approved or does not match the employee'];
                }

                unset($data['interview_id']);
                $success = $this->interviewModel->updateInterview($interviewId, $data);

                if ($success) {
                    return ['success' => true, 'message' => 'Exit interview updated successfully', 'interview_id' => $interviewId];
                }

                return ['success' => false, 'message' => 'Failed to update exit interview'];

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
                $status = $data['status'] ?? null;
                $page = (int)($data['page'] ?? 1);
                $limit = (int)($data['limit'] ?? 10);
                $search = $data['search'] ?? '';
                return $this->interviewModel->getAllInterviews($status, $page, $limit, $search);

            case 'complete_interview':
                return $this->completeInterview($data['interview_id'] ?? 0);

            case 'archive_interview':
                return $this->archiveInterview($data['interview_id'] ?? 0);

            case 'unarchive_interview':
                return $this->unarchiveInterview($data['interview_id'] ?? 0);

            case 'get_interview_details':
                return $this->getInterviewDetails($data['interview_id'] ?? 0);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }

    /**
     * Archive interview
     */
    public function archiveInterview(int $interviewId): array
    {
        try {
            $archiveReason = $_POST['archive_reason'] ?? 'Manual archive';
            $success = $this->interviewModel->archiveInterview($interviewId, $archiveReason);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Interview archived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to archive interview'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Unarchive interview
     */
    public function unarchiveInterview(int $interviewId): array
    {
        try {
            $success = $this->interviewModel->unarchiveInterview($interviewId);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Interview unarchived successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to unarchive interview'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get interview details for archiving
     */
    private function getInterviewDetails(int $interviewId): array
    {
        try {
            if (empty($interviewId)) {
                return [
                    'success' => false,
                    'message' => 'Interview ID is required'
                ];
            }

            $interview = $this->interviewModel->getInterviewById($interviewId);

            if (!$interview) {
                return [
                    'success' => false,
                    'message' => 'Interview not found'
                ];
            }

            // Get employee name
            $employee = $this->interviewModel->getEmployeeById($interview['employee_id']);
            // Get interviewer name
            $interviewer = $this->interviewModel->getUserById($interview['interviewer_id']);

            return [
                'success' => true,
                'data' => [
                    'id' => $interview['id'],
                    'employee_id' => $interview['employee_id'],
                    'employee_name' => $employee ? ($employee['full_name'] ?? trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''))) : 'Unknown',
                    'interviewer_name' => $interviewer ? ($interviewer['full_name'] ?? trim(($interviewer['first_name'] ?? '') . ' ' . ($interviewer['last_name'] ?? ''))) : 'Unknown',
                    'scheduled_date' => $interview['scheduled_date'],
                    'status' => $interview['status']
                ]
            ];
        } catch (Exception $e) {
            error_log("Error getting interview details: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving interview details'
            ];
        }
    }
}