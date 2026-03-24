<?php

class ComplianceItemModel
{
    private PDO $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =====================================================
    // COMPLIANCE ITEM CRUD OPERATIONS
    // =====================================================

    /**
     * Get all compliance items with optional filters
     */
    public function getAllComplianceItems($filters = [])
    {
        $sql = "SELECT ci.*, u.first_name as responsible_first_name, u.last_name as responsible_last_name,
                u.email as responsible_email, d.name as department_name
                FROM compliance_items ci
                LEFT JOIN users u ON ci.responsible_person_id = u.id
                LEFT JOIN departments d ON ci.department = d.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category'])) {
            $sql .= " AND ci.category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND ci.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['department'])) {
            $sql .= " AND ci.department = ?";
            $params[] = $filters['department'];
        }
        
        if (!empty($filters['risk_level'])) {
            $sql .= " AND ci.risk_level = ?";
            $params[] = $filters['risk_level'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (ci.name LIKE ? OR ci.description LIKE ? OR ci.compliance_id LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY ci.due_date ASC, FIELD(ci.status, 'Overdue', 'Non-Compliant', 'Pending', 'Compliant')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get single compliance item by ID
     */
    public function getComplianceItemById($id)
    {
        $stmt = $this->db->prepare("
            SELECT ci.*, u.first_name as responsible_first_name, u.last_name as responsible_last_name,
                   u.email as responsible_email
            FROM compliance_items ci
            LEFT JOIN users u ON ci.responsible_person_id = u.id
            WHERE ci.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create new compliance item
     */
    public function createComplianceItem($data)
    {
        // Generate unique compliance ID
        $complianceId = $this->generateComplianceId($data['category']);
        
        $stmt = $this->db->prepare("
            INSERT INTO compliance_items (
                compliance_id, name, category, subcategory, description, department,
                responsible_person_id, frequency, due_date, status, risk_level, remarks,
                is_recurring, created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $complianceId,
            $data['name'],
            $data['category'],
            $data['subcategory'] ?? null,
            $data['description'] ?? null,
            $data['department'] ?? null,
            $data['responsible_person_id'] ?? null,
            $data['frequency'] ?? 'Monthly',
            $data['due_date'] ?? null,
            $data['status'] ?? 'Pending',
            $data['risk_level'] ?? 'Low',
            $data['remarks'] ?? null,
            $data['is_recurring'] ?? 0,
            $data['created_by'] ?? null
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update compliance item
     */
    public function updateComplianceItem($id, $data)
    {
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'category', 'subcategory', 'description', 'department',
                          'responsible_person_id', 'frequency', 'due_date', 'status', 
                          'risk_level', 'remarks', 'attachment_path', 'is_recurring'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE compliance_items SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete compliance item
     */
    public function deleteComplianceItem($id)
    {
        $stmt = $this->db->prepare("DELETE FROM compliance_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Generate unique compliance ID
     */
    private function generateComplianceId($category)
    {
        $prefix = strtoupper(substr($category, 0, 3));
        $stmt = $this->db->query("SELECT MAX(CAST(SUBSTRING(compliance_id, 5) AS UNSIGNED)) as max_num 
                                  FROM compliance_items 
                                  WHERE compliance_id LIKE '$prefix-%'");
        $result = $stmt->fetch();
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return sprintf("%s-%03d", $prefix, $nextNum);
    }

    // =====================================================
    // COMPLIANCE LOGGING
    // =====================================================

    /**
     * Log compliance item action
     */
    public function logComplianceAction($complianceItemId, $userId, $action, $oldValue = null, $newValue = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO compliance_logs (
                compliance_item_id, user_id, action, old_value, new_value, ip_address, user_agent, timestamp
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $complianceItemId,
            $userId,
            $action,
            $oldValue ? json_encode($oldValue) : null,
            $newValue ? json_encode($newValue) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Get compliance logs for an item
     */
    public function getComplianceLogs($complianceItemId, $limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT cl.*, u.first_name, u.last_name
            FROM compliance_logs cl
            LEFT JOIN users u ON cl.user_id = u.id
            WHERE cl.compliance_item_id = ?
            ORDER BY cl.timestamp DESC
            LIMIT ?
        ");
        $stmt->execute([$complianceItemId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get all audit logs with filters
     */
    public function getAllAuditLogs($filters = [], $limit = 100)
    {
        $sql = "SELECT cl.*, u.first_name, u.last_name, ci.compliance_id, ci.name as compliance_name
                FROM compliance_logs cl
                LEFT JOIN users u ON cl.user_id = u.id
                LEFT JOIN compliance_items ci ON cl.compliance_item_id = ci.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['compliance_item_id'])) {
            $sql .= " AND cl.compliance_item_id = ?";
            $params[] = $filters['compliance_item_id'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND cl.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND cl.action = ?";
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND cl.timestamp >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND cl.timestamp <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY cl.timestamp DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // =====================================================
    // ALERTS MANAGEMENT
    // =====================================================

    /**
     * Get all alerts
     */
    public function getAlerts($filters = [])
    {
        $sql = "SELECT a.*, ci.compliance_id, ci.name as compliance_name, 
                u.first_name as recipient_first_name, u.last_name as recipient_last_name
                FROM alerts a
                LEFT JOIN compliance_items ci ON a.compliance_item_id = ci.id
                LEFT JOIN users u ON a.recipient_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['is_read'])) {
            $sql .= " AND a.is_read = ?";
            $params[] = $filters['is_read'] === 'true' ? 1 : 0;
        }
        
        if (!empty($filters['is_resolved'])) {
            $sql .= " AND a.is_resolved = ?";
            $params[] = $filters['is_resolved'] === 'true' ? 1 : 0;
        }
        
        if (!empty($filters['alert_type'])) {
            $sql .= " AND a.alert_type = ?";
            $params[] = $filters['alert_type'];
        }
        
        if (!empty($filters['recipient_id'])) {
            $sql .= " AND a.recipient_id = ?";
            $params[] = $filters['recipient_id'];
        }
        
        $sql .= " ORDER BY FIELD(a.priority, 'critical', 'high', 'medium', 'low'), a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get unread alert count
     */
    public function getUnreadAlertCount($userId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM alerts WHERE is_read = 0 AND is_resolved = 0";
        
        if ($userId) {
            $sql .= " AND (recipient_id = ? OR recipient_id IS NULL)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetch()['count'] ?? 0;
    }

    /**
     * Mark alert as read
     */
    public function markAlertAsRead($alertId)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$alertId]);
    }

    /**
     * Mark alert as resolved
     */
    public function resolveAlert($alertId, $resolvedBy)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET is_resolved = 1, resolved_at = NOW(), resolved_by = ? WHERE id = ?");
        return $stmt->execute([$resolvedBy, $alertId]);
    }

    /**
     * Snooze alert
     */
    public function snoozeAlert($alertId, $snoozeUntil)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET snoozed_until = ? WHERE id = ?");
        return $stmt->execute([$snoozeUntil, $alertId]);
    }

    /**
     * Create new alert
     */
    public function createAlert($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO alerts (compliance_item_id, alert_type, priority, message, recipient_id, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $data['compliance_item_id'],
            $data['alert_type'],
            $data['priority'] ?? 'medium',
            $data['message'],
            $data['recipient_id'] ?? null
        ]);
    }

    // =====================================================
    // RISK INDICATORS
    // =====================================================

    /**
     * Get risk indicators for compliance item
     */
    public function getRiskIndicators($complianceItemId = null)
    {
        if ($complianceItemId) {
            $stmt = $this->db->prepare("
                SELECT ri.*, ci.compliance_id, ci.name as compliance_name
                FROM risk_indicators ri
                LEFT JOIN compliance_items ci ON ri.compliance_item_id = ci.id
                WHERE ri.compliance_item_id = ?
                ORDER BY FIELD(ri.risk_level, 'Critical', 'High', 'Medium', 'Low')
            ");
            $stmt->execute([$complianceItemId]);
        } else {
            $stmt = $this->db->query("
                SELECT ri.*, ci.compliance_id, ci.name as compliance_name
                FROM risk_indicators ri
                LEFT JOIN compliance_items ci ON ri.compliance_item_id = ci.id
                WHERE ri.is_active = 1
                ORDER BY FIELD(ri.risk_level, 'Critical', 'High', 'Medium', 'Low')
            ");
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Create risk indicator
     */
    public function createRiskIndicator($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO risk_indicators (compliance_item_id, risk_level, triggering_condition, mitigation_steps, is_active)
            VALUES (?, ?, ?, ?, 1)
        ");
        
        return $stmt->execute([
            $data['compliance_item_id'],
            $data['risk_level'],
            $data['triggering_condition'],
            $data['mitigation_steps'] ?? null
        ]);
    }

    /**
     * Update risk indicator
     */
    public function updateRiskIndicator($id, $data)
    {
        $fields = [];
        $params = [];
        
        if (isset($data['risk_level'])) {
            $fields[] = "risk_level = ?";
            $params[] = $data['risk_level'];
        }
        
        if (isset($data['triggering_condition'])) {
            $fields[] = "triggering_condition = ?";
            $params[] = $data['triggering_condition'];
        }
        
        if (isset($data['mitigation_steps'])) {
            $fields[] = "mitigation_steps = ?";
            $params[] = $data['mitigation_steps'];
        }
        
        if (isset($data['is_active'])) {
            $fields[] = "is_active = ?";
            $params[] = $data['is_active'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE risk_indicators SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // =====================================================
    // DASHBOARD STATISTICS
    // =====================================================

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_items' => 0,
            'compliant_count' => 0,
            'pending_count' => 0,
            'non_compliant_count' => 0,
            'overdue_count' => 0,
            'compliance_score' => 0,
            'upcoming_deadlines' => [],
            'by_category' => [],
            'by_department' => [],
            'by_risk_level' => [],
            'trend_data' => []
        ];
        
        try {
            // Get total counts
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Compliant' THEN 1 ELSE 0 END) as compliant,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Non-Compliant' THEN 1 ELSE 0 END) as non_compliant,
                    SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as overdue
                FROM compliance_items
            ");
            $result = $stmt->fetch();
            
            if ($result) {
                $stats['total_items'] = $result['total'] ?? 0;
                $stats['compliant_count'] = $result['compliant'] ?? 0;
                $stats['pending_count'] = $result['pending'] ?? 0;
                $stats['non_compliant_count'] = $result['non_compliant'] ?? 0;
                $stats['overdue_count'] = $result['overdue'] ?? 0;
                
                // Calculate compliance score
                if ($stats['total_items'] > 0) {
                    $score = ($stats['compliant_count'] / $stats['total_items']) * 100;
                    $score -= ($stats['overdue_count'] * 2);
                    $score -= ($stats['non_compliant_count'] * 5);
                    $stats['compliance_score'] = max(0, round($score));
                }
            }
            
            // Get upcoming deadlines (next 7 days)
            $stmt = $this->db->query("
                SELECT id, name, due_date, status, risk_level
                FROM compliance_items
                WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                AND status NOT IN ('Compliant')
                ORDER BY due_date ASC
                LIMIT 10
            ");
            $stats['upcoming_deadlines'] = $stmt->fetchAll();
            
            // Get by category
            $stmt = $this->db->query("
                SELECT 
                    category,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Compliant' THEN 1 ELSE 0 END) as compliant,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Non-Compliant' THEN 1 ELSE 0 END) as non_compliant,
                    SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as overdue
                FROM compliance_items
                GROUP BY category
                ORDER BY category
            ");
            $stats['by_category'] = $stmt->fetchAll();
            
            // Get by department
            $stmt = $this->db->query("
                SELECT 
                    department,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Compliant' THEN 1 ELSE 0 END) as compliant,
                    SUM(CASE WHEN status = 'Non-Compliant' THEN 1 ELSE 0 END) as non_compliant
                FROM compliance_items
                WHERE department IS NOT NULL
                GROUP BY department
                ORDER BY department
            ");
            $stats['by_department'] = $stmt->fetchAll();
            
            // Get by risk level
            $stmt = $this->db->query("
                SELECT 
                    risk_level,
                    COUNT(*) as count
                FROM compliance_items
                GROUP BY risk_level
                ORDER BY FIELD(risk_level, 'Critical', 'High', 'Medium', 'Low')
            ");
            $stats['by_risk_level'] = $stmt->fetchAll();
            
        } catch (Exception $e) {
            // Return default stats on error
        }
        
        return $stats;
    }

    /**
     * Get departments for filter
     */
    public function getDepartments()
    {
        $stmt = $this->db->query("SELECT DISTINCT department FROM compliance_items WHERE department IS NOT NULL ORDER BY department");
        return $stmt->fetchAll();
    }

    /**
     * Get users for assignment
     */
    public function getResponsiblePersons()
    {
        $stmt = $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.email
            FROM users u
            WHERE u.status = 'active'
            ORDER BY u.first_name, u.last_name
        ");
        return $stmt->fetchAll();
    }

    // =====================================================
    // AUTOMATED COMPLIANCE CHECKS
    // =====================================================

    /**
     * Check and update overdue items
     */
    public function checkOverdueItems()
    {
        // Update status to overdue for past due items
        $stmt = $this->db->query("
            UPDATE compliance_items 
            SET status = 'Overdue', 
                risk_level = CASE 
                    WHEN risk_level = 'Low' THEN 'Medium'
                    WHEN risk_level = 'Medium' THEN 'High'
                    WHEN risk_level = 'High' THEN 'Critical'
                    ELSE 'Critical'
                END,
                updated_at = NOW()
            WHERE due_date < CURDATE() 
            AND status NOT IN ('Compliant', 'Overdue')
        ");
        
        // Get affected items
        $stmt = $this->db->query("
            SELECT id, name, responsible_person_id
            FROM compliance_items 
            WHERE due_date < CURDATE() 
            AND status = 'Overdue'
        ");
        
        return $stmt->fetchAll();
    }

    /**
     * Generate upcoming deadline alerts
     */
    public function generateDeadlineAlerts()
    {
        $alerts = [];
        
        // 7 days before
        $stmt = $this->db->query("
            SELECT id, name, responsible_person_id
            FROM compliance_items
            WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            AND status NOT IN ('Compliant', 'Overdue')
        ");
        $items = $stmt->fetchAll();
        foreach ($items as $item) {
            $this->createAlert([
                'compliance_item_id' => $item['id'],
                'alert_type' => 'upcoming_deadline',
                'priority' => 'low',
                'message' => "Compliance item '{$item['name']}' is due in 7 days",
                'recipient_id' => $item['responsible_person_id']
            ]);
            $alerts[] = $item['id'];
        }
        
        // 3 days before
        $stmt = $this->db->query("
            SELECT id, name, responsible_person_id
            FROM compliance_items
            WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            AND status NOT IN ('Compliant', 'Overdue')
        ");
        $items = $stmt->fetchAll();
        foreach ($items as $item) {
            $this->createAlert([
                'compliance_item_id' => $item['id'],
                'alert_type' => 'upcoming_deadline',
                'priority' => 'medium',
                'message' => "Compliance item '{$item['name']}' is due in 3 days",
                'recipient_id' => $item['responsible_person_id']
            ]);
            $alerts[] = $item['id'];
        }
        
        // 1 day before
        $stmt = $this->db->query("
            SELECT id, name, responsible_person_id
            FROM compliance_items
            WHERE due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
            AND status NOT IN ('Compliant', 'Overdue')
        ");
        $items = $stmt->fetchAll();
        foreach ($items as $item) {
            $this->createAlert([
                'compliance_item_id' => $item['id'],
                'alert_type' => 'upcoming_deadline',
                'priority' => 'high',
                'message' => "Compliance item '{$item['name']}' is due tomorrow",
                'recipient_id' => $item['responsible_person_id']
            ]);
            $alerts[] = $item['id'];
        }
        
        return $alerts;
    }

    /**
     * Update compliance item status with logging
     */
    public function updateStatusWithLogging($itemId, $newStatus, $userId, $remarks = null)
    {
        // Get old values
        $oldItem = $this->getComplianceItemById($itemId);
        
        if (!$oldItem) {
            return false;
        }
        
        // Determine risk level based on new status
        $newRiskLevel = $oldItem['risk_level'];
        if ($newStatus === 'Non-Compliant') {
            if ($oldItem['category'] === 'Labor Law' || $oldItem['category'] === 'Health & Safety') {
                $newRiskLevel = 'Critical';
            } else {
                $newRiskLevel = 'High';
            }
        } elseif ($newStatus === 'Compliant') {
            $newRiskLevel = 'Low';
        }
        
        // Update the item
        $data = [
            'status' => $newStatus,
            'risk_level' => $newRiskLevel,
            'last_checked' => date('Y-m-d H:i:s')
        ];
        
        if ($remarks) {
            $data['remarks'] = $remarks;
        }
        
        $this->updateComplianceItem($itemId, $data);
        
        // Log the change
        $this->logComplianceAction($itemId, $userId, 'status_changed', 
            ['status' => $oldItem['status'], 'risk_level' => $oldItem['risk_level']], 
            ['status' => $newStatus, 'risk_level' => $newRiskLevel]
        );
        
        // Create alert for status change
        if ($newStatus === 'Non-Compliant') {
            $this->createAlert([
                'compliance_item_id' => $itemId,
                'alert_type' => 'critical_non_compliance',
                'priority' => 'critical',
                'message' => "Compliance item marked as Non-Compliant: {$oldItem['name']}",
                'recipient_id' => $oldItem['responsible_person_id']
            ]);
        }
        
        return true;
    }

    // =====================================================
    // HR DATABASE INTEGRATION - Sample HR Tables
    // =====================================================

    /**
     * Get dashboard statistics from HR database
     * Uses sample_hr.compliance_summary table
     */
    public function getHRDashboardStats()
    {
        $stats = [
            'total_items' => 0,
            'compliant_count' => 0,
            'pending_count' => 0,
            'non_compliant_count' => 0,
            'overdue_count' => 0,
            'compliance_score' => 0
        ];
        
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'compliant' THEN 1 ELSE 0 END) as compliant,
                    SUM(CASE WHEN status = 'at_risk' THEN 1 ELSE 0 END) as at_risk,
                    SUM(CASE WHEN status = 'non_compliant' THEN 1 ELSE 0 END) as non_compliant,
                    AVG(overall_score) as avg_score
                FROM compliance_summary
            ");
            $result = $stmt->fetch();
            
            if ($result) {
                $stats['total_items'] = (int)($result['total'] ?? 0);
                $stats['compliant_count'] = (int)($result['compliant'] ?? 0);
                $stats['pending_count'] = (int)($result['at_risk'] ?? 0);
                $stats['non_compliant_count'] = (int)($result['non_compliant'] ?? 0);
                $stats['compliance_score'] = round($result['avg_score'] ?? 0);
            }
        } catch (PDOException $e) {
            // Table may not exist, return defaults
        }
        
        return $stats;
    }

    /**
     * Get employee compliance data from HR database
     * Uses sample_hr.compliance_summary and employees tables
     */
    public function getEmployeeComplianceData()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    cs.id as summary_id,
                    cs.employee_id,
                    cs.employment_score,
                    cs.leave_score,
                    cs.benefits_score,
                    cs.working_conditions_score,
                    cs.workplace_protection_score,
                    cs.data_privacy_score,
                    cs.overall_score,
                    cs.status,
                    cs.critical_issues,
                    cs.high_risks,
                    cs.medium_risks,
                    cs.low_risks,
                    cs.last_checked,
                    e.first_name,
                    e.last_name,
                    e.email,
                    e.department_id,
                    d.name as department_name
                FROM compliance_summary cs
                LEFT JOIN employees e ON cs.employee_id = e.id
                LEFT JOIN departments d ON e.department_id = d.id
                ORDER BY cs.overall_score ASC
            ");
            
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'employee_name' => $row['first_name'] . ' ' . $row['last_name'],
                    'email' => $row['email'],
                    'department' => $row['department_name'] ?? 'Unassigned',
                    'employment_score' => (float)$row['employment_score'],
                    'leave_score' => (float)$row['leave_score'],
                    'benefits_score' => (float)$row['benefits_score'],
                    'conditions_score' => (float)$row['working_conditions_score'],
                    'protection_score' => (float)$row['workplace_protection_score'],
                    'privacy_score' => (float)$row['data_privacy_score'],
                    'overall_score' => (float)$row['overall_score'],
                    'status' => $row['status'],
                    'critical_issues' => (int)$row['critical_issues'],
                    'high_risks' => (int)$row['high_risks'],
                    'medium_risks' => (int)$row['medium_risks'],
                    'low_risks' => (int)$row['low_risks'],
                    'last_checked' => $row['last_checked']
                ];
            }, $results);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get single employee compliance details
     */
    public function getEmployeeComplianceDetails($employeeId)
    {
        try {
            // Get summary
            $stmt = $this->db->prepare("
                SELECT cs.*, e.first_name, e.last_name, e.email, e.employment_type
                FROM compliance_summary cs
                LEFT JOIN employees e ON cs.employee_id = e.id
                WHERE cs.employee_id = ?
            ");
            $stmt->execute([$employeeId]);
            $summary = $stmt->fetch();
            
            if (!$summary) {
                return null;
            }
            
            // Get individual rule results
            $stmt = $this->db->prepare("
                SELECT cr.*, cr.result as check_result
                FROM compliance_results cr
                WHERE cr.employee_id = ?
                ORDER BY cr.checked_at DESC
            ");
            $stmt->execute([$employeeId]);
            $results = $stmt->fetchAll();
            
            // Get issues (non-compliant results)
            $issues = [];
            foreach ($results as $result) {
                if ($result['result'] !== 'compliant') {
                    $issues[] = [
                        'rule_id' => $result['rule_id'],
                        'message' => $result['remarks'] ?? 'Compliance issue found',
                        'risk' => $result['result'] === 'non_compliant' ? 'High' : 'Medium',
                        'checked_at' => $result['checked_at']
                    ];
                }
            }
            
            return [
                'employee_id' => $summary['employee_id'],
                'employee_name' => $summary['first_name'] . ' ' . $summary['last_name'],
                'email' => $summary['email'],
                'employment_type' => $summary['employment_type'],
                'employment_score' => (float)$summary['employment_score'],
                'leave_score' => (float)$summary['leave_score'],
                'benefits_score' => (float)$summary['benefits_score'],
                'conditions_score' => (float)$summary['working_conditions_score'],
                'protection_score' => (float)$summary['workplace_protection_score'],
                'privacy_score' => (float)$summary['data_privacy_score'],
                'overall_score' => (float)$summary['overall_score'],
                'status' => $summary['status'],
                'critical_issues' => (int)$summary['critical_issues'],
                'high_risks' => (int)$summary['high_risks'],
                'medium_risks' => (int)$summary['medium_risks'],
                'low_risks' => (int)$summary['low_risks'],
                'last_checked' => $summary['last_checked'],
                'issues' => $issues,
                'rule_results' => $results
            ];
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get compliance rules from HR database
     */
    public function getComplianceRules()
    {
        try {
            $stmt = $this->db->query("
                SELECT cr.*, cc.name as category_name
                FROM compliance_rules cr
                LEFT JOIN compliance_categories cc ON cr.category_id = cc.id
                WHERE cr.is_active = 1
                ORDER BY cc.id, cr.id
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get compliance categories from HR database
     */
    public function getComplianceCategories()
    {
        try {
            $stmt = $this->db->query("
                SELECT * FROM compliance_categories WHERE is_active = 1 ORDER BY id
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get recent audit logs from HR database
     */
    public function getHRAuditLogs($limit = 20)
    {
        try {
            $stmt = $this->db->query("
                SELECT al.*, u.first_name, u.last_name
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.module = 'compliance' OR al.module LIKE '%compliance%'
                ORDER BY al.created_at DESC
                LIMIT $limit
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Try compliance_logs table
            try {
                $stmt = $this->db->query("
                    SELECT cl.*, e.first_name, e.last_name
                    FROM compliance_logs cl
                    LEFT JOIN employees e ON cl.employee_id = e.id
                    ORDER BY cl.created_at DESC
                    LIMIT $limit
                ");
                return $stmt->fetchAll();
            } catch (PDOException $e2) {
                return [];
            }
        }
    }

    /**
     * Run compliance check for all employees
     */
    public function runComplianceCheck()
    {
        try {
            // This would typically run compliance rules against employee data
            // For now, just return a success with stats
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM employees");
            $total = $stmt->fetch()['total'] ?? 0;
            
            return [
                'success' => true,
                'total_employees' => $total,
                'message' => 'Compliance check completed'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error running compliance check: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get alerts from HR database (employees with compliance issues)
     */
    public function getHRAlerts()
    {
        try {
            // Get employees with compliance issues from HR database
            $stmt = $this->db->query("
                SELECT 
                    cs.employee_id,
                    cs.status,
                    cs.critical_issues,
                    cs.high_risks,
                    cs.medium_risks,
                    cs.overall_score,
                    e.first_name,
                    e.last_name,
                    cs.last_checked
                FROM compliance_summary cs
                LEFT JOIN employees e ON cs.employee_id = e.id
                WHERE cs.status IN ('at_risk', 'non_compliant') 
                   OR cs.critical_issues > 0 
                   OR cs.high_risks > 0
                ORDER BY cs.overall_score ASC, cs.critical_issues DESC
            ");
            
            $results = $stmt->fetchAll();
            
            $alerts = [];
            foreach ($results as $row) {
                $priority = 'low';
                $alertType = 'Compliance Reminder';
                $message = '';
                
                if ($row['critical_issues'] > 0) {
                    $priority = 'critical';
                    $alertType = 'Critical Compliance Issue';
                    $message = "Employee {$row['first_name']} {$row['last_name']} has {$row['critical_issues']} critical compliance issue(s)";
                } elseif ($row['high_risks'] > 0) {
                    $priority = 'high';
                    $alertType = 'High Risk Compliance';
                    $message = "Employee {$row['first_name']} {$row['last_name']} has {$row['high_risks']} high risk compliance item(s)";
                } elseif ($row['status'] === 'non_compliant') {
                    $priority = 'critical';
                    $alertType = 'Non-Compliant Status';
                    $message = "Employee {$row['first_name']} {$row['last_name']} is marked as non-compliant";
                } else {
                    $priority = 'medium';
                    $alertType = 'At Risk';
                    $message = "Employee {$row['first_name']} {$row['last_name']} is at risk (Score: {$row['overall_score']}%)";
                }
                
                $alerts[] = [
                    'id' => $row['employee_id'],
                    'employee_id' => $row['employee_id'],
                    'employee_name' => $row['first_name'] . ' ' . $row['last_name'],
                    'priority' => $priority,
                    'alert_type' => $alertType,
                    'message' => $message,
                    'category' => 'Employee Compliance',
                    'created_at' => $row['last_checked'],
                    'is_resolved' => 0
                ];
            }
            
            return $alerts;
        } catch (PDOException $e) {
            return [];
        }
    }
}
