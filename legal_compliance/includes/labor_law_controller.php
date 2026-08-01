<?php
/**
 * Labor Law Compliance Controller
 * Handles all PHP logic, database queries, and data processing
 * for the Labor Law Compliance Module
 */

// Determine base paths
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';

// Database connection
require_once __DIR__ . "/../../auth/database.php";
require_once __DIR__ . "/../../includes/FormProtection.php";
$db = Database::getInstance()->getConnection();

// Process form submissions
$message = '';
$messageType = '';

// Form protection - handle redirect messages FROM lc_PRG pattern
$prgMessage = FormProtection::consumePRGMessage('form_message');
if ($prgMessage) {
    $message = $prgMessage;
    $messageType = $_GET['message_type'] ?? 'success';
}

// Process form submissions with double-submission protection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Get the action type
    $action = $_POST['action'];
    
    // Handle 'add' action with form protection
    if ($action === 'add') {
        $submittedToken = $_POST['form_protection_token'] ?? '';
        $formId = 'labor_law_compliance_' . $action;
        
        // Check if token is valid (consume it to prevent duplicate submissions)
        if (!FormProtection::validateToken($submittedToken, $formId)) {
            // Token is invalid or expired
            if (!empty($submittedToken) || FormProtection::isFormSubmitted($formId)) {
                // This is likely a duplicate submission - redirect with message
                $currentUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET);
                FormProtection::redirectAfterSubmit($currentUrl, [], 'form_message', 'Your session has expired or the form was already submitted. Please try again.');
            } else {
                $message = 'Invalid form submission. Please refresh the page and try again.';
                $messageType = 'danger';
            }
        } else {
            // Token is valid - process the action
            // Track submission to prevent duplicates
            FormProtection::trackSubmission($formId, $_POST);
            $stmt = $db->prepare("INSERT INTO lc_labor_law_compliance 
                (compliance_id, requirement_name, category, subcategory, description, legal_basis, status, due_date, frequency, assigned_to, remarks, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $result = $stmt->execute([
                $_POST['compliance_id'] ?? null,
                $_POST['requirement_name'] ?? null,
                $_POST['category'] ?? 'Government Contributions',
                $_POST['subcategory'] ?? null,
                $_POST['description'] ?? null,
                $_POST['legal_basis'] ?? null,
                $_POST['status'] ?? 'Pending',
                $_POST['due_date'] ?? null,
                $_POST['frequency'] ?? 'Monthly',
                $_POST['assigned_to'] ?? null,
                $_POST['remarks'] ?? null,
                $_SESSION['user']['id'] ?? 1
            ]);
            
            if ($result) {
                $message = 'Compliance item added successfully!';
                $messageType = 'success';
                // Use PRG pattern - redirect after successful submission
                $redirectUrl = $_SERVER['PHP_SELF'] . '?view=checklist';
                FormProtection::redirectAfterSubmit($redirectUrl, [], 'form_message', 'Compliance item added successfully!');
            }
        }
    }
    
    // Update compliance status
    if ($action === 'update_status') {
        $id = $_POST['id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        
        if (empty($id) || empty($newStatus)) {
            $message = 'Error: Missing required fields (ID or Status)';
            $messageType = 'danger';
            error_log("Status Update Error: Missing ID or Status - ID: $id, Status: $newStatus");
        } else {
            try {
                $stmt = $db->prepare("UPDATE lc_labor_law_compliance SET status = ?, last_checked = NOW(), updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$newStatus, $id]);
                
                if ($result && $stmt->rowCount() > 0) {
                    $message = 'Status updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error: Record not found or no changes made';
                    $messageType = 'danger';
                }
            } catch (PDOException $e) {
                $message = 'Error updating status: ' . $e->getMessage();
                $messageType = 'danger';
                error_log("Status Update Error: " . $e->getMessage());
            }
        }
    }
    
    // Delete compliance item
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        
        if (empty($id)) {
            $message = 'Error: Missing required field (ID)';
            $messageType = 'danger';
            error_log("Delete Error: Missing ID");
        } else {
            try {
                $stmt = $db->prepare("UPDATE lc_labor_law_compliance SET is_active = 0 WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result && $stmt->rowCount() > 0) {
                    $message = 'Compliance item deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error: Record not found';
                    $messageType = 'danger';
                }
            } catch (PDOException $e) {
                $message = 'Error deleting record: ' . $e->getMessage();
                $messageType = 'danger';
                error_log("Delete Error: " . $e->getMessage());
            }
        }
    }
    
    // Upload attachment
    if ($action === 'upload_attachment') {
        $complianceId = $_POST['compliance_id'];
        
        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = 'uploads/compliance_documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['attachment']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $stmt = $db->prepare("INSERT INTO lc_labor_law_compliance_attachments (compliance_id, file_name, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $complianceId,
                    basename($_FILES['attachment']['name']),
                    $targetPath,
                    $_FILES['attachment']['type'],
                    $_FILES['attachment']['size'],
                    $_SESSION['user']['id'] ?? 1
                ]);
                
                $message = 'Document uploaded successfully!';
                $messageType = 'success';
            }
        }
    }
}

