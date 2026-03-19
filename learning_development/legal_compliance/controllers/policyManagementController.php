<?php
/**
 * Policy Management Controller
 * Handles complete policy lifecycle: creation, approval, publication, acknowledgment
 */

require_once "../../auth/database.php";

class PolicyManagementController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all policies with optional filtering
     */
    public function getAllPolicies($status = null, $includeUnpublished = true)
    {
        $sql = "SELECT * FROM policies";
        $conditions = [];
        $params = [];
        
        if (!$includeUnpublished) {
            $conditions[] = "is_published = 1";
        }
        
        if ($status) {
            $conditions[] = "status = ?";
            $params[] = $status;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY status ASC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get only published and active policies (for end users)
     */
    public function getPublishedPolicies()
    {
        $sql = "SELECT * FROM policies 
                WHERE status = 'published' AND is_published = 1 AND is_active = 1
                ORDER BY category, title";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get policies pending approval
     */
    public function getPendingApprovalPolicies()
    {
        $sql = "SELECT * FROM policies 
                WHERE status = 'pending_approval' 
                ORDER BY created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get single policy by ID
     */
    public function getPolicyById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM policies WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new policy (draft status)
     */
    public function createPolicy($data, $userId)
    {
        $sql = "INSERT INTO policies (title, content, category, version, status, created_by, created_at) 
                VALUES (?, ?, ?, ?, 'draft', ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['category'],
            $data['version'] ?? '1.0',
            $userId
        ]);
    }
    
    /**
     * Update policy
     */
    public function updatePolicy($id, $data)
    {
        $sql = "UPDATE policies SET 
                title = ?, content = ?, category = ?, version = ?, 
                updated_at = NOW() 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['category'],
            $data['version'],
            $id
        ]);
    }
    
    /**
     * Submit policy for approval
     */
    public function submitForApproval($id)
    {
        $sql = "UPDATE policies SET status = 'pending_approval', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Approve policy (management approval)
     */
    public function approvePolicy($id, $approverId, $approverName, $approverLevel, $comments = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Add approval record
            $sql = "INSERT INTO policy_approvals (policy_id, approver_id, approver_name, approver_level, approval_status, comments, approved_at) 
                    VALUES (?, ?, ?, ?, 'approved', ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $approverId, $approverName, $approverLevel, $comments]);
            
            // Update policy status
            $sql = "UPDATE policies SET status = 'approved', approved_by = ?, approved_at = NOW(), updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$approverId, $id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Reject policy
     */
    public function rejectPolicy($id, $approverId, $approverName, $approverLevel, $comments)
    {
        try {
            $this->db->beginTransaction();
            
            // Add rejection record
            $sql = "INSERT INTO policy_approvals (policy_id, approver_id, approver_name, approver_level, approval_status, comments, approved_at) 
                    VALUES (?, ?, ?, ?, 'rejected', ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $approverId, $approverName, $approverLevel, $comments]);
            
            // Update policy status back to draft
            $sql = "UPDATE policies SET status = 'draft', updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Publish approved policy
     */
    public function publishPolicy($id, $userId)
    {
        $sql = "UPDATE policies SET 
                status = 'published', 
                is_published = 1, 
                published_by = ?, 
                published_at = NOW(), 
                updated_at = NOW() 
                WHERE id = ? AND status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $id]);
    }
    
    /**
     * Archive policy
     */
    public function archivePolicy($id)
    {
        $sql = "UPDATE policies SET status = 'archived', is_published = 0, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get policy acknowledgments
     */
    public function getAcknowledgments($policyId)
    {
        $sql = "SELECT pa.*, e.first_name, e.last_name, e.department, e.position
                FROM policy_acknowledgments pa
                LEFT JOIN employees e ON pa.user_id = e.id
                WHERE pa.policy_id = ?
                ORDER BY pa.acknowledged_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get user acknowledgment for a policy
     */
    public function getUserAcknowledgment($policyId, $userId)
    {
        $sql = "SELECT * FROM policy_acknowledgments WHERE policy_id = ? AND user_id = ? AND acknowledged = 'yes'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all acknowledgments for a user
     */
    public function getUserAcknowledgments($userId)
    {
        $sql = "SELECT pa.*, p.title, p.category, p.version
                FROM policy_acknowledgments pa
                JOIN policies p ON pa.policy_id = p.id
                WHERE pa.user_id = ? AND pa.acknowledged = 'yes'
                ORDER BY pa.acknowledged_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Acknowledge policy
     */
    public function acknowledgePolicy($policyId, $userId, $ipAddress = null)
    {
        // Check if already acknowledged
        $existing = $this->getUserAcknowledgment($policyId, $userId);
        if ($existing) {
            return ['success' => false, 'message' => 'Already acknowledged'];
        }
        
        $sql = "INSERT INTO policy_acknowledgments (policy_id, user_id, acknowledged, acknowledged_at, ip_address) 
                VALUES (?, ?, 'yes', NOW(), ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$policyId, $userId, $ipAddress]);
        
        return ['success' => $result, 'message' => $result ? 'Acknowledged successfully' : 'Error'];
    }
    
    /**
     * Get policy approval history
     */
    public function getApprovalHistory($policyId)
    {
        $sql = "SELECT * FROM policy_approvals WHERE policy_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get acknowledgment statistics for a policy
     */
    public function getAcknowledgmentStats($policyId)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN acknowledged = 'yes' THEN 1 ELSE 0 END) as acknowledged,
                    SUM(CASE WHEN acknowledged = 'no' THEN 1 ELSE 0 END) as pending
                FROM policy_acknowledgments WHERE policy_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$policyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user is management (can approve policies)
     */
    public function isManagement($userId)
    {
        // Check user's department/role
        $sql = "SELECT department, position FROM employees WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return false;
        
        $dept = strtolower($user['department'] ?? '');
        $pos = strtolower($user['position'] ?? '');
        
        // HR, Finance, Legal, Administration are management
        $mgmtDepts = ['human resources', 'administration', 'finance', 'legal', 'executive'];
        
        foreach ($mgmtDepts as $d) {
            if (strpos($dept, $d) !== false) {
                return true;
            }
        }
        
        // Also check for management positions
        $mgmtTitles = ['manager', 'director', 'vp', 'president', 'head', 'chief'];
        foreach ($mgmtTitles as $t) {
            if (strpos($pos, $t) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get user's approval level
     */
    public function getApprovalLevel($userId)
    {
        $sql = "SELECT position FROM employees WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return 'employee';
        
        $pos = strtolower($user['position'] ?? '');
        
        if (strpos($pos, 'president') !== false || strpos($pos, 'ceo') !== false) {
            return 'president';
        }
        if (strpos($pos, 'vice president') !== false || strpos($pos, 'vp') !== false) {
            return 'vp';
        }
        if (strpos($pos, 'director') !== false) {
            return 'director';
        }
        if (strpos($pos, 'manager') !== false || strpos($pos, 'head') !== false) {
            return 'manager';
        }
        
        return 'employee';
    }
}
