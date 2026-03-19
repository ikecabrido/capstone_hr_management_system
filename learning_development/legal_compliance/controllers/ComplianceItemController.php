<?php

require_once "../models/ComplianceItemModel.php";

class ComplianceItemController
{
    private $model;
    
    public function __construct($db)
    {
        $this->model = new ComplianceItemModel($db);
    }
    
    // Get model for direct access
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * Get HR Dashboard Stats from database
     */
    public function getHRDashboardStats()
    {
        return $this->model->getHRDashboardStats();
    }
    
    /**
     * Get Employee Compliance Data from database
     */
    public function getEmployeeComplianceData()
    {
        return $this->model->getEmployeeComplianceData();
    }
    
    /**
     * Get Employee Compliance Details
     */
    public function getEmployeeComplianceDetails($employeeId)
    {
        return $this->model->getEmployeeComplianceDetails($employeeId);
    }
    
    /**
     * Get Compliance Rules from database
     */
    public function getComplianceRules()
    {
        return $this->model->getComplianceRules();
    }
    
    /**
     * Get Compliance Categories from database
     */
    public function getComplianceCategories()
    {
        return $this->model->getComplianceCategories();
    }
    
    /**
     * Get HR Audit Logs from database
     */
    public function getHRAuditLogs($limit = 20)
    {
        return $this->model->getHRAuditLogs($limit);
    }

    /**
     * Get HR Alerts
     */
    public function getHRAlerts()
    {
        return $this->model->getHRAlerts();
    }

    /**
     * Run compliance check
     */
    public function runComplianceCheck()
    {
        return $this->model->runComplianceCheck();
    }
    