/**
 * Get compliance statistics
 */
function getComplianceStats($db) {
    $statsQuery = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Compliant' THEN 1 ELSE 0 END) as compliant,
            SUM(CASE WHEN status = 'Pending' AND due_date >= CURDATE() THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'Overdue' OR (status = 'Pending' AND due_date < CURDATE()) THEN 1 ELSE 0 END) as overdue,
            SUM(CASE WHEN status = 'Not Applicable' THEN 1 ELSE 0 END) as not_applicable
        FROM lc_labor_law_compliance 
        WHERE is_active = 1
    ");
    return $statsQuery->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get compliance rate percentage
 */
function getComplianceRate($stats) {
    return $stats['total'] > 0 ? round(($stats['compliant'] / $stats['total']) * 100) : 0;
}

/**
 * Get compliance by category
 */
function getComplianceByCategory($db) {
    $categoryQuery = $db->query("
        SELECT category,
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Compliant' THEN 1 ELSE 0 END) as compliant
        FROM lc_labor_law_compliance 
        WHERE is_active = 1
        GROUP BY category
    ");
    return $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get upcoming deadlines (next 30 days)
 */
function getUpcomingDeadlines($db, $limit = 10) {
    $deadlineQuery = $db->prepare("
        SELECT * FROM lc_labor_law_compliance 
        WHERE is_active = 1 
        AND due_date >= CURDATE() AND due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND status NOT IN ('Compliant', 'Overdue')
        ORDER BY due_date ASC
        LIMIT ?
    ");
    $deadlineQuery->bindValue(1, $limit, PDO::PARAM_INT);
    $deadlineQuery->execute();
    return $deadlineQuery->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get overdue items
 */
function getOverdueItems($db) {
    $overdueQuery = $db->query("
        SELECT * FROM lc_labor_law_compliance 
        WHERE is_active = 1 
        AND (status = 'Overdue' OR (status = 'Pending' AND due_date < CURDATE()))
        ORDER BY due_date ASC
    ");
    return $overdueQuery->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get filtered compliance items
 */
function getComplianceItems($db, $filters = []) {
    $whereClause = "WHERE is_active = 1";
    $params = [];
    
    if (!empty($filters['status'])) {
        $whereClause .= " AND status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['category'])) {
        $whereClause .= " AND category = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['search'])) {
        $whereClause .= " AND (requirement_name LIKE ? OR description LIKE ? OR compliance_id LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql = "SELECT * FROM lc_labor_law_compliance $whereClause ORDER BY due_date ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get employees for dropdown
 */
function getEmployees($db) {
    // Correct status column is employment_status
    $employeesQuery = $db->query("SELECT MIN(employee_id) as id, employee_no, full_name as first_name, '' as last_name FROM employees WHERE employment_status = 'active' GROUP BY full_name, employee_no, department, position ORDER BY full_name");
    return $employeesQuery->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get attachments for a compliance item
 */
function getComplianceAttachments($db, $complianceId) {
    $attachmentStmt = $db->prepare("SELECT * FROM lc_labor_law_compliance_attachments WHERE compliance_id = ? ORDER BY uploaded_at DESC");
    $attachmentStmt->execute([$complianceId]);
    return $attachmentStmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get category icon class
 */
function getCategoryIcon($category) {
    $icons = [
        'Government Contributions' => 'fa-university',
        'Employee Benefits' => 'fa-gift',
        'Legal Documentation' => 'fa-file-contract',
        'Mandatory Reports' => 'fa-file-alt',
        'Workplace Safety' => 'fa-hard-hat',
        'Tax Compliance' => 'fa-calculator'
    ];
    return $icons[$category] ?? 'fa-folder';
}

/**
 * Get category CSS class
 */
function getCategoryClass($category) {
    return str_replace(' ', '-', strtolower($category));
}

/**
 * Get status CSS class
 */
function getStatusClass($status) {
    return strtolower(str_replace(' ', '-', $status));
}

/**
 * Calculate days until due date
 */
function getDaysUntilDue($dueDate, $status) {
    if (empty($dueDate) || $status === 'Compliant') {
        return null;
    }
    
    $due = new DateTime($dueDate);
    $today = new DateTime();
    return $today->diff($due)->days;
}

/**
 * Check if due date is overdue
 */
function isOverdue($dueDate, $status) {
    if (empty($dueDate) || $status === 'Compliant') {
        return false;
    }
    $due = new DateTime($dueDate);
    return $due < new DateTime();
}

/**
 * Check if due date is urgent (within 7 days)
 */
function isUrgent($dueDate, $status) {
    if (empty($dueDate) || $status === 'Compliant') {
        return false;
    }
    $days = getDaysUntilDue($dueDate, $status);
    return $days !== null && $days <= 7 && !isOverdue($dueDate, $status);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    return number_format($bytes / 1024, 1) . ' KB';
}
