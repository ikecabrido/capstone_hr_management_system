<?php

require_once __DIR__ . '/../models/IncidentModel.php';

class IncidentController
{
    private IncidentModel $model;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new IncidentModel($db);
    }

    // =====================================================
    // INCIDENT MANAGEMENT
    // =====================================================

    /**
     * Create a new incident
     */
    public function createIncident($data, $userId)
    {
        try {
            // Generate incident ID
            $incidentId = $this->generateIncidentId();
            
            // Prepare incident data
            $incidentData = [
                'incident_id' => $incidentId,
                'incident_type' => $data['incident_type'],
                'severity' => $data['severity'],
                'title' => $data['incident_type'] . ' - ' . date('Y-m-d'),
                'description' => $data['description'],
                'incident_date' => $data['incident_date'],
                'location' => $data['location'] ?? null,
                'respondent_id' => $data['respondent_id'],
                'reporter_name' => $data['reporter_name'],
                'reported_by' => $data['reported_by'] ?? 'Employee',
                'witnesses' => $data['witnesses'] ?? null,
                'status' => 'open',
                'current_workflow_step' => 'submitted',
                'created_by' => $userId
            ];
            
            $result = $this->model->createIncident($incidentData);
            
            if ($result['success']) {
                // Log activity
                $this->logActivity($result['incident_id'], 'created', 'Incident reported', $userId);
                
                // Create initial workflow step
                $this->model->createWorkflowStep($result['incident_id'], 'submitted', 'pending', $userId);
                
                // Send notification email
                $this->sendEmailNotification(
                    $result['incident_id'],
                    $userId,
                    'incident_submitted',
                    'New Incident Reported',
                    'A new incident has been reported and requires HR review.'
                );
                
                return [
                    'success' => true,
                    'message' => 'Incident reported successfully!',
                    'incident_id' => $result['incident_id']
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating incident: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all incidents
     */
    public function getAllIncidents()
    {
        return $this->model->getAllIncidents();
    }

    /**
     * Get incidents by status
     */
    public function getIncidentsByStatus($status)
    {
        return $this->model->getIncidentsByStatus($status);
    }

    /**
     * Get incident by ID
     */
    public function getIncidentById($id)
    {
        return $this->model->getIncidentById($id);
    }

    /**
     * Update incident status
     */
    public function updateIncidentStatus($incidentId, $newStatus, $userId)
    {
        try {
            $currentIncident = $this->model->getIncidentById($incidentId);
            if (!$currentIncident) {
                return ['success' => false, 'message' => 'Incident not found'];
            }
            
            $oldStatus = $currentIncident['current_workflow_step'];
            
            // Update status
            $result = $this->model->updateWorkflowStep($incidentId, $newStatus, $userId);
            
            if ($result['success']) {
                // Log activity
                $this->logActivity($incidentId, 'status_changed', 
                    "Status changed from $oldStatus to $newStatus", $userId);
                
                return [
                    'success' => true,
                    'message' => 'Status updated successfully!'
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ];
        }
    }

    // =====================================================
    // NOTICE TO EXPLAIN (NTE) MANAGEMENT
    // =====================================================

    /**
     * Issue Notice to Explain
     */
    public function issueNTE($data, $userId)
    {
        try {
            $incidentId = $data['incident_id'];
            $respondentId = $data['respondent_id'];
            
            // Generate NTE number
            $nteNumber = $this->generateNTENumber();
            
            // Calculate deadlines
            $nteDeadline = date('Y-m-d', strtotime('+48 hours'));
            $explanationDeadline = date('Y-m-d', strtotime('+72 hours'));
            
            // NTE content template
            $nteContent = $this->generateNTEContent($incidentId);
            
            // Create NTE record
            $nteData = [
                'incident_id' => $incidentId,
                'nte_number' => $nteNumber,
                'issued_to' => $respondentId,
                'issued_by' => $userId,
                'issue_date' => date('Y-m-d'),
                'deadline_date' => $nteDeadline,
                'nte_content' => $nteContent,
                'delivery_method' => 'email'
            ];
            
            $result = $this->model->createNTE($nteData);
            
            if ($result['success']) {
                // Update incident workflow step
                $this->model->updateWorkflowStep($incidentId, 'nte_issued', $userId);
                
                // Update incident deadlines
                $this->model->updateIncidentDeadlines($incidentId, $nteDeadline, $explanationDeadline);
                
                // Log activity
                $this->logActivity($incidentId, 'nte_issued', 
                    "Notice to Explain (NTE) issued - NTE#$nteNumber", $userId);
                
                // Send NTE notification email to employee
                $employee = $this->model->getEmployeeById($respondentId);
                if ($employee) {
                    $this->sendEmailNotification(
                        $incidentId,
                        $respondentId,
                        'nte_issued',
                        'Notice to Explain - Incident #' . $incidentId,
                        "You have been issued a Notice to Explain. Please submit your explanation by $explanationDeadline."
                    );
                }
                
                return [
                    'success' => true,
                    'message' => 'NTE issued successfully!',
                    'nte_number' => $nteNumber
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error issuing NTE: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Submit employee explanation
     */
    public function submitExplanation($data, $userId)
    {
        try {
            $incidentId = $data['incident_id'];
            $employeeId = $data['employee_id'];
            
            // Check if explanation is late
            $incident = $this->model->getIncidentById($incidentId);
            $isLate = false;
            
            if ($incident && $incident['explanation_deadline']) {
                $isLate = strtotime(date('Y-m-d')) > strtotime($incident['explanation_deadline']);
            }
            
            // Create explanation record
            $explanationData = [
                'incident_id' => $incidentId,
                'employee_id' => $employeeId,
                'explanation_text' => $data['explanation_text'],
                'submission_method' => 'online',
                'is_late' => $isLate ? 1 : 0,
                'late_reason' => $isLate ? $data['late_reason'] ?? null : null,
                'attachments' => $data['attachments'] ?? null
            ];
            
            $result = $this->model->createExplanation($explanationData);
            
            if ($result['success']) {
                // Update workflow step
                $this->model->updateWorkflowStep($incidentId, 'explanation_received', $userId);
                
                // Log activity
                $this->logActivity($incidentId, 'explanation_submitted', 
                    'Employee submitted explanation', $userId);
                
                // Send notification to HR
                $this->sendEmailNotification(
                    $incidentId,
                    $userId,
                    'explanation_received',
                    'Explanation Received',
                    'Employee has submitted explanation for incident #' . $incidentId
                );
                
                return [
                    'success' => true,
                    'message' => 'Explanation submitted successfully!'
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error submitting explanation: ' . $e->getMessage()
            ];
        }
    }

    // =====================================================
    // DECISION MAKING
    // =====================================================

    /**
     * Make disciplinary decision
     */
    public function makeDecision($data, $userId)
    {
        try {
            $incidentId = $data['incident_id'];
            $decision = $data['decision'];
            $actionDetails = $data['action_details'] ?? null;
            $duration = $data['duration_days'] ?? null;
            $effectiveDate = $data['effective_date'] ?? date('Y-m-d');
            
            // Get employee info
            $incident = $this->model->getIncidentById($incidentId);
            $employeeId = $incident['respondent_id'];
            
            // Create disciplinary action record
            $actionData = [
                'incident_id' => $incidentId,
                'action_type' => $decision,
                'action_details' => $actionDetails,
                'action_date' => date('Y-m-d'),
                'effective_date' => $effectiveDate,
                'duration_days' => $duration,
                'issued_by' => $userId,
                'is_final' => 1
            ];
            
            $result = $this->model->createDisciplinaryAction($actionData);
            
            if ($result['success']) {
                // Update workflow step
                $this->model->updateWorkflowStep($incidentId, 'decision_made', $userId);
                
                // Update incident with final decision
                $this->model->updateFinalDecision($incidentId, $decision, date('Y-m-d'));
                
                // Log activity
                $this->logActivity($incidentId, 'decision_made', 
                    "Disciplinary decision: " . ucfirst($decision), $userId);
                
                // Notify employee
                $employee = $this->model->getEmployeeById($employeeId);
                if ($employee) {
                    $this->sendEmailNotification(
                        $incidentId,
                        $employeeId,
                        'decision_notice',
                        'Disciplinary Decision - Incident #' . $incidentId,
                        "A decision has been made regarding your case. Action: " . ucfirst($decision)
                    );
                }
                
                return [
                    'success' => true,
                    'message' => 'Decision recorded successfully!'
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error making decision: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Close case
     */
    public function closeCase($incidentId, $closureReason, $userId)
    {
        try {
            // Update workflow step
            $result = $this->model->closeIncident($incidentId, $closureReason, $userId);
            
            if ($result['success']) {
                // Log activity
                $this->logActivity($incidentId, 'case_closed', 
                    'Case closed: ' . $closureReason, $userId);
                
                // Send notification
                $incident = $this->model->getIncidentById($incidentId);
                if ($incident && $incident['respondent_id']) {
                    $this->sendEmailNotification(
                        $incidentId,
                        $incident['respondent_id'],
                        'case_closed',
                        'Case Closed - Incident #' . $incidentId,
                        "Your case has been closed. Closure reason: $closureReason"
                    );
                }
                
                return [
                    'success' => true,
                    'message' => 'Case closed successfully!'
                ];
            }
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error closing case: ' . $e->getMessage()
            ];
        }
    }

    // =====================================================
    // WORKFLOW & HISTORY
    // =====================================================

    /**
     * Get workflow history for an incident
     */
    public function getWorkflowHistory($incidentId)
    {
        return $this->model->getWorkflowHistory($incidentId);
    }

    // =====================================================
    // STATISTICS
    // =====================================================

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        return [
            'total_incidents' => $this->model->countIncidents(),
            'pending_review' => $this->model->countIncidentsByStatus('submitted'),
            'nte_issued' => $this->model->countIncidentsByStatus('nte_issued'),
            'under_evaluation' => $this->model->countIncidentsByStatus('hr_evaluation'),
            'decisions_made' => $this->model->countIncidentsByStatus('decision_made'),
            'closed_cases' => $this->model->countIncidentsByStatus('closed')
        ];
    }

    /**
     * Get employees list
     */
    public function getEmployees()
    {
        return $this->model->getEmployees();
    }

    // =====================================================
    // HELPER METHODS
    // =====================================================

    /**
     * Generate unique incident ID
     */
    private function generateIncidentId()
    {
        return 'INC-' . date('Y') . '-' . strtoupper(uniqid());
    }

    /**
     * Generate NTE number
     */
    private function generateNTENumber()
    {
        return 'NTE-' . date('Y') . '-' . strtoupper(uniqid());
    }

    /**
     * Generate NTE content
     */
    private function generateNTEContent($incidentId)
    {
        return "
            NOTICE TO EXPLAIN
            
            Date: " . date('F d, Y') . "
            Incident Reference: $incidentId
            
            Dear Employee,
            
            You are hereby informed that an incident report has been filed involving you. 
            You are required to submit a written explanation regarding this matter within 
            48 hours from receipt of this notice.
            
            Please submit your explanation through the HR portal or directly to the HR Department.
            
            Failure to respond within the given period may result in administrative action 
            being taken without further notice.
            
            Sincerely,
            Human Resources Department
            Bestlink College of the Philippines
        ";
    }

    /**
     * Log activity
     */
    private function logActivity($incidentId, $activityType, $description, $userId)
    {
        $this->model->logActivity($incidentId, $activityType, $description, $userId);
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($incidentId, $recipientId, $emailType, $subject, $body)
    {
        try {
            $recipient = $this->model->getEmployeeById($recipientId);
            
            if ($recipient && isset($recipient['email'])) {
                $this->model->logEmail([
                    'incident_id' => $incidentId,
                    'recipient_id' => $recipientId,
                    'recipient_email' => $recipient['email'],
                    'email_type' => $emailType,
                    'subject' => $subject,
                    'body' => $body,
                    'sent_by' => $_SESSION['user_id'] ?? 1
                ]);
                
                // In production, integrate with PHPMailer or SMTP here
                // $this->sendSMTPEmail($recipient['email'], $subject, $body);
            }
        } catch (Exception $e) {
            // Log error but don't break the flow
            error_log("Email notification error: " . $e->getMessage());
        }
    }

    // =====================================================
    // EMAIL SENDING (Placeholder - integrate with PHPMailer)
    // =====================================================

    /**
     * Send SMTP email (placeholder)
     */
    private function sendSMTPEmail($to, $subject, $body)
    {
        // This is a placeholder. In production, use PHPMailer:
        // require_once '../assets/plugins/phpmailer/PHPMailerAutoload.php';
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = 'smtp.example.com';
        // $mail->SMTPAuth = true;
        // $mail->Username = 'hr@bestlink.edu.ph';
        // $mail->Password = 'password';
        // $mail->SMTPSecure = 'tls';
        // $mail->Port = 587;
        // $mail->setFrom('hr@bestlink.edu.ph', 'HR Department');
        // $mail->addAddress($to);
        // $mail->Subject = $subject;
        // $mail->Body = $body;
        // return $mail->send();
        
        return true;
    }
}
