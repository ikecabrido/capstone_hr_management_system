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

        if (!isset($interview['feedback'])) {
            $interview['feedback'] = null;
        }

        // hr_assessment is loaded by model.getInterviewById but ensure key exists
        if (!isset($interview['hr_assessment'])) {
            $interview['hr_assessment'] = $this->interviewModel->getHrAssessmentByInterview($interviewId);
        }

        // Attach exit case details and engagement data
        if (!empty($interview['exit_case_type']) && !empty($interview['exit_case_id'])) {
            $interview['exit_case_details'] = $this->interviewModel->getExitCaseDetails(
                $interview['exit_case_type'],
                (int)$interview['exit_case_id']
            );
        }

        if (!empty($interview['employee_id'])) {
            $interview['engagement_records'] = $this->interviewModel->getEngagementRecords($interview['employee_id']);
        } else {
            $interview['engagement_records'] = [];
        }

        return $interview;
    }

    /**
     * Get HR assessment for an interview
     */
    public function getHrAssessment(int $interviewId): array
    {
        $assessment = $this->interviewModel->getHrAssessmentByInterview($interviewId);
        if (!$assessment) {
            return ['success' => false, 'message' => 'No HR assessment found'];
        }

        return ['success' => true, 'data' => $assessment];
    }

    /**
     * Save HR assessment (admin-only)
     */
    public function saveHrAssessment(int $interviewId, array $data): array
    {
        $role = strtolower((string)($_SESSION['user']['role'] ?? ''));
        $isAdmin = in_array($role, ['admin', 'superadmin', 'administrator', 'hr_admin'], true);

        if (empty($_SESSION['user']) || !$isAdmin) {
            return ['success' => false, 'message' => 'Permission denied'];
        }

        $userId = $_SESSION['user']['id'] ?? null;

        try {
            $saved = $this->interviewModel->saveHrAssessment($interviewId, $data, $userId);

            if ($saved) {
                return ['success' => true, 'message' => 'HR assessment saved successfully'];
            }

            return ['success' => false, 'message' => 'Failed to save HR assessment'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
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
    public function getInterviews(?string $status = null, int $page = 1, int $limit = 10, string $search = ''): array
    {
        // Auto-archive interviews that have remained completed for longer than the configured interval.
        $this->interviewModel->archiveDueCompletedInterviews(3);

        return $this->interviewModel->getAllInterviews($status, $page, $limit, $search);
    }

    /**
     * Complete interview
     */
    public function completeInterview(int $interviewId): array
    {
        try {
            $interview = $this->interviewModel->getInterviewById($interviewId);
            if (!$interview) {
                return ['success' => false, 'message' => 'Interview not found'];
            }

            $scheduledDate = $interview['scheduled_date'] ?? null;
            $today = date('Y-m-d');
            $canComplete = !$scheduledDate || $scheduledDate <= $today;

            if (!$canComplete) {
                return [
                    'success' => false,
                    'message' => 'This interview cannot be completed yet because its scheduled date is still in the future.'
                ];
            }

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
                return $this->getInterviews($status, $page, $limit, $search);

            case 'complete_interview':
                return $this->completeInterview($data['interview_id'] ?? 0);

            case 'archive_interview':
                return $this->archiveInterview($data['interview_id'] ?? 0);

            case 'unarchive_interview':
                return $this->unarchiveInterview($data['interview_id'] ?? 0);

            case 'get_archived_interviews':
                $page = (int)($data['page'] ?? 1);
                $limit = (int)($data['limit'] ?? 10);
                $search = $data['search'] ?? '';
                return $this->interviewModel->getArchivedInterviews($page, $limit, $search);

            case 'get_interview_details':
                return $this->getInterviewDetails($data['interview_id'] ?? 0);

            case 'get_hr_assessment':
                return $this->getHrAssessment($data['interview_id'] ?? 0);

            case 'save_hr_assessment':
                return $this->saveHrAssessment($data['interview_id'] ?? 0, $data['assessment'] ?? []);

            default:
                return parent::handleAjaxRequest($action, $data);
        }
    }

    /**
     * Verify current user can manage interview archives
     */
    private function userCanManageInterviewArchives(): bool
    {
        $role = strtolower((string)($_SESSION['user']['role'] ?? ''));
        return in_array($role, ['admin', 'superadmin', 'administrator', 'hr_admin'], true);
    }

    /**
     * Archive interview
     */
    public function archiveInterview(int $interviewId): array
    {
        if (empty($_SESSION['user']) || !$this->userCanManageInterviewArchives()) {
            return ['success' => false, 'message' => 'Permission denied'];
        }

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
        if (empty($_SESSION['user']) || !$this->userCanManageInterviewArchives()) {
            return ['success' => false, 'message' => 'Permission denied'];
        }

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
            if (!is_numeric($interviewId) || $interviewId <= 0) {
                return [
                    'success' => false,
                    'message' => 'A valid interview ID is required'
                ];
            }

            $interview = $this->interviewModel->getInterviewById($interviewId);

            if (!$interview) {
                return [
                    'success' => false,
                    'message' => 'Interview not found'
                ];
            }

            $employeeName = 'Unknown';
            if (!empty($interview['employee_id'])) {
                $employee = $this->interviewModel->getEmployeeById($interview['employee_id']);
                if ($employee) {
                    $employeeName = $employee['full_name'] ?? trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''));
                }
            }
            if ($employeeName === 'Unknown' && !empty($interview['employee_full_name'])) {
                $employeeName = $interview['employee_full_name'];
            }

            $interviewerName = 'Unknown';
            if (!empty($interview['interviewer_id'])) {
                $interviewer = $this->interviewModel->getUserById($interview['interviewer_id']);
                if ($interviewer) {
                    $interviewerName = $interviewer['full_name'] ?? trim(($interviewer['first_name'] ?? '') . ' ' . ($interviewer['last_name'] ?? ''));
                }
            }

            return [
                'success' => true,
                'data' => [
                    'id' => $interview['id'],
                    'employee_id' => $interview['employee_id'] ?? null,
                    'employee_name' => $employeeName,
                    'interviewer_name' => $interviewerName,
                    'scheduled_date' => $interview['scheduled_date'] ?? null,
                    'status' => $interview['status'] ?? null
                ]
            ];
        } catch (Exception $e) {
            error_log("Error getting interview details: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving interview details: ' . $e->getMessage()
            ];
        }
    }
}