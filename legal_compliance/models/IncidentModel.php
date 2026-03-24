<?php

class IncidentModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =====================================================
    // INCIDENT CRUD OPERATIONS
    // =====================================================

    /**
     * Create a new incident
     */
    public function createIncident($data)
    {
        try {
            $sql = "INSERT INTO incidents (
                incident_id, incident_type, severity, title, description, 
                incident_date, location, respondent_id, reporter_name, reported_by,
                witnesses, status, current_workflow_step, created_by
            ) VALUES (
                :incident_id, :incident_type, :severity, :title, :description,
                :incident_date, :location, :respondent_id, :reporter_name, :reported_by,
                :witnesses, 'open', 'submitted', :created_by
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $data['incident_id'],
                ':incident_type' => $data['incident_type'],
                ':severity' => $data['severity'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':incident_date' => $data['incident_date'],
                ':location' => $data['location'],
                ':respondent_id' => $data['respondent_id'],
                ':reporter_name' => $data['reporter_name'],
                ':reported_by' => $data['reported_by'],
                ':witnesses' => $data['witnesses'],
                ':created_by' => $data['created_by']
            ]);
            
            $incidentId = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'incident_id' => $incidentId
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all incidents
     */
    public function getAllIncidents()
    {
        try {
            $sql = "SELECT i.*, 
                           e.first_name as respondent_first_name,
                           e.last_name as respondent_last_name,
                           rep.first_name as reporter_first_name,
                           rep.last_name as reporter_last_name
                    FROM incidents i
                    LEFT JOIN employees e ON i.respondent_id = e.id
                    LEFT JOIN employees rep ON i.reporter_id = rep.id
                    ORDER BY i.created_at DESC";
            
            $stmt = $this->db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build full names for each result
            foreach ($results as &$result) {
                $result['respondent_name'] = '';
                if (!empty($result['respondent_first_name']) || !empty($result['respondent_last_name'])) {
                    $result['respondent_name'] = trim(($result['respondent_first_name'] ?? '') . ' ' . ($result['respondent_last_name'] ?? ''));
                }
                
                $result['reporter_name'] = '';
                if (!empty($result['reporter_first_name']) || !empty($result['reporter_last_name'])) {
                    $result['reporter_name'] = trim(($result['reporter_first_name'] ?? '') . ' ' . ($result['reporter_last_name'] ?? ''));
                }
            }
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("Error in getAllIncidents: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get incidents by status
     */
    public function getIncidentsByStatus($status)
    {
        try {
            $sql = "SELECT i.*, 
                           e.first_name as respondent_first_name,
                           e.last_name as respondent_last_name,
                           rep.first_name as reporter_first_name,
                           rep.last_name as reporter_last_name
                    FROM incidents i
                    LEFT JOIN employees e ON i.respondent_id = e.id
                    LEFT JOIN employees rep ON i.reporter_id = rep.id
                    WHERE i.current_workflow_step = :status
                    ORDER BY i.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build full names for each result
            foreach ($results as &$result) {
                $result['respondent_name'] = '';
                if (!empty($result['respondent_first_name']) || !empty($result['respondent_last_name'])) {
                    $result['respondent_name'] = trim(($result['respondent_first_name'] ?? '') . ' ' . ($result['respondent_last_name'] ?? ''));
                }
                
                $result['reporter_name'] = '';
                if (!empty($result['reporter_first_name']) || !empty($result['reporter_last_name'])) {
                    $result['reporter_name'] = trim(($result['reporter_first_name'] ?? '') . ' ' . ($result['reporter_last_name'] ?? ''));
                }
            }
            
            return $results;
            
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get incident by ID
     */
    public function getIncidentById($id)
    {
        try {
            $sql = "SELECT i.*, 
                           e.first_name as respondent_first_name,
                           e.last_name as respondent_last_name,
                           e.email as respondent_email,
                           rep.first_name as reporter_first_name,
                           rep.last_name as reporter_last_name
                    FROM incidents i
                    LEFT JOIN employees e ON i.respondent_id = e.id
                    LEFT JOIN employees rep ON i.reporter_id = rep.id
                    WHERE i.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Build full names if individual parts exist
            if ($result) {
                $result['respondent_name'] = '';
                if (!empty($result['respondent_first_name']) || !empty($result['respondent_last_name'])) {
                    $result['respondent_name'] = trim(($result['respondent_first_name'] ?? '') . ' ' . ($result['respondent_last_name'] ?? ''));
                }
                
                $result['reporter_name'] = '';
                if (!empty($result['reporter_first_name']) || !empty($result['reporter_last_name'])) {
                    $result['reporter_name'] = trim(($result['reporter_first_name'] ?? '') . ' ' . ($result['reporter_last_name'] ?? ''));
                }
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error in getIncidentById: " . $e->getMessage());
            return null;
        }
    }

    // =====================================================
    // WORKFLOW OPERATIONS
    // =====================================================

    /**
     * Create initial workflow step
     */
    public function createWorkflowStep($incidentId, $step, $status, $userId)
    {
        try {
            $sql = "INSERT INTO incident_workflow (
                incident_id, step, step_status, started_at, performed_by
            ) VALUES (
                :incident_id, :step, :status, NOW(), :performed_by
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $incidentId,
                ':step' => $step,
                ':status' => $status,
                ':performed_by' => $userId
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update workflow step
     */
    public function updateWorkflowStep($incidentId, $newStep, $userId)
    {
        try {
            // Get current step
            $currentSql = "SELECT step FROM incident_workflow 
                          WHERE incident_id = :id 
                          ORDER BY id DESC LIMIT 1";
            $currentStmt = $this->db->prepare($currentSql);
            $currentStmt->execute([':id' => $incidentId]);
            $currentStep = $currentStmt->fetch(PDO::FETCH_ASSOC);
            
            // Mark current step as completed
            if ($currentStep) {
                $updateSql = "UPDATE incident_workflow 
                             SET step_status = 'completed', completed_at = NOW() 
                             WHERE incident_id = :id AND step = :step";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ':id' => $incidentId,
                    ':step' => $currentStep['step']
                ]);
            }
            
            // Create new step
            $deadline = $this->calculateDeadline($newStep);
            
            $insertSql = "INSERT INTO incident_workflow (
                incident_id, step, step_status, started_at, deadline, performed_by
            ) VALUES (
                :incident_id, :step, 'in_progress', NOW(), :deadline, :performed_by
            )";
            
            $insertStmt = $this->db->prepare($insertSql);
            $insertStmt->execute([
                ':incident_id' => $incidentId,
                ':step' => $newStep,
                ':deadline' => $deadline,
                ':performed_by' => $userId
            ]);
            
            // Update incident status
            $updateIncidentSql = "UPDATE incidents 
                                 SET current_workflow_step = :step, 
                                     status_changed_at = NOW() 
                                 WHERE id = :id";
            $updateIncidentStmt = $this->db->prepare($updateIncidentSql);
            $updateIncidentStmt->execute([
                ':step' => $newStep,
                ':id' => $incidentId
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Calculate deadline based on step
     */
    private function calculateDeadline($step)
    {
        $hours = [
            'under_review' => 48,
            'nte_issued' => 48,
            'explanation_received' => 72,
            'hr_evaluation' => 48,
            'decision_made' => 24,
            'final_action' => 24
        ];
        
        $hours = $hours[$step] ?? 0;
        return $hours > 0 ? date('Y-m-d H:i:s', strtotime("+{$hours} hours")) : null;
    }

    /**
     * Get workflow history
     */
    public function getWorkflowHistory($incidentId)
    {
        try {
            $sql = "SELECT w.*, e.first_name as performed_by_first_name, e.last_name as performed_by_last_name
                    FROM incident_workflow w
                    LEFT JOIN employees e ON w.performed_by = e.id
                    WHERE w.incident_id = :id
                    ORDER BY w.id ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $incidentId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build full name
            foreach ($results as &$result) {
                $result['performed_by_name'] = '';
                if (!empty($result['performed_by_first_name']) || !empty($result['performed_by_last_name'])) {
                    $result['performed_by_name'] = trim(($result['performed_by_first_name'] ?? '') . ' ' . ($result['performed_by_last_name'] ?? ''));
                }
            }
            
            return $results;
            
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Update incident deadlines
     */
    public function updateIncidentDeadlines($incidentId, $nteDeadline, $explanationDeadline)
    {
        try {
            $sql = "UPDATE incidents 
                   SET nte_deadline = :nte_deadline, 
                       explanation_deadline = :explanation_deadline 
                   WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nte_deadline' => $nteDeadline,
                ':explanation_deadline' => $explanationDeadline,
                ':id' => $incidentId
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // NTE OPERATIONS
    // =====================================================

    /**
     * Create NTE record
     */
    public function createNTE($data)
    {
        try {
            $sql = "INSERT INTO notice_to_explain (
                incident_id, nte_number, issued_to, issued_by, issue_date,
                deadline_date, nte_content, delivery_method
            ) VALUES (
                :incident_id, :nte_number, :issued_to, :issued_by, :issue_date,
                :deadline_date, :nte_content, :delivery_method
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $data['incident_id'],
                ':nte_number' => $data['nte_number'],
                ':issued_to' => $data['issued_to'],
                ':issued_by' => $data['issued_by'],
                ':issue_date' => $data['issue_date'],
                ':deadline_date' => $data['deadline_date'],
                ':nte_content' => $data['nte_content'],
                ':delivery_method' => $data['delivery_method']
            ]);
            
            return ['success' => true, 'nte_id' => $this->db->lastInsertId()];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // EXPLANATION OPERATIONS
    // =====================================================

    /**
     * Create explanation record
     */
    public function createExplanation($data)
    {
        try {
            $sql = "INSERT INTO explanations (
                incident_id, employee_id, explanation_text, submission_method,
                is_late, late_reason, attachments
            ) VALUES (
                :incident_id, :employee_id, :explanation_text, :submission_method,
                :is_late, :late_reason, :attachments
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $data['incident_id'],
                ':employee_id' => $data['employee_id'],
                ':explanation_text' => $data['explanation_text'],
                ':submission_method' => $data['submission_method'],
                ':is_late' => $data['is_late'],
                ':late_reason' => $data['late_reason'],
                ':attachments' => $data['attachments']
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // DISCIPLINARY ACTION OPERATIONS
    // =====================================================

    /**
     * Create disciplinary action record
     */
    public function createDisciplinaryAction($data)
    {
        try {
            $sql = "INSERT INTO disciplinary_actions (
                incident_id, action_type, action_details, action_date,
                effective_date, duration_days, issued_by, is_final
            ) VALUES (
                :incident_id, :action_type, :action_details, :action_date,
                :effective_date, :duration_days, :issued_by, :is_final
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $data['incident_id'],
                ':action_type' => $data['action_type'],
                ':action_details' => $data['action_details'],
                ':action_date' => $data['action_date'],
                ':effective_date' => $data['effective_date'],
                ':duration_days' => $data['duration_days'],
                ':issued_by' => $data['issued_by'],
                ':is_final' => $data['is_final']
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update final decision
     */
    public function updateFinalDecision($incidentId, $decision, $date)
    {
        try {
            $sql = "UPDATE incidents 
                   SET final_decision = :decision, 
                       final_decision_date = :date 
                   WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':decision' => $decision,
                ':date' => $date,
                ':id' => $incidentId
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Close incident
     */
    public function closeIncident($incidentId, $reason, $userId)
    {
        try {
            $sql = "UPDATE incidents 
                   SET current_workflow_step = 'closed',
                       closed_at = NOW(),
                       closure_reason = :reason 
                   WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':reason' => $reason,
                ':id' => $incidentId
            ]);
            
            // Add to workflow
            $this->createWorkflowStep($incidentId, 'closed', 'completed', $userId);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // ACTIVITY LOGGING
    // =====================================================

    /**
     * Log activity
     */
    public function logActivity($incidentId, $activityType, $description, $userId)
    {
        try {
            $sql = "INSERT INTO incident_activity_log (
                incident_id, activity_type, activity_description, performed_by, created_at
            ) VALUES (
                :incident_id, :activity_type, :activity_description, :performed_by, NOW()
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $incidentId,
                ':activity_type' => $activityType,
                ':activity_description' => $description,
                ':performed_by' => $userId
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // EMAIL LOGGING
    // =====================================================

    /**
     * Log email notification
     */
    public function logEmail($data)
    {
        try {
            $sql = "INSERT INTO incident_email_log (
                incident_id, recipient_id, recipient_email, email_type,
                subject, body, sent_by, sent_at, status
            ) VALUES (
                :incident_id, :recipient_id, :recipient_email, :email_type,
                :subject, :body, :sent_by, NOW(), 'pending'
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $data['incident_id'],
                ':recipient_id' => $data['recipient_id'],
                ':recipient_email' => $data['recipient_email'],
                ':email_type' => $data['email_type'],
                ':subject' => $data['subject'],
                ':body' => $data['body'],
                ':sent_by' => $data['sent_by']
            ]);
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =====================================================
    // STATISTICS
    // =====================================================

    /**
     * Count all incidents
     */
    public function countIncidents()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM incidents";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Count incidents by status
     */
    public function countIncidentsByStatus($status)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM incidents WHERE current_workflow_step = :status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            return 0;
        }
    }

    // =====================================================
    // EMPLOYEE DATA
    // =====================================================

    /**
     * Get employee by ID
     */
    public function getEmployeeById($id)
    {
        try {
            $sql = "SELECT id, username, full_name, email FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get all employees
     */
    public function getEmployees()
    {
        try {
            $sql = "SELECT id, first_name, last_name, email, department, position 
                   FROM employees 
                   WHERE status = 'Active' 
                   ORDER BY last_name ASC, first_name ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // =====================================================
    // PROGRESSIVE DISCIPLINE LOGIC
    // =====================================================
    
    /**
     * Count offenses for an employee within a time period
     * @param int $employeeId Employee ID
     * @param string $incidentType Type of incident (optional)
     * @param int $months Number of months to look back (default 12)
     * @return int Number of previous offenses
     */
    public function countOffenses($employeeId, $incidentType = null, $months = 12)
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM incidents 
                    WHERE respondent_id = :employee_id 
                    AND status IN ('resolved', 'closed', 'final_action', 'closed_no_violation')
                    AND incident_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)";
            
            $params = [
                ':employee_id' => $employeeId,
                ':months' => $months
            ];
            
            if ($incidentType) {
                $sql .= " AND incident_type = :incident_type";
                $params[':incident_type'] = $incidentType;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error counting offenses: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get suggested disciplinary action based on offense count
     * @param int $offenseCount Number of previous offenses
     * @return string Suggested action type
     */
    public function getSuggestedAction($offenseCount)
    {
        $rules = [
            1 => 'verbal_warning',
            2 => 'written_warning',
            3 => 'suspension',
            4 => 'termination'
        ];
        
        // For 4th offense and beyond, suggest termination
        if ($offenseCount >= 4) {
            return 'termination';
        }
        
        return $rules[$offenseCount] ?? 'verbal_warning';
    }
    
    /**
     * Get employee offense history
     * @param int $employeeId Employee ID
     * @param int $months Number of months to look back
     * @return array List of previous incidents
     */
    public function getOffenseHistory($employeeId, $months = 12)
    {
        try {
            $sql = "SELECT i.*, da.action_type, da.action_date 
                    FROM incidents i
                    LEFT JOIN disciplinary_actions da ON i.id = da.incident_id
                    WHERE i.respondent_id = :employee_id 
                    AND i.status IN ('resolved', 'closed', 'final_action', 'closed_no_violation')
                    AND i.incident_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                    ORDER BY i.incident_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':employee_id' => $employeeId,
                ':months' => $months
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get progressive discipline statistics for dashboard
     * @return array Statistics including offense distribution
     */
    public function getDisciplineStats()
    {
        try {
            $stats = [];
            
            // Count by suggested action
            $sql = "SELECT suggested_action, COUNT(*) as count 
                    FROM incidents 
                    WHERE suggested_action IS NOT NULL 
                    AND status NOT IN ('closed', 'resolved')
                    GROUP BY suggested_action";
            
            $stmt = $this->db->query($sql);
            $stats['by_action'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top offenders
            $sql = "SELECT e.first_name, e.last_name, e.id, COUNT(i.id) as offense_count
                    FROM incidents i
                    JOIN employees e ON i.respondent_id = e.id
                    WHERE i.status IN ('resolved', 'closed', 'final_action')
                    AND i.incident_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY e.id, e.first_name, e.last_name
                    ORDER BY offense_count DESC
                    LIMIT 10";
            
            $stmt = $this->db->query($sql);
            $stats['top_offenders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Apply final disciplinary action with progressive discipline logic
     * @param int $incidentId Incident ID
     * @param string $actionType Selected action type
     * @param array $actionDetails Additional details
     * @param int $userId User performing the action
     * @param bool $isOverride Whether HR is overriding the suggested action
     * @param string $overrideReason Reason for override
     * @return array Result with success status
     */
    public function applyDisciplinaryAction($incidentId, $actionType, $actionDetails, $userId, $isOverride = false, $overrideReason = null)
    {
        try {
            // Get incident details
            $incident = $this->getIncidentById($incidentId);
            if (!$incident) {
                return ['success' => false, 'message' => 'Incident not found'];
            }
            
            // Start transaction
            $this->db->beginTransaction();
            
            // Insert disciplinary action
            $sql = "INSERT INTO disciplinary_actions (
                incident_id, action_type, action_details, action_date, effective_date, 
                duration_days, issued_by, is_final
            ) VALUES (
                :incident_id, :action_type, :action_details, CURDATE(), CURDATE(), 
                :duration_days, :issued_by, 1
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':incident_id' => $incidentId,
                ':action_type' => $actionType,
                ':action_details' => $actionDetails['details'] ?? null,
                ':duration_days' => $actionDetails['duration_days'] ?? null,
                ':issued_by' => $userId
            ]);
            
            // Update incident with final decision
            $sql = "UPDATE incidents SET 
                final_decision = :action_type,
                final_decision_date = CURDATE(),
                current_workflow_step = 'final_action',
                is_override = :is_override,
                override_reason = :override_reason
                WHERE id = :incident_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':action_type' => $actionType,
                ':is_override' => $isOverride ? 1 : 0,
                ':override_reason' => $overrideReason,
                ':incident_id' => $incidentId
            ]);
            
            // Log the activity
            $this->logActivity($incidentId, 'disciplinary_action', 
                "Disciplinary action applied: $actionType" . ($isOverride ? " (Override)" : ""), $userId);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Disciplinary action applied successfully'
            ];
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return [
                'success' => false,
                'message' => 'Error applying disciplinary action: ' . $e->getMessage()
            ];
        }
    }
}