    // =====================================================
    // DASHBOARD
    // =====================================================
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        return $this->model->getDashboardStats();
    }
    
    // =====================================================
    // COMPLIANCE ITEMS
    // =====================================================
    
    /**
     * Get all compliance items
     */
    public function getComplianceItems($filters = [])
    {
        return $this->model->getAllComplianceItems($filters);
    }
    
    /**
     * Get single compliance item
     */
    public function getComplianceItem($id)
    {
        return $this->model->getComplianceItemById($id);
    }
    
    /**
     * Create new compliance item
     */
    public function createComplianceItem($data, $userId)
    {
        // Validate required fields
        if (empty($data['name']) || empty($data['category'])) {
            return ['success' => false, 'message' => 'Name and category are required'];
        }
        
        $data['created_by'] = $userId;
        
        $id = $this->model->createComplianceItem($data);
        
        if ($id) {
            // Log the creation
            $this->model->logComplianceAction($id, $userId, 'created', null, $data);
            
            return ['success' => true, 'id' => $id, 'message' => 'Compliance item created successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to create compliance item'];
    }
    
    /**
     * Update compliance item
     */
    public function updateComplianceItem($id, $data, $userId)
    {
        $oldItem = $this->model->getComplianceItemById($id);
        
        if (!$oldItem) {
            return ['success' => false, 'message' => 'Compliance item not found'];
        }
        
        $result = $this->model->updateComplianceItem($id, $data);
        
        if ($result) {
            // Log the update
            $this->model->logComplianceAction($id, $userId, 'updated', $oldItem, $data);
            
            return ['success' => true, 'message' => 'Compliance item updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update compliance item'];
    }
    
    /**
     * Update compliance item status with logging
     */
    public function updateStatus($itemId, $newStatus, $userId, $remarks = null)
    {
        $result = $this->model->updateStatusWithLogging($itemId, $newStatus, $userId, $remarks);
        
        if ($result) {
            return ['success' => true, 'message' => 'Status updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update status'];
    }
    
    /**
     * Delete compliance item
     */
    public function deleteComplianceItem($id, $userId)
    {
        $item = $this->model->getComplianceItemById($id);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Compliance item not found'];
        }
        
        $result = $this->model->deleteComplianceItem($id);
        
        if ($result) {
            // Log the deletion
            $this->model->logComplianceAction($id, $userId, 'deleted', $item, null);
            
            return ['success' => true, 'message' => 'Compliance item deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete compliance item'];
    }
    
    // =====================================================
    // AUDIT LOGS
    // =====================================================
    
    /**
     * Get audit logs
     */
    public function getAuditLogs($filters = [])
    {
        return $this->model->getAllAuditLogs($filters);
    }
    
    /**
     * Get compliance item logs
     */
    public function getComplianceItemLogs($itemId)
    {
        return $this->model->getComplianceLogs($itemId);
    }
    
    // =====================================================
    // ALERTS
    // =====================================================
    
    /**
     * Get alerts
     */
    public function getAlerts($filters = [])
    {
        return $this->model->getAlerts($filters);
    }
    
    /**
     * Get unread alert count
     */
    public function getUnreadAlertCount($userId = null)
    {
        return $this->model->getUnreadAlertCount($userId);
    }
    
    /**
     * Mark alert as read
     */
    public function markAlertAsRead($alertId)
    {
        $result = $this->model->markAlertAsRead($alertId);
        
        return ['success' => $result];
    }
    
    /**
     * Mark alert as resolved
     */
    public function resolveAlert($alertId, $userId)
    {
        $result = $this->model->resolveAlert($alertId, $userId);
        
        return ['success' => $result];
    }
    
    /**
     * Snooze alert
     */
    public function snoozeAlert($alertId, $snoozeUntil)
    {
        $result = $this->model->snoozeAlert($alertId, $snoozeUntil);
        
        return ['success' => $result];
    }
    
    /**
     * Bulk acknowledge alerts
     */
    public function bulkAcknowledgeAlerts($alertIds, $userId)
    {
        foreach ($alertIds as $alertId) {
            $this->model->markAlertAsRead($alertId);
        }
        
        return ['success' => true, 'message' => 'Alerts acknowledged'];
    }
    
    // =====================================================
    // RISK INDICATORS
    // =====================================================
    
    /**
     * Get risk indicators
     */
    public function getRiskIndicators($complianceItemId = null)
    {
        return $this->model->getRiskIndicators($complianceItemId);
    }
    
    /**
     * Create risk indicator
     */
    public function createRiskIndicator($data)
    {
        if (empty($data['compliance_item_id']) || empty($data['risk_level']) || empty($data['triggering_condition'])) {
            return ['success' => false, 'message' => 'Required fields missing'];
        }
        
        $id = $this->model->createRiskIndicator($data);
        
        if ($id) {
            return ['success' => true, 'id' => $id];
        }
        
        return ['success' => false, 'message' => 'Failed to create risk indicator'];
    }
    
    // =====================================================
    // UTILITIES
    // =====================================================
    
    /**
     * Get departments for filter
     */
    public function getDepartments()
    {
        return $this->model->getDepartments();
    }
    
    /**
     * Get responsible persons
     */
    public function getResponsiblePersons()
    {
        return $this->model->getResponsiblePersons();
    }
    
    /**
     * Run automated checks
     */
    public function runAutomatedChecks()
    {
        // Check overdue items
        $overdueItems = $this->model->checkOverdueItems();
        
        // Generate deadline alerts
        $alerts = $this->model->generateDeadlineAlerts();
        
        return [
            'success' => true,
            'overdue_items_updated' => count($overdueItems),
            'alerts_generated' => count($alerts)
        ];
    }
    
    // =====================================================
    // API HANDLERS
    // =====================================================
    
    /**
     * Handle API requests
     */
    public function handleApiRequest($action, $data = [])
    {
        $userId = $_SESSION['user_id'] ?? 1;
        
        switch ($action) {
            case 'get_items':
                return $this->getComplianceItems($data);
                
            case 'get_item':
                return $this->getComplianceItem($data['id'] ?? 0);
                
            case 'create_item':
                return $this->createComplianceItem($data, $userId);
                
            case 'update_item':
                return $this->updateComplianceItem($data['id'], $data['data'], $userId);
                
            case 'update_status':
                return $this->updateStatus($data['id'], $data['status'], $userId, $data['remarks'] ?? null);
                
            case 'delete_item':
                return $this->deleteComplianceItem($data['id'], $userId);
                
            case 'get_dashboard_stats':
                return $this->getDashboardStats();
                
            case 'get_alerts':
                return $this->getAlerts($data);
                
            case 'mark_alert_read':
                return $this->markAlertAsRead($data['alert_id']);
                
            case 'resolve_alert':
                return $this->resolveAlert($data['alert_id'], $userId);
                
            case 'snooze_alert':
                return $this->snoozeAlert($data['alert_id'], $data['snooze_until']);
                
            case 'get_audit_logs':
                return $this->getAuditLogs($data);
                
            case 'get_item_logs':
                return $this->getComplianceItemLogs($data['item_id']);
                
            case 'get_risk_indicators':
                return $this->getRiskIndicators($data['item_id'] ?? null);
                
            case 'get_departments':
                return $this->getDepartments();
                
            case 'get_responsible_persons':
                return $this->getResponsiblePersons();
                
            case 'run_automated_checks':
                return $this->runAutomatedChecks();

            // HR Database Integration
            case 'run_compliance_check':
                return $this->model->runComplianceCheck();

            case 'get_hr_dashboard_stats':
                return $this->model->getHRDashboardStats();

            case 'get_employee_compliance':
                return $this->model->getEmployeeComplianceDetails($data['id'] ?? 0);

            case 'get_compliance_rules':
                return $this->model->getComplianceRules();

            case 'get_compliance_categories':
                return $this->model->getComplianceCategories();

            case 'get_hr_audit_logs':
                return $this->model->getHRAuditLogs($data['limit'] ?? 20);

            case 'get_hr_alerts':
                return $this->model->getHRAlerts();

            default:
                return ['success' => false, 'message' => 'Unknown action'];
        }
    }
}
