<?php
/**
 * Comprehensive Policy Management Controller
 * Complete workflow: Create → Submit → Approve → Publish → Acknowledge
 */

require_once "../../auth/database.php";

class PolicyWorkflowController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // ==================== CRUD OPERATIONS ====================
    
    /**
     * Create new policy (Draft status)
     */
    public function createPolicy($data, $userId)
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO policies (title, category, category_id, version, content, status, created_by, is_mandatory, acknowledgment_required) 
                    VALUES (?, ?, ?, ?, ?, 'Draft', ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['category'],
                $data['category_id'] ?? null,
                $data['version'] ?? '1.0',
                $data['content'],
                $userId,
                $data['is_mandatory'] ?? 0,
                $data['acknowledgment_required'] ?? 1
            ]);
            
            $policyId = $this->db->lastInsertId();
            
            // Create first version
            $this->createVersion($policyId, $data['content'], 'Initial version', $userId);
            
            $this->db->commit();
            return ['success' => true, 'policy_id' => $policyId];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Update existing policy
     */
    public function updatePolicy($id, $data, $userId)
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE policies SET 
                    title = ?, category = ?, category_id = ?, version = ?, content = ?, 
                    is_mandatory = ?, acknowledgment_required = ?, updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['category'],
                $data['category_id'] ?? null,
                $data['version'],
                $data['content'],
                $data['is_mandatory'] ?? 0,
                $data['acknowledgment_required'] ?? 1,
                $id
            ]);
            
            // Create new version
            $this->createVersion($id, $data['content'], $data['changes_summary'] ?? 'Updated', $userId);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Delete policy (Draft or Pending Approval status)
     */
    public function deletePolicy($id)
    {
        $stmt = $this->db->prepare("DELETE FROM policies WHERE id = ? AND status IN ('Draft', 'Pending Approval')");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get single policy
     */
    public function getPolicy($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM policies WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all policies with filters
     */
    public function getAllPolicies($status = null, $category = null, $search = null)
    {
        try {
            // Build query
            $sql = "SELECT * FROM policies WHERE 1=1";
            $params = [];
            
            if ($status && $status !== 'null' && $status !== 'all') {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            if ($search) {
                $sql .= " AND (title LIKE ? OR content LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            $sql .= " ORDER BY id DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (Exception $e) {
            // Return empty array on error
            return [];
        }
    }
    
    // ==================== WORKFLOW TRANSITIONS ====================
    
    /**
     * Submit policy for initial approval (from Draft)
     */
    public function submitForApproval($id, $userId)
    {
        try {
            $this->db->beginTransaction();
            
            // Update status
            $stmt = $this->db->prepare("UPDATE policies SET status = 'Pending Approval', updated_at = NOW() WHERE id = ? AND status = 'Draft'");
            $stmt->execute([$id]);
            
            // Log action
            $this->logAction($id, 'Submitted', 'Submitted for approval', $userId);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Supervisor level approval (first level)
     */
    public function supervisorApprove($id, $userId, $remarks = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Update status to Supervisor Approved
            $stmt = $this->db->prepare("UPDATE policies SET 
                    status = 'Supervisor Approved', approved_by = ?, approved_at = NOW(), updated_at = NOW() 
                    WHERE id = ? AND status = 'Pending Approval'");
            $stmt->execute([$userId, $id]);
            
            // Log action
            $this->logAction($id, 'Supervisor Approved', $remarks, $userId);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Final approval by management/HR Director
     */
    public function approvePolicy($id, $userId, $remarks = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Check if supervisor already approved
            $policy = $this->getPolicy($id);
            if (!$policy || !in_array($policy['status'], ['Pending Approval', 'Supervisor Approved'])) {
                return ['success' => false, 'error' => 'Policy must be in Pending Approval or Supervisor Approved status'];
            }
            
            $stmt = $this->db->prepare("UPDATE policies SET 
                    status = 'Approved', approved_by = ?, approved_at = NOW(), updated_at = NOW() 
                    WHERE id = ?");
            $stmt->execute([$userId, $id]);
            
            // Log action
            $this->logAction($id, 'Approved', $remarks, $userId);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Reject policy (can be done at any approval stage)
     */
    public function rejectPolicy($id, $userId, $remarks)
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE policies SET status = 'Rejected', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);
            
            // Log action
            $this->logAction($id, 'Rejected', $remarks, $userId);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Publish policy (requires final approval)
     */
    public function publishPolicy($id, $userId, $effectiveDate = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Check if fully approved
            $policy = $this->getPolicy($id);
            if (!$policy || $policy['status'] !== 'Approved') {
                return ['success' => false, 'error' => 'Policy must be fully approved before publishing'];
            }
            
            $sql = "UPDATE policies SET 
                    status = 'Published', published_by = ?, published_at = NOW(), 
                    effective_date = ?, updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $effectiveDate, $id]);
            
            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Unpublish/Archive policy
     */
    public function unpublishPolicy($id)
    {
        $stmt = $this->db->prepare("UPDATE policies SET status = 'Approved', published_at = NULL, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== ACKNOWLEDGMENTS ====================
    
    /**
     * Acknowledge policy
     */
    public function acknowledgePolicy($policyId, $employeeId, $ipAddress = null, $browserInfo = null)
    {
        try {
            // Check if already acknowledged
            $check = $this->db->prepare("SELECT id FROM policy_acknowledgments WHERE policy_id = ? AND employee_id = ?");
            $check->execute([$policyId, $employeeId]);
            
            if ($check->fetch()) {
                return ['success' => false, 'error' => 'Already acknowledged'];
            }
            
            $stmt = $this->db->prepare("INSERT INTO policy_acknowledgments (employee_id, policy_id, ip_address, browser_info, acknowledged_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$employeeId, $policyId, $ipAddress, $browserInfo]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get employee's acknowledgment status
     */
    public function getEmployeeAcknowledgment($employeeId, $policyId)
    {
        $stmt = $this->db->prepare("SELECT * FROM policy_acknowledgments WHERE employee_id = ? AND policy_id = ?");
        $stmt->execute([$employeeId, $policyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all acknowledgments for a policy
     */
    public function getPolicyAcknowledgments($policyId)
    {
        $sql = "SELECT pa.*, e.first_name, e.last_name, e.department, e.position, e.email
                FROM policy_acknowledgments pa
                LEFT JOIN employees e ON pa.employee_id = e.id
                WHERE pa.policy_id = ?
                ORDER BY pa.acknowledged_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get employee's acknowledged policies
     */
    public function getEmployeeAcknowledgments($employeeId)
    {
        $sql = "SELECT p.*, pa.acknowledged_at, pa.ip_address
                FROM policy_acknowledgments pa
                JOIN policies p ON pa.policy_id = p.id
                WHERE pa.employee_id = ?
                ORDER BY pa.acknowledged_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ==================== COMPLIANCE ====================
    
    /**
     * Get compliance stats for a policy
     */
    public function getPolicyComplianceStats($policyId)
    {
        // Get total employees
        $totalStmt = $this->db->query("SELECT COUNT(*) as total FROM employees WHERE status = 'Active'");
        $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get acknowledged count
        $ackStmt = $this->db->prepare("SELECT COUNT(*) as acknowledged FROM policy_acknowledgments WHERE policy_id = ?");
        $ackStmt->execute([$policyId]);
        $acknowledged = $ackStmt->fetch(PDO::FETCH_ASSOC)['acknowledged'];
        
        $pending = $total - $acknowledged;
        $percentage = $total > 0 ? round(($acknowledged / $total) * 100, 2) : 0;
        
        return [
            'total' => $total,
            'acknowledged' => $acknowledged,
            'pending' => $pending,
            'percentage' => $percentage
        ];
    }
    
    /**
     * Get employees who haven't acknowledged
     */
    public function getPendingAcknowledgmentEmployees($policyId)
    {
        $sql = "SELECT e.id, e.employee_number, e.first_name, e.last_name, e.email, e.department, e.position
                FROM employees e
                WHERE e.status = 'Active'
                AND e.id NOT IN (
                    SELECT employee_id FROM policy_acknowledgments WHERE policy_id = ?
                )
                ORDER BY e.department, e.last_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all compliance stats
     */
    public function getAllComplianceStats()
    {
        $sql = "SELECT p.id, p.title, p.category, p.version, p.published_at, p.effective_date,
                       (SELECT COUNT(*) FROM policy_acknowledgments WHERE policy_id = p.id) as acknowledged_count
                FROM policies p
                WHERE p.status = 'Published'
                ORDER BY p.published_at DESC";
        
        $stmt = $this->db->query($sql);
        $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalStmt = $this->db->query("SELECT COUNT(*) as total FROM employees WHERE status = 'Active'");
        $totalEmployees = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $result = [];
        foreach ($policies as $policy) {
            $policy['percentage'] = $totalEmployees > 0 ? round(($policy['acknowledged_count'] / $totalEmployees) * 100, 2) : 0;
            $result[] = $policy;
        }
        
        return $result;
    }
    
    // ==================== HELPERS ====================
    
    /**
     * Create policy version
     */
    private function createVersion($policyId, $content, $summary, $userId)
    {
        $policy = $this->getPolicy($policyId);
        $version = $policy['version'] ?? '1.0';
        
        $stmt = $this->db->prepare("INSERT INTO policy_versions (policy_id, version, content, changes_summary, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$policyId, $version, $content, $summary, $userId]);
    }
    
    /**
     * Log policy action
     */
    private function logAction($policyId, $action, $remarks, $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO policy_approvals (policy_id, action, remarks, approved_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$policyId, $action, $remarks, $userId]);
    }
    
    /**
     * Get policy approval history
     */
    public function getApprovalHistory($policyId)
    {
        $stmt = $this->db->prepare("SELECT * FROM policy_approvals WHERE policy_id = ? ORDER BY created_at DESC");
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get policy versions
     */
    public function getPolicyVersions($policyId)
    {
        $stmt = $this->db->prepare("SELECT * FROM policy_versions WHERE policy_id = ? ORDER BY created_at DESC");
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get categories
     */
    public function getCategories()
    {
        $stmt = $this->db->query("SELECT * FROM policy_categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user is HR/Admin
     */
    public function isHRAdmin($userId)
    {
        try {
            $this->db->query("SELECT 1 FROM employees LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT department, position FROM employees WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return false;
        
        $dept = strtolower($user['department'] ?? '');
        $pos = strtolower($user['position'] ?? '');
        
        return strpos($dept, 'human resources') !== false || 
               strpos($dept, 'administration') !== false ||
               strpos($pos, 'admin') !== false ||
               strpos($pos, 'hr') !== false;
    }
    
    /**
     * Check if user can approve
     */
    public function canApprove($userId)
    {
        try {
            $this->db->query("SELECT 1 FROM employees LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT department, position FROM employees WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return false;
        
        $dept = strtolower($user['department'] ?? '');
        $pos = strtolower($user['position'] ?? '');
        
        // Legal, HR, Management can approve
        return strpos($dept, 'legal') !== false ||
               strpos($dept, 'human resources') !== false ||
               strpos($dept, 'administration') !== false ||
               strpos($pos, 'manager') !== false ||
               strpos($pos, 'director') !== false ||
               strpos($pos, 'head') !== false;
    }
    
    /**
     * Check if user is a supervisor/manager (first level approval)
     */
    public function isSupervisor($userId)
    {
        try {
            $this->db->query("SELECT 1 FROM employees LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT department, position FROM employees WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return false;
        
        $pos = strtolower($user['position'] ?? '');
        
        // Supervisors, managers, team leads can do first-level approval
        return strpos($pos, 'supervisor') !== false ||
               strpos($pos, 'manager') !== false ||
               strpos($pos, 'team lead') !== false ||
               strpos($pos, 'head') !== false;
    }
    
    /**
     * Check if user is management/HR Director (final approval)
     */
    public function isManagement($userId)
    {
        try {
            $this->db->query("SELECT 1 FROM employees LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT department, position FROM employees WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return false;
        
        $dept = strtolower($user['department'] ?? '');
        $pos = strtolower($user['position'] ?? '');
        
        // Directors, VP, HR Head, Legal Head can do final approval
        return strpos($pos, 'director') !== false ||
               strpos($pos, 'vp') !== false ||
               strpos($pos, 'vice president') !== false ||
               strpos($pos, 'head') !== false ||
               (strpos($dept, 'human resources') !== false && strpos($pos, 'manager') !== false) ||
               (strpos($dept, 'legal') !== false && strpos($pos, 'manager') !== false);
    }
    
    /**
     * Get published policies (for employees)
     */
    public function getPublishedPolicies($search = null)
    {
        $sql = "SELECT * FROM policies WHERE status = 'Published'";
        $params = [];
        
        if ($search) {
            $sql .= " AND (title LIKE ? OR category LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY category, title";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
