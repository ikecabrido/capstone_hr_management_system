<?php
/**
 * Policy Documentation Controller
 * Handles all PHP logic, database queries, and data processing
 * for the Policy Documentation Module
 */

// Determine base paths
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';

// Database connection
require_once __DIR__ . "/../../auth/database.php";
$db = Database::getInstance()->getConnection();

// Process form submissions
$message = '';
$messageType = '';

// Debug: Log incoming requests
if (isset($_POST['action'])) {
    error_log("Policy Documentation - Action: " . $_POST['action'] . " - ID: " . ($_POST['id'] ?? 'N/A'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Add new policy
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO lc_policy_documents 
            (title, description, category, department_owner, file_path, file_name, file_type, requires_acknowledgment, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $filePath = null;
        $fileName = null;
        $fileType = null;
        
        // Handle file upload
        if (!empty($_FILES['policy_file']['name'])) {
            $uploadDir = 'uploads/policies/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = basename($_FILES['policy_file']['name']);
            $targetPath = $uploadDir . time() . '_' . $fileName;
            
            if (move_uploaded_file($_FILES['policy_file']['tmp_name'], $targetPath)) {
                $filePath = $targetPath;
                $fileType = $_FILES['policy_file']['type'];
            }
        }
        
        $result = $stmt->execute([
            $_POST['title'] ?? null,
            $_POST['description'] ?? null,
            $_POST['category'] ?? 'HR Policies',
            $_POST['department_owner'] ?? null,
            $filePath,
            $fileName,
            $fileType,
            isset($_POST['requires_acknowledgment']) ? 1 : 0,
            $_SESSION['user']['id'] ?? 1
        ]);
        
        if ($result) {
            $policyId = $db->lastInsertId();
            
            // Create initial version
            $versionStmt = $db->prepare("INSERT INTO lc_policy_versions 
                (policy_id, version_number, file_path, file_name, change_notes, is_current, created_by) 
                VALUES (?, 1, ?, ?, 'Initial version', 1, ?)");
            $versionStmt->execute([$policyId, $filePath, $fileName, $_SESSION['user']['id'] ?? 1]);
            
            // Create audit log
            logPolicyAction($db, $policyId, 'created', 'Policy created', null, json_encode($_POST), $_SESSION['user']['id'] ?? 1);
            
            $message = 'Policy created successfully!';
            $messageType = 'success';
        }
    }
    
    // Update policy
    if ($action === 'update') {
        $id = $_POST['id'] ?? null;
        
        if (!empty($id)) {
            // Get old values for audit
            $oldStmt = $db->prepare("SELECT * FROM lc_policy_documents WHERE id = ?");
            $oldStmt->execute([$id]);
            $oldPolicy = $oldStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$oldPolicy) {
                $message = 'Error: Policy not found.';
                $messageType = 'danger';
            } else {
                $filePath = $oldPolicy['file_path'];
                $fileName = $oldPolicy['file_name'];
                $fileType = $oldPolicy['file_type'];
                $newVersion = ($oldPolicy['version_number'] ?? 0) + 1;
                
                // Handle new file upload
                if (!empty($_FILES['policy_file']['name'])) {
                    $uploadDir = 'uploads/policies/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $origFileName = basename($_FILES['policy_file']['name']);
                    $targetPath = $uploadDir . time() . '_' . $origFileName;
                    
                    if (move_uploaded_file($_FILES['policy_file']['tmp_name'], $targetPath)) {
                        $filePath = $targetPath;
                        $fileName = $origFileName;
                        $fileType = $_FILES['policy_file']['type'];
                    }
                }
                
                try {
                    // Update policy
                    $stmt = $db->prepare("UPDATE lc_policy_documents 
                        SET title = ?, description = ?, category = ?, department_owner = ?, 
                            file_path = ?, file_name = ?, file_type = ?, version_number = ?,
                            requires_acknowledgment = ?, updated_by = ?, updated_at = NOW() 
                        WHERE id = ?");
                    
                    $result = $stmt->execute([
                        $_POST['title'] ?? $oldPolicy['title'],
                        $_POST['description'] ?? $oldPolicy['description'],
                        $_POST['category'] ?? $oldPolicy['category'],
                        $_POST['department_owner'] ?? $oldPolicy['department_owner'],
                        $filePath,
                        $fileName,
                        $fileType,
                        $newVersion,
                        isset($_POST['requires_acknowledgment']) ? 1 : 0,
                        $_SESSION['user']['id'] ?? 1,
                        $id
                    ]);
                    
                    if ($result) {
                        // Mark old version as not current
                        $db->prepare("UPDATE lc_policy_versions SET is_current = 0 WHERE policy_id = ?")->execute([$id]);
                        
                        // Create new version
                        $versionStmt = $db->prepare("INSERT INTO lc_policy_versions 
                            (policy_id, version_number, file_path, file_name, change_notes, is_current, created_by) 
                            VALUES (?, ?, ?, ?, ?, 1, ?)");
                        $versionStmt->execute([
                            $id, 
                            $newVersion, 
                            $filePath, 
                            $fileName, 
                            $_POST['change_notes'] ?? 'Policy updated',
                            $_SESSION['user']['id'] ?? 1
                        ]);
                        
                        // Create audit log
                        logPolicyAction($db, $id, 'updated', 'Policy updated', json_encode($oldPolicy), json_encode($_POST), $_SESSION['user']['id'] ?? 1);
                        
                        $message = 'Policy updated successfully! New version ' . $newVersion . ' created.';
                        $messageType = 'success';
                    }
                } catch (PDOException $e) {
                    $message = 'Error updating policy: ' . $e->getMessage();
                    $messageType = 'danger';
                    error_log("Policy Update Error: " . $e->getMessage());
                }
            }
        }
    }
    
    // Delete policy (soft delete)
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        
        if (!empty($id)) {
            try {
                $stmt = $db->prepare("UPDATE lc_policy_documents SET is_active = 0 WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    logPolicyAction($db, $id, 'deleted', 'Policy deleted', null, '{"is_active":0}', $_SESSION['user']['id'] ?? 1);
                    $message = 'Policy deleted successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Error deleting policy: ' . $e->getMessage();
                $messageType = 'danger';
                error_log("Policy Delete Error: " . $e->getMessage());
            }
        }
    }
    
    // Send reminder for policy acknowledgment
    if ($action === 'send_reminder') {
        $ackId = $_POST['ack_id'] ?? null;
        $message = $_POST['message'] ?? '';
        
        if (!empty($ackId) && !empty($message)) {
            try {
                // Get acknowledgment details
                $stmt = $db->prepare("
                    SELECT pa.*, p.title as policy_title, 
                           e.full_name as first_name, '' as last_name, e.email
                    FROM lc_policy_acknowledgments pa
                    JOIN lc_policy_documents p ON pa.policy_id = p.id
                    JOIN employees e ON pa.employee_id = e.employee_id
                    WHERE pa.id = ?
                ");
                $stmt->execute([$ackId]);
                $ack = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($ack) {
                    // Create reminder record
                    $reminderStmt = $db->prepare("
                        INSERT INTO lc_policy_reminders 
                        (acknowledgment_id, sent_by, message, sent_at) 
                        VALUES (?, ?, ?, NOW())
                    ");
                    $reminderStmt->execute([
                        $ackId,
                        $_SESSION['user']['id'] ?? 1,
                        $message
                    ]);
                    
                    // Log the action
                    logPolicyAction($db, $ack['policy_id'], 'reminder_sent', 
                        'Reminder sent to ' . $ack['first_name'] . ' ' . $ack['last_name'],
                        null, json_encode(['message' => $message]), 
                        $_SESSION['user']['id'] ?? 1);
                    
                    $reminderMessage = 'Reminder sent successfully to ' . $ack['first_name'] . ' ' . $ack['last_name'];
                    $messageType = 'success';
                } else {
                    $reminderMessage = 'Acknowledgment record not found';
                    $messageType = 'danger';
                }
            } catch (PDOException $e) {
                $reminderMessage = 'Error sending reminder: ' . $e->getMessage();
                $messageType = 'danger';
                error_log('Reminder Error: ' . $e->getMessage());
            }
        } else {
            $reminderMessage = 'Invalid reminder data';
            $messageType = 'danger';
        }
    }
    
    // Acknowledge policy
    if ($action === 'acknowledge') {
        $policyId = $_POST['policy_id'] ?? null;
        $employeeId = $_POST['employee_id'] ?? ($_SESSION['user']['id'] ?? 1);
        
        if (!empty($policyId)) {
            try {
                // Check if acknowledgment record exists
                $checkStmt = $db->prepare("SELECT * FROM lc_policy_acknowledgments WHERE policy_id = ? AND employee_id = ?");
                $checkStmt->execute([$policyId, $employeeId]);
                $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    // Update existing
                    $stmt = $db->prepare("UPDATE lc_policy_acknowledgments 
                        SET status = 'acknowledged', acknowledged_at = NOW(), ip_address = ? 
                        WHERE policy_id = ? AND employee_id = ?");
                    $stmt->execute([$_SERVER['REMOTE_ADDR'] ?? null, $policyId, $employeeId]);
                } else {
                    // Create new
                    $stmt = $db->prepare("INSERT INTO lc_policy_acknowledgments 
                        (policy_id, employee_id, status, acknowledged_at, ip_address) 
                        VALUES (?, ?, 'acknowledged', NOW(), ?)");
                    $stmt->execute([$policyId, $employeeId, $_SERVER['REMOTE_ADDR'] ?? null]);
                }
                
                logPolicyAction($db, $policyId, 'acknowledged', 'Policy acknowledged by employee', null, json_encode(['employee_id' => $employeeId]), $employeeId);
                
                $message = 'Policy acknowledged successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message = 'Error acknowledging policy: ' . $e->getMessage();
                $messageType = 'danger';
                error_log("Policy Acknowledge Error: " . $e->getMessage());
            }
        }
    }
}

// ============================================
// DATA FETCHING FUNCTIONS
// ============================================

/**
 * Get policy statistics for dashboard
 */
function getPolicyStats($db) {
    $stats = [
        'total' => 0,
        'active' => 0,
        'pending_ack' => 0,
        'recent_updates' => 0
    ];
    
    // Total policies
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM lc_policy_documents WHERE is_active = 1");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
    
    // Active policies
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM lc_policy_documents WHERE is_active = 1 AND is_active = 1");
    $stats['active'] = $stats['total'];
    
    // Pending acknowledgments (unread)
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM lc_policy_acknowledgments WHERE status = 'unread'");
    $stats['pending_ack'] = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
    
    // Recently updated (last 7 days)
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM lc_policy_documents WHERE is_active = 1 AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats['recent_updates'] = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
    
    return $stats;
}

/**
 * Get all policies with filters
 */
function getPolicies($db, $filters = []) {
    $sql = "SELECT p.*, 
            (SELECT COUNT(*) FROM lc_policy_acknowledgments pa 
             WHERE pa.policy_id = p.id AND pa.status = 'unread') as unread_count,
            (SELECT COUNT(*) FROM lc_policy_acknowledgments pa 
             WHERE pa.policy_id = p.id AND pa.status = 'acknowledged') as acknowledged_count
            FROM lc_policy_documents p 
            WHERE p.is_active = 1";
    
    $params = [];
    
    if (!empty($filters['category'])) {
        $sql .= " AND p.category = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['department'])) {
        $sql .= " AND p.department_owner = ?";
        $params[] = $filters['department'];
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY p.updated_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Apply status filter in PHP since it uses subquery aliases
    if (!empty($filters['status'])) {
        $results = array_filter($results, function($policy) use ($filters) {
            if ($filters['status'] === 'unread') {
                return $policy['unread_count'] > 0;
            } elseif ($filters['status'] === 'acknowledged') {
                return $policy['acknowledged_count'] > 0;
            }
            return true;
        });
    }
    
    return $results;
}

/**
 * Get policy by ID
 */
function getPolicyById($db, $id) {
    $stmt = $db->prepare("SELECT * FROM lc_policy_documents WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get policy versions
 */
function getPolicyVersions($db, $policyId) {
    $stmt = $db->prepare("SELECT pv.*, 
            e.full_name as updated_by_name
            FROM lc_policy_versions pv
            LEFT JOIN employees e ON pv.created_by = e.employee_id
            WHERE pv.policy_id = ?
            ORDER BY pv.version_number DESC");
    $stmt->execute([$policyId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get policy acknowledgments
 */
function getPolicyAcknowledgments($db, $policyId) {
    $stmt = $db->prepare("SELECT pa.*, 
            e.full_name as first_name, '' as last_name, e.employee_no
            FROM lc_policy_acknowledgments pa
            JOIN employees e ON pa.employee_id = e.employee_id
            WHERE pa.policy_id = ?
            ORDER BY pa.acknowledged_at DESC");
    $stmt->execute([$policyId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get categories
 */
function getPolicyCategories($db) {
    $stmt = $db->query("SELECT DISTINCT category FROM lc_policy_documents WHERE is_active = 1 ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get departments
 */
function getPolicyDepartments($db) {
    $stmt = $db->query("SELECT DISTINCT department_owner FROM lc_policy_documents WHERE is_active = 1 AND department_owner IS NOT NULL ORDER BY department_owner");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get recent policy updates
 */
function getRecentPolicyUpdates($db) {
    $stmt = $db->query("SELECT p.*, 
            (SELECT pv.change_notes FROM lc_policy_versions pv WHERE pv.policy_id = p.id AND pv.is_current = 1) as change_notes
            FROM lc_policy_documents p 
            WHERE p.is_active = 1 
            ORDER BY p.updated_at DESC 
            LIMIT 5");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get pending acknowledgments
 */
function getPendingAcknowledgments($db) {
    $stmt = $db->query("SELECT pa.*, p.title as policy_title, 
            e.full_name as first_name, '' as last_name, e.employee_no
            FROM lc_policy_acknowledgments pa
            JOIN lc_policy_documents p ON pa.policy_id = p.id
            JOIN employees e ON pa.employee_id = e.employee_id
            WHERE pa.status = 'unread' AND p.is_active = 1
            ORDER BY pa.created_at DESC
            LIMIT 10");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get employees
 */
function getEmployees($db) {
    $stmt = $db->query("SELECT MIN(employee_id) as id, employee_no, full_name as first_name, '' as last_name, employment_status as status FROM employees WHERE employment_status = 'active' GROUP BY full_name, employee_no, department, position ORDER BY full_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Log policy action
 */
function logPolicyAction($db, $policyId, $action, $description, $oldValues, $newValues, $userId) {
    $stmt = $db->prepare("INSERT INTO lc_policy_audit_log 
        (policy_id, action, description, old_values, new_values, user_id, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $policyId, 
        $action, 
        $description, 
        $oldValues, 
        $newValues, 
        $userId, 
        $_SERVER['REMOTE_ADDR'] ?? null
    ]);
}

/**
 * Get policy audit log
 */
function getPolicyAuditLog($db, $policyId) {
    $stmt = $db->prepare("SELECT pal.*, 
            e.full_name as first_name, '' as last_name
            FROM lc_policy_audit_log pal
            LEFT JOIN employees e ON pal.user_id = e.employee_id
            WHERE pal.policy_id = ?
            ORDER BY pal.created_at DESC");
    $stmt->execute([$policyId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
