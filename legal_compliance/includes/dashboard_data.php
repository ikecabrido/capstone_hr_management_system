<?php
/**
 * Legal & Compliance Dashboard - Data Controller
 * HR Legal & Compliance System - Bestlink College of the Philippines
 * Server-side logic for fetching and processing dashboard data
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$userName = $user['name'] ?? 'HR Admin';
$userRole = $user['role'] ?? 'hr_admin';

// Determine base paths
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';

// Database connection
require_once $basePath . "auth/database.php";
$db = Database::getInstance()->getConnection();

// Error tracking
$dbErrors = [];
$tablesExist = [];

// Check which tables exist
function tableExists($db, $tableName) {
    try {
        $stmt = $db->query("SHOW TABLES LIKE '$tableName'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Initialize variables with defaults
$totalEmployees = 0;
$employeeCategories = [];
$totalIncidents = 0;
$openIncidents = 0;
$resolvedIncidents = 0;
$incidentByStatus = [
    'submitted' => 0,
    'under_review' => 0,
    'investigation' => 0,
    'escalated' => 0,
    'resolved' => 0,
    'closed' => 0
];
$totalRisks = 0;
$risksByLevel = ['low' => 0, 'medium' => 0, 'high' => 0];
$complianceRate = 0;
$complianceItems = [];
$recentIncidents = [];
$recentActivities = [];
$upcomingDeadlines = [];
$overdueRequirements = [];
$highRisks = [];
$contributionStats = ['completed' => 0, 'pending' => 0];

try {
    // Check which tables exist to avoid errors
    $tablesExist['employees'] = tableExists($db, 'employees');
    $tablesExist['lc_incidents'] = tableExists($db, 'lc_incidents');
    $tablesExist['lc_risks'] = tableExists($db, 'lc_employee_risk_assessments');
    $tablesExist['lc_compliance_items'] = tableExists($db, 'lc_compliance_items');
    $tablesExist['lc_alerts'] = tableExists($db, 'lc_alerts');
    $tablesExist['lc_activity_log'] = tableExists($db, 'lc_activity_log');
    $tablesExist['employees_categories'] = tableExists($db, 'lc_employee_categories');
    $tablesExist['employees_contributions'] = tableExists($db, 'employees_contributions');
    
    // Total Employees - always query FROM employees table
    if ($tablesExist['employees']) {
        $stmt = $db->query("SELECT COUNT(DISTINCT full_name, employee_no, department, position) as total FROM employees WHERE employment_status = 'active'");
        $totalEmployees = $stmt->fetch()['total'] ?? 0;
    }

    // Employee Contributions Stats
    if ($tablesExist['employees'] && $tablesExist['employees_contributions']) {
        // Find employees who have all 3 types (sss, pagibig, philhealth) as 'submitted'
        $sql = "SELECT COUNT(*) as completed_count FROM (
                    SELECT employee_id 
                    FROM employees_contributions 
                    WHERE status = 'submitted' 
                    GROUP BY employee_id 
                    HAVING COUNT(DISTINCT contribution_type) = 3
                ) as sub";
        $stmt = $db->query($sql);
        $completed = $stmt->fetch()['completed_count'] ?? 0;
        
        $contributionStats['completed'] = $completed;
        $contributionStats['pending'] = max(0, $totalEmployees - $completed);
    }
    
    // Employee breakdown by category (department in the new employees table)
    if ($tablesExist['employees']) {
        $stmt = $db->query("SELECT department as category, COUNT(DISTINCT full_name, employee_no, position) as count FROM employees WHERE employment_status = 'active' GROUP BY department");
        $employeeCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Incidents stats
    if ($tablesExist['lc_incidents']) {
        $stmt = $db->query("SELECT status, COUNT(*) as count FROM lc_incidents GROUP BY status");
        $incidentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($incidentStats as $stat) {
            $totalIncidents += $stat['count'];
            $status = strtolower($stat['status']);
            
            // Map common status names to chart keys
            $mappedStatus = $status;
            if ($status === 'open') $mappedStatus = 'submitted';
            if ($status === 'in_progress') $mappedStatus = 'investigation';
            if ($status === 'closed_no_violation') $mappedStatus = 'closed';
            
            if (in_array($status, ['submitted', 'under_review', 'investigation', 'escalated', 'open', 'in_progress'])) {
                $openIncidents += $stat['count'];
            }
            if (in_array($status, ['resolved', 'closed', 'closed_no_violation'])) {
                $resolvedIncidents += $stat['count'];
            }
            
            if (isset($incidentByStatus[$mappedStatus])) {
                $incidentByStatus[$mappedStatus] += $stat['count'];
            }
        }
    }
    
    // Risk stats
    if ($tablesExist['lc_risks']) {
        $stmt = $db->query("SELECT risk_level as severity, COUNT(*) as count FROM lc_employee_risk_assessments GROUP BY risk_level");
        $riskStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($riskStats as $stat) {
            $totalRisks += $stat['count'];
            $severity = strtolower($stat['severity']);
            if (isset($risksByLevel[$severity])) {
                $risksByLevel[$severity] = $stat['count'];
            }
        }
    }
    
    // Compliance overview
    if ($tablesExist['lc_compliance_items']) {
        $stmt = $db->query("SELECT category, status, COUNT(*) as count FROM lc_compliance_items GROUP BY category, status");
        $complianceItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalCompliance = array_sum(array_column($complianceItems, 'count'));
        
        $compliantCount = 0;
        foreach ($complianceItems as $item) {
            if ($item['status'] === 'Compliant') {
                $compliantCount += $item['count'];
            }
        }
        $complianceRate = $totalCompliance > 0 ? round(($compliantCount / $totalCompliance) * 100) : 0;
    }
    
    // Alerts
    if ($tablesExist['lc_alerts']) {
        $stmt = $db->query("SELECT * FROM lc_alerts WHERE is_resolved = 0 ORDER BY created_at DESC LIMIT 10");
        $upcomingDeadlines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Overdue Compliance Requirements
    if ($tablesExist['lc_compliance_items']) {
        $stmt = $db->query("SELECT * FROM lc_compliance_items WHERE status IN ('Overdue', 'Non-Compliant', 'Non-compliant') ORDER BY due_date ASC LIMIT 10");
        $overdueRequirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // High risks
    if ($tablesExist['lc_risks']) {
        $stmt = $db->query("SELECT * FROM lc_employee_risk_assessments WHERE risk_level IN ('high', 'critical') AND status != 'closed' ORDER BY created_at DESC LIMIT 5");
        $highRisks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recent incidents
    if ($tablesExist['lc_incidents']) {
        $stmt = $db->query("
            SELECT i.*, 
                   COALESCE(e.full_name, i.reporter_name) as first_name, '' as last_name, 
                   COALESCE(e.employee_no, i.reporter_employee_id) as employee_no, 
                   e.employment_status as employee_status,
                   COALESCE(e.department, i.reporter_department) as employee_category,
                   COALESCE(e.position, i.reporter_position) as position_title,
                   'N/A' as employment_type
            FROM lc_incidents i 
            LEFT JOIN employees e ON i.reporter_id = e.employee_id 
            ORDER BY i.created_at DESC 
            LIMIT 5
        ");
        $recentIncidents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Activity log
    if ($tablesExist['lc_activity_log']) {
        $stmt = $db->query("
            SELECT al.*, 
                   COALESCE(u.full_name, e.full_name, 'System') as user_name
            FROM lc_activity_log al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN employees e ON al.user_id = e.employee_id
            ORDER BY al.created_at DESC 
            LIMIT 10
        ");
        $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $dbErrors[] = "Database error: " . $e->getMessage();
}

// Store errors for debugging
$debugMode = true; // Set to false in production

// Map status for display
function getIncidentStatusLabel($status) {
    $labels = [
        'open' => 'Pending',
        'under_review' => 'Under Review',
        'in_progress' => 'Ongoing',
        'pending_approval' => 'Pending Approval',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
        'escalated' => 'Escalated',
        'closed_no_violation' => 'Closed',
        'compliant' => 'Compliant',
        'non_compliant' => 'Non-Compliant',
        'pending' => 'Pending'
    ];
    return $labels[$status] ?? $status;
}

function getIncidentStatusClass($status) {
    $classes = [
        'open' => 'badge-warning',
        'under_review' => 'badge-info',
        'in_progress' => 'badge-primary',
        'pending_approval' => 'badge-warning',
        'resolved' => 'badge-success',
        'rejected' => 'badge-danger',
        'escalated' => 'badge-danger',
        'closed_no_violation' => 'badge-secondary',
        'compliant' => 'badge-success',
        'non_compliant' => 'badge-danger',
        'pending' => 'badge-warning'
    ];
    return $classes[$status] ?? 'badge-secondary';
}

function getIncidentStatusIcon($status) {
    $icons = [
        'open' => 'clock',
        'under_review' => 'search',
        'in_progress' => 'spinner',
        'pending_approval' => 'clock',
        'resolved' => 'check',
        'rejected' => 'times',
        'escalated' => 'exclamation-triangle',
        'closed_no_violation' => 'check',
        'compliant' => 'check',
        'non_compliant' => 'exclamation-triangle',
        'pending' => 'clock'
    ];
    return $icons[$status] ?? 'circle';
}