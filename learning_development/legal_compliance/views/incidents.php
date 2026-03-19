<?php
session_start();
require_once "../../auth/database.php";
require_once "../../auth/auth_check.php";

// Page configuration
$pageTitle = 'Incident Cases';
$currentPage = 'Incidents';

$db = Database::getInstance()->getConnection();

// For demo/testing: use default user if not logged in
$userId = $_SESSION['user_id'] ?? 2;

// Get user info
$stmt = $db->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Determine user role
$userPosition = $_SESSION['position'] ?? $user['position'] ?? 'Employee';
$userPosLower = strtolower($userPosition);

// Check for HR/Manager roles
$isHR = false;
$isManager = false;

if (strpos($userPosLower, 'hr') !== false || 
    strpos($userPosLower, 'human resources') !== false || 
    strpos($userPosLower, 'admin') !== false ||
    strpos($userPosLower, 'head') !== false) {
    $isHR = true;
}

if (strpos($userPosLower, 'manager') !== false ||
    strpos($userPosLower, 'supervisor') !== false ||
    strpos($userPosLower, 'head') !== false ||
    strpos($userPosLower, 'director') !== false) {
    $isManager = true;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // Search employees
    if ($_GET['action'] === 'search_employees' && isset($_GET['q'])) {
        $search = '%' . $_GET['q'] . '%';
        $stmt = $db->prepare("SELECT id, first_name, last_name, department, position FROM employees WHERE first_name LIKE ? OR last_name LIKE ? OR employee_id LIKE ? LIMIT 10");
        $stmt->execute([$search, $search, $search]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
    // Search HR officers for assignment
    if ($_GET['action'] === 'search_hr_officers' && isset($_GET['q'])) {
        $search = '%' . $_GET['q'] . '%';
        $stmt = $db->prepare("SELECT id, first_name, last_name, department, position FROM employees WHERE position LIKE '%HR%' OR position LIKE '%Human Resources%' OR position LIKE '%Admin%' AND (first_name LIKE ? OR last_name LIKE ?) LIMIT 10");
        $stmt->execute([$search, $search]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
    // Get incident with full workflow details
    if ($_GET['action'] === 'get_incident' && isset($_GET['id'])) {
        $stmt = $db->prepare("
            SELECT i.*, 
                   r.first_name as reporter_first_name,
                   r.last_name as reporter_last_name,
                   r.department as reporter_department,
                   a.first_name as assignee_first_name,
                   a.last_name as assignee_last_name,
                   hr.first_name as hr_first_name,
                   hr.last_name as hr_last_name,
                   ap.first_name as approver_first_name,
                   ap.last_name as approver_last_name
            FROM incidents i
            LEFT JOIN employees r ON i.reporter_id = r.id
            LEFT JOIN employees a ON i.assigned_to = a.id
            LEFT JOIN employees hr ON i.assigned_hr_id = hr.id
            LEFT JOIN employees ap ON i.approved_by = ap.id
            WHERE i.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $incident = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($incident) {
            // Get attachments
            $attStmt = $db->prepare("SELECT * FROM incident_evidence WHERE incident_id = ?");
            $attStmt->execute([$_GET['id']]);
            $incident['attachments'] = $attStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get notes
            $noteStmt = $db->prepare("SELECT n.*, e.first_name, e.last_name FROM incident_notes n LEFT JOIN employees e ON n.created_by = e.id WHERE n.incident_id = ? ORDER BY n.created_at DESC");
            $noteStmt->execute([$_GET['id']]);
            $incident['notes'] = $noteStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get audit log
            $auditStmt = $db->prepare("SELECT a.*, e.first_name, e.last_name FROM incident_audit_log a LEFT JOIN employees e ON a.performed_by = e.id WHERE a.incident_id = ? ORDER BY a.created_at DESC");
            $auditStmt->execute([$_GET['id']]);
            $incident['audit_log'] = $auditStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'incident' => $incident]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incident not found']);
        }
        exit;
    }
    
    // HR Review: Accept incident
    if ($_GET['action'] === 'hr_accept' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        $stmt = $db->prepare("UPDATE incidents SET status = 'under_review', previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['previous_status'] ?? 'open', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'HR Accept', ?, 'under_review', ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'open', $userId, $_POST['notes'] ?? 'Incident accepted by HR']);
        
        // Create notification for reporter
        $notifStmt = $db->prepare("INSERT INTO incident_notifications (incident_id, user_id, title, message, created_at) VALUES (?, (SELECT reporter_id FROM incidents WHERE id = ?), 'Incident Accepted', 'Your incident report has been accepted and is now under review.', NOW())");
        $notifStmt->execute([$_POST['incident_id'], $_POST['incident_id']]);
        
        echo json_encode(['success' => true, 'message' => 'Incident accepted and moved to Under Review']);
        exit;
    }
    
    // HR Review: Reject incident
    if ($_GET['action'] === 'hr_reject' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        $stmt = $db->prepare("UPDATE incidents SET status = 'rejected', rejection_reason = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['rejection_reason'] ?? '', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'HR Reject', ?, 'rejected', ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'open', $userId, $_POST['rejection_reason'] ?? 'Incident rejected by HR']);
        
        echo json_encode(['success' => true, 'message' => 'Incident rejected']);
        exit;
    }
    
    // HR Review: Request more information
    if ($_GET['action'] === 'hr_request_info' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        $stmt = $db->prepare("UPDATE incidents SET request_info_notes = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['request_info_notes'] ?? '', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Request Info', ?, 'under_review', ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'open', $userId, $_POST['request_info_notes'] ?? 'Additional information requested']);
        
        // Create notification
        $notifStmt = $db->prepare("INSERT INTO incident_notifications (incident_id, user_id, title, message, created_at) VALUES (?, (SELECT reporter_id FROM incidents WHERE id = ?), 'Additional Info Required', ?, NOW())");
        $notifStmt->execute([$_POST['incident_id'], $_POST['incident_id'], $_POST['request_info_notes'] ?? 'Please provide additional information for your incident report.']);
        
        echo json_encode(['success' => true, 'message' => 'Information request sent to reporter']);
        exit;
    }
    
    // Assign investigator/HR officer
    if ($_GET['action'] === 'assign_investigator' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        // Calculate SLA deadline (5 days from now)
        $slaDeadline = date('Y-m-d H:i:s', strtotime('+5 days'));
        
        // Check for repeat offender
        $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM incidents WHERE respondent_name = (SELECT respondent_name FROM incidents WHERE id = ?) AND status = 'resolved'");
        $checkStmt->execute([$_POST['incident_id']]);
        $repeatResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $isRepeatOffender = $repeatResult['count'] >= 3;
        
        $stmt = $db->prepare("UPDATE incidents SET assigned_hr_id = ?, assigned_to = ?, status = 'in_progress', sla_deadline = ?, repeat_offender = ?, previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['officer_id'], $_POST['officer_id'], $slaDeadline, $isRepeatOffender ? 1 : 0, $_POST['previous_status'] ?? 'under_review', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Assign Investigator', ?, 'in_progress', ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'under_review', $userId, 'Investigator assigned - Case escalated to Investigation Phase']);
        
        echo json_encode(['success' => true, 'message' => 'Investigator assigned, case moved to In Progress', 'sla_deadline' => $slaDeadline, 'repeat_offender' => $isRepeatOffender]);
        exit;
    }
    
    // Submit decision/action
    if ($_GET['action'] === 'submit_decision' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        $decision = $_POST['decision'] ?? '';
        $newStatus = in_array($decision, ['verbal_warning', 'written_warning', 'suspension', 'termination']) ? 'pending_approval' : 'resolved';
        
        $stmt = $db->prepare("UPDATE incidents SET decision = ?, status = ?, resolution_notes = ?, previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$decision, $newStatus, $_POST['resolution_notes'] ?? '', $_POST['previous_status'] ?? 'in_progress', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Submit Decision', ?, ?, ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'in_progress', $newStatus, $userId, 'Decision submitted: ' . $decision]);
        
        echo json_encode(['success' => true, 'message' => 'Decision submitted', 'new_status' => $newStatus]);
        exit;
    }
    
    // Approve decision (Manager)
    if ($_GET['action'] === 'approve_decision' && isset($_POST['incident_id']) && $isManager) {
        $stmt = $db->prepare("UPDATE incidents SET status = 'resolved', approved_by = ?, approved_at = NOW(), previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$userId, $_POST['previous_status'] ?? 'pending_approval', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Approve Decision', ?, 'resolved', ?, 'Decision approved by Manager', NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'pending_approval', $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Decision approved, case resolved']);
        exit;
    }
    
    // Reject decision (Manager)
    if ($_GET['action'] === 'reject_decision' && isset($_POST['incident_id']) && $isManager) {
        $stmt = $db->prepare("UPDATE incidents SET status = 'in_progress', rejection_reason = ?, previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['rejection_reason'] ?? '', $_POST['previous_status'] ?? 'pending_approval', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Reject Decision', ?, 'in_progress', ?, ?, NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'pending_approval', $userId, $_POST['rejection_reason'] ?? 'Decision rejected, returned to investigation']);
        
        echo json_encode(['success' => true, 'message' => 'Decision rejected, case returned to investigation']);
        exit;
    }
    
    // Auto-escalate critical cases
    if ($_GET['action'] === 'escalate_case' && isset($_POST['incident_id']) && ($isHR || $isManager)) {
        $stmt = $db->prepare("UPDATE incidents SET status = 'escalated', escalation_level = escalation_level + 1, previous_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$_POST['previous_status'] ?? 'in_progress', $_POST['incident_id']]);
        
        // Add audit log
        $auditStmt = $db->prepare("INSERT INTO incident_audit_log (incident_id, action, old_status, new_status, performed_by, notes, created_at) VALUES (?, 'Escalate', ?, 'escalated', ?, 'Case escalated to senior HR', NOW())");
        $auditStmt->execute([$_POST['incident_id'], $_POST['previous_status'] ?? 'in_progress', $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Case escalated']);
        exit;
    }
    
    // Get repeat offender info
    if ($_GET['action'] === 'check_repeat_offender' && isset($_GET['respondent_name'])) {
        $stmt = $db->prepare("SELECT COUNT(*) as count, MAX(resolved_at) as last_violation FROM incidents WHERE respondent_name = ? AND status = 'resolved'");
        $stmt->execute([$_GET['respondent_name']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'is_repeat_offender' => $result['count'] >= 3,
            'violation_count' => $result['count'],
            'last_violation' => $result['last_violation']
        ]);
        exit;
    }
    
    // Get notifications for current user
    if ($_GET['action'] === 'get_notifications') {
        $stmt = $db->prepare("SELECT * FROM incident_notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
    // Mark notification as read
    if ($_GET['action'] === 'mark_notification_read' && isset($_POST['notification_id'])) {
        $stmt = $db->prepare("UPDATE incident_notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['notification_id'], $userId]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // Export incidents
    if ($_GET['action'] === 'export' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $format = $_POST['format'] ?? 'csv';
        $status = $_POST['status'] ?? '';
        $category = $_POST['category'] ?? '';
        
        $sql = "SELECT i.*, e.first_name, e.last_name, e.department FROM incidents i LEFT JOIN employees e ON i.reporter_id = e.id WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
        }
        if ($category) {
            $sql .= " AND i.incident_type = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY i.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="incidents_" . date("Y-m-d") . ".csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Incident ID', 'Employee', 'Department', 'Type', 'Category', 'Severity', 'Status', 'Date', 'Location', 'Description']);
            
            foreach ($incidents as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['incident_id'],
                    $row['first_name'] . ' ' . $row['last_name'],
                    $row['department'],
                    $row['incident_type'],
                    $row['category'] ?? 'N/A',
                    $row['severity'] ?? 'N/A',
                    $row['status'],
                    $row['incident_date'],
                    $row['location'],
                    substr($row['description'] ?? '', 0, 100)
                ]);
            }
            fclose($output);
        }
        exit;
    }
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Report new incident
    if ($_POST['action'] === 'report_incident') {
        try {
            // Generate incident ID
            $year = date('Y');
            $stmt = $db->query("SELECT MAX(CAST(SUBSTRING_INDEX(incident_id, '-', -1) AS UNSIGNED)) as max_id FROM incidents WHERE incident_id LIKE 'INC-{$year}-%'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextNum = ($result['max_id'] ?? 0) + 1;
            $incidentId = sprintf("INC-%s-%04d", $year, $nextNum);
            
            // Handle employee selection
            $employeeId = $_POST['employee_id'] ?? null;
            $reporterId = $userId;
            
            // Check if anonymous
            $isAnonymous = isset($_POST['is_anonymous']) ? 1 : 0;
            if ($isAnonymous) {
                $reporterId = null;
            }
            
            $stmt = $db->prepare("
                INSERT INTO incidents (
                    incident_id, reporter_id, incident_type, category, 
                    severity, title, description, incident_date, incident_time,
                    location, complainant_name, respondent_name, witnesses,
                    is_anonymous, is_confidential, status, reported_by, created_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', ?, NOW()
                )
            ");
            $stmt->execute([
                $incidentId,
                $reporterId,
                $_POST['incident_type'] ?? '',
                $_POST['category'] ?? '',
                $_POST['severity'] ?? 'medium',
                $_POST['title'] ?? '',
                $_POST['description'] ?? '',
                $_POST['incident_date'] ?? date('Y-m-d'),
                $_POST['incident_time'] ?? date('H:i'),
                $_POST['location'] ?? '',
                $_POST['complainant_name'] ?? '',
                $_POST['respondent_name'] ?? '',
                $_POST['witnesses'] ?? '',
                $isAnonymous,
                isset($_POST['is_confidential']) ? 1 : 0,
                $_POST['reported_by'] ?? 'Employee'
            ]);
            
            $incidentDbId = $db->lastInsertId();
            
            // Handle file uploads
            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadDir = '../../uploads/incidents/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                foreach ($_FILES['attachments']['name'] as $key => $name) {
                    if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['attachments']['tmp_name'][$key];
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $newName = $incidentId . '_' . time() . '_' . $key . '.' . $ext;
                        $targetPath = $uploadDir . $newName;
                        
                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $fileStmt = $db->prepare("INSERT INTO incident_evidence (incident_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                            $fileStmt->execute([$incidentDbId, $name, $newName, $_FILES['attachments']['type'][$key]]);
                        }
                    }
                }
            }
            
            $message = 'Incident reported successfully! ID: ' . $incidentId;
            $messageType = 'success';
            
            // Return JSON for AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => true, 'message' => $message, 'incident_id' => $incidentId]);
                exit;
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'danger';
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => $message]);
                exit;
            }
        }
    }
    
    // Update incident status
    if ($_POST['action'] === 'update_incident' && ($isManager || $isHR)) {
        try {
            $stmt = $db->prepare("
                UPDATE incidents 
                SET incident_type = ?, category = ?, severity = ?, status = ?,
                    description = ?, assigned_to = ?, resolution_notes = ?, 
                    resolved_at = CASE WHEN ? = 'resolved' THEN NOW() ELSE resolved_at END,
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['incident_type'],
                $_POST['category'],
                $_POST['severity'],
                $_POST['status'],
                $_POST['description'],
                $_POST['assigned_to'] ?? $userId,
                $_POST['resolution_notes'] ?? null,
                $_POST['status'],
                $_POST['incident_id']
            ]);
            $message = 'Incident updated successfully!';
            $messageType = 'success';
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => true, 'message' => $message]);
                exit;
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'danger';
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => $message]);
                exit;
            }
        }
    }
    
    // Resolve incident
    if ($_POST['action'] === 'resolve_incident' && ($isManager || $isHR)) {
        try {
            $stmt = $db->prepare("UPDATE incidents SET status = 'resolved', resolution_notes = ?, resolved_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$_POST['resolution_notes'], $_POST['incident_id']]);
            $message = 'Incident resolved successfully!';
            $messageType = 'success';
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => true, 'message' => $message]);
                exit;
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'danger';
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => $message]);
                exit;
            }
        }
    }
    
    // Add investigation note
    if ($_POST['action'] === 'add_note' && ($isManager || $isHR)) {
        try {
            $stmt = $db->prepare("INSERT INTO incident_notes (incident_id, content, created_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_POST['incident_id'], $_POST['content'], $userId]);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => true]);
                exit;
            }
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }
    }
    
    // Assign HR officer
    if ($_POST['action'] === 'assign_officer' && ($isManager || $isHR)) {
        try {
            $stmt = $db->prepare("UPDATE incidents SET assigned_to = ?, status = 'in_progress', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$_POST['officer_id'], $_POST['incident_id']]);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => true]);
                exit;
            }
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }
    }
}

// Get all incidents with reporter info
$query = "
    SELECT i.*, 
           e.first_name as reporter_first_name,
           e.last_name as reporter_last_name,
           e.department as reporter_department,
           a.first_name as assignee_first_name,
           a.last_name as assignee_last_name
    FROM incidents i
    LEFT JOIN employees e ON i.reporter_id = e.id
    LEFT JOIN employees a ON i.assigned_to = a.id
    ORDER BY i.id DESC
";
$stmt = $db->query($query);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$stats = [
    'total' => count($incidents),
    'filed' => count(array_filter($incidents, fn($i) => $i['status'] === 'filed')),
    'under_review' => count(array_filter($incidents, fn($i) => $i['status'] === 'under_review')),
    'investigating' => count(array_filter($incidents, fn($i) => $i['status'] === 'investigating')),
    'resolved' => count(array_filter($incidents, fn($i) => $i['status'] === 'resolved')),
    'closed' => count(array_filter($incidents, fn($i) => $i['status'] === 'closed'))
];

// Include Header Template
include "../components/header_template.php";
?>

<!-- Custom CSS -->
<link rel="stylesheet" href="compliance.css">
<link rel="stylesheet" href="incidents.css">

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $message ?>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards - 3x3 Grid -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number"><?= $stats['total'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Filed</span>
                        <span class="info-box-number"><?= $stats['filed'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-search"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Under Review</span>
                        <span class="info-box-number"><?= $stats['under_review'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Investigating</span>
                        <span class="info-box-number"><?= $stats['investigating'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Resolved</span>
                        <span class="info-box-number"><?= $stats['resolved'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-archive"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Closed</span>
                        <span class="info-box-number"><?= $stats['closed'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Incidents Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Incident Cases</h3>
                        <div class="card-tools">
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reportIncidentModal">
                                <i class="fas fa-exclamation-triangle"></i> Report Incident
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Reporter</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($incidents)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                        <p>No incidents found</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($incidents as $incident): ?>
                                <tr>
                                    <td><?= $incident['id'] ?></td>
                                    <td>
                                        <?php 
                                        $typeLabels = [
                                            'complaint' => 'Complaint',
                                            'disciplinary_case' => 'Disciplinary',
                                            'legal_incident' => 'Legal',
                                            'accident' => 'Accident',
                                            'harassment' => 'Harassment',
                                            'violation' => 'Violation'
                                        ];
                                        echo htmlspecialchars($typeLabels[$incident['incident_type']] ?? ucfirst($incident['incident_type']));
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($incident['title'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($incident['location'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($incident['is_anonymous']): ?>
                                            <span class="text-muted">Anonymous</span>
                                        <?php else: ?>
                                            <?= htmlspecialchars(($incident['reporter_first_name'] ?? '') . ' ' . ($incident['reporter_last_name'] ?? '')) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($incident['incident_date'])) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'filed' => 'warning',
                                            'under_review' => 'info',
                                            'investigating' => 'primary',
                                            'resolved' => 'success',
                                            'closed' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'filed' => 'Filed',
                                            'under_review' => 'Under Review',
                                            'investigating' => 'Investigating',
                                            'resolved' => 'Resolved',
                                            'closed' => 'Closed'
                                        ];
                                        ?>
                                        <span class="badge badge-<?= $statusClass[$incident['status']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$incident['status']] ?? ucfirst($incident['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#viewModal<?= $incident['id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($isManager || $isHR): ?>
                                        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#updateModal<?= $incident['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?= $incident['id'] ?>" data-backdrop="false">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info">
                                                <h4 class="modal-title">Incident #<?= $incident['id'] ?></h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Type:</strong> <?= htmlspecialchars($typeLabels[$incident['incident_type']] ?? $incident['incident_type']) ?></p>
                                                <p><strong>Title:</strong> <?= htmlspecialchars($incident['title']) ?></p>
                                                <p><strong>Description:</strong></p>
                                                <p><?= nl2br(htmlspecialchars($incident['description'])) ?></p>
                                                <p><strong>Location:</strong> <?= htmlspecialchars($incident['location'] ?? 'N/A') ?></p>
                                                <p><strong>Incident Date:</strong> <?= date('M d, Y', strtotime($incident['incident_date'])) ?></p>
                                                <p><strong>Reporter:</strong> <?= $incident['is_anonymous'] ? 'Anonymous' : htmlspecialchars(($incident['reporter_first_name'] ?? '') . ' ' . ($incident['reporter_last_name'] ?? '')) ?></p>
                                                <p><strong>Confidential:</strong> <?= $incident['is_confidential'] ? 'Yes' : 'No' ?></p>
                                                <?php if ($incident['resolution_notes']): ?>
                                                <p><strong>Resolution Notes:</strong></p>
                                                <p><?= nl2br(htmlspecialchars($incident['resolution_notes'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Update Status Modal -->
                                <?php if ($isManager || $isHR): ?>
                                <div class="modal fade" id="updateModal<?= $incident['id'] ?>" data-backdrop="false">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header bg-primary">
                                                    <h4 class="modal-title">Update Incident #<?= $incident['id'] ?></h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="incident_id" value="<?= $incident['id'] ?>">
                                                    
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control" required>
                                                            <option value="filed" <?= $incident['status'] === 'filed' ? 'selected' : '' ?>>Filed</option>
                                                            <option value="under_review" <?= $incident['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                                                            <option value="investigating" <?= $incident['status'] === 'investigating' ? 'selected' : '' ?>>Investigating</option>
                                                            <option value="resolved" <?= $incident['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                                            <option value="closed" <?= $incident['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Resolution Notes</label>
                                                        <textarea name="resolution_notes" class="form-control" rows="4" placeholder="Enter resolution notes..."><?= htmlspecialchars($incident['resolution_notes'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Report Incident Modal -->
<div class="modal fade" id="reportIncidentModal" data-backdrop="false" data-keyboard="true" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="incidentReportForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="report_incident">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Report Incident</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Employee Selection -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee Involved *</label>
                                <div class="employee-search-wrapper">
                                    <input type="text" id="employeeSearch" class="form-control" placeholder="Search employee by name or ID..." autocomplete="off">
                                    <input type="hidden" id="selectedEmployeeId" name="employee_id">
                                    <div id="employeeDropdown" class="employee-dropdown" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" id="employeeDepartment" class="form-control" readonly placeholder="Auto-populated">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Incident Date *</label>
                                <input type="date" name="incident_date" id="incidentDate" class="form-control" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Incident Time</label>
                                <input type="time" name="incident_time" id="incidentTime" class="form-control" value="<?= date('H:i') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Incident Type *</label>
                                <select name="incident_type" id="incidentType" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="violation">Violation</option>
                                    <option value="accident">Accident</option>
                                    <option value="misconduct">Misconduct</option>
                                    <option value="others">Others</option>
                                </select>
                                <div id="othersTypeField" class="form-group mt-2" style="display: none;">
                                    <input type="text" name="incident_type_others" class="form-control" placeholder="Please specify...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Incident Category *</label>
                                <select name="category" id="incidentCategory" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="workplace_safety">Workplace Safety</option>
                                    <option value="payroll_issue">Payroll Issue</option>
                                    <option value="attendance_issue">Attendance Issue</option>
                                    <option value="discrimination">Discrimination</option>
                                    <option value="data_privacy">Data Privacy</option>
                                    <option value="others">Others</option>
                                </select>
                                <div id="othersCategoryField" class="form-group mt-2" style="display: none;">
                                    <input type="text" name="category_others" class="form-control" placeholder="Please specify...">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location *</label>
                                <select name="location" class="form-control" required>
                                    <option value="">Select Location</option>
                                    <option value="Office">Office</option>
                                    <option value="Remote">Remote</option>
                                    <option value="Branch">Branch</option>
                                    <option value="Off-site">Off-site</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Severity Level *</label>
                                <select name="severity" class="form-control" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" class="form-control" required placeholder="Brief title of the incident">
                    </div>
                    
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" id="incidentDescription" class="form-control" rows="5" required placeholder="Describe the incident in detail..."></textarea>
                    </div>
                    
                    <!-- People Involved Section -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Complainant Name</label>
                                <input type="text" name="complainant_name" class="form-control" placeholder="Who is reporting?">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Respondent Name</label>
                                <input type="text" name="respondent_name" class="form-control" placeholder="Who is involved?">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Witnesses</label>
                                <input type="text" name="witnesses" class="form-control" placeholder="Witness names">
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Attachments -->
                    <div class="form-group">
                        <label>Attachments</label>
                        <div class="file-upload-wrapper" id="fileDropZone">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                            <p>Drag & drop files here or click to browse</p>
                            <p class="text-muted small">Supported: Images, PDFs, Videos (Max 10MB each)</p>
                            <input type="file" name="attachments[]" id="incidentAttachments" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.mp4,.webm" style="display: none;">
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                    </div>
                    
                    <!-- Options -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="d-block">Options</label>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="is_anonymous" class="form-check-input" id="isAnonymous">
                                    <label class="form-check-label" for="isAnonymous">Report Anonymously</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="is_confidential" class="form-check-input" id="isConfidential">
                                    <label class="form-check-label" for="isConfidential">Mark as Confidential</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reported By</label>
                                <select name="reported_by" class="form-control">
                                    <option value="Employee">Employee</option>
                                    <option value="HR">HR</option>
                                    <option value="Anonymous">Anonymous</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane mr-1"></i>Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include Footer Template
include "../components/footer_template.php";
?>

<!-- ===================================================== -->
<!-- WORKFLOW MODALS -->
<!-- ===================================================== -->

<!-- HR Review Modal -->
<div class="modal fade" id="hrReviewModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-search"></i> HR Review - Incident #<span id="hrReviewIncidentId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Review this incident to determine if it's valid and requires further action.
                </div>
                <div id="hrReviewDetails"></div>
                
                <div class="form-group mt-3">
                    <label>Classification</label>
                    <select class="form-control" id="reviewViolationType">
                        <option value="minor">Minor Violation</option>
                        <option value="major">Major Violation</option>
                    </select>
                    <small class="text-muted">Minor: Late attendance, minor misconduct | Major: Harassment, fraud, safety breach</small>
                </div>
                
                <div class="form-group">
                    <label>Severity Level</label>
                    <select class="form-control" id="reviewSeverity">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes (for rejection or info request)</label>
                    <textarea class="form-control" id="hrReviewNotes" rows="3" placeholder="Enter notes if rejecting or requesting additional information..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="hrReject()"><i class="fas fa-times"></i> Reject</button>
                <button type="button" class="btn btn-info" onclick="hrRequestInfo()"><i class="fas fa-question"></i> Request Info</button>
                <button type="button" class="btn btn-success" onclick="hrAccept()"><i class="fas fa-check"></i> Accept & Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Investigator Modal -->
<div class="modal fade" id="assignInvestigatorModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="fas fa-user-tie"></i> Assign Investigator - Incident #<span id="assignInvestigatorId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary">
                    <i class="fas fa-info-circle"></i> Assign an HR Officer to investigate this case. SLA deadline will be set to 5 days.
                </div>
                
                <div class="form-group">
                    <label>Search HR Officer</label>
                    <input type="text" class="form-control" id="searchHrOfficer" placeholder="Search by name..." onkeyup="searchHrOfficers()">
                    <div id="hrOfficerResults" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
                
                <div class="form-group">
                    <label>Selected Officer</label>
                    <input type="hidden" id="selectedOfficerId">
                    <div id="selectedOfficerName" class="text-muted">No officer selected</div>
                </div>
                
                <div class="alert alert-warning" id="repeatOffenderWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Repeat Offender Detected!</strong> This respondent has 3 or more previous violations.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="assignInvestigator()"><i class="fas fa-user-plus"></i> Assign & Start Investigation</button>
            </div>
        </div>
    </div>
</div>

<!-- Investigation Notes Modal -->
<div class="modal fade" id="investigationNotesModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-clipboard-list"></i> Investigation Notes - Incident #<span id="investigationNotesId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="card-title">Case Details</h6></div>
                            <div class="card-body" id="investigationCaseDetails"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h6 class="card-title">Evidence & Attachments</h6></div>
                            <div class="card-body" id="investigationEvidence"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-3">
                    <label>Add Investigation Note</label>
                    <textarea class="form-control" id="investigationNoteContent" rows="3" placeholder="Enter investigation notes, findings, or updates..."></textarea>
                </div>
                <button type="button" class="btn btn-primary" onclick="addInvestigationNote()"><i class="fas fa-plus"></i> Add Note</button>
                
                <h6 class="mt-4">Previous Notes</h6>
                <div class="timeline" id="investigationNotesTimeline"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="escalateCase()"><i class="fas fa-arrow-up"></i> Escalate</button>
                <button type="button" class="btn btn-success" onclick="showDecisionModal()"><i class="fas fa-gavel"></i> Submit Decision</button>
            </div>
        </div>
    </div>
</div>

<!-- Decision & Action Modal -->
<div class="modal fade" id="decisionModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-gavel"></i> Submit Decision - Incident #<span id="decisionIncidentId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Final Decision</label>
                    <select class="form-control" id="finalDecision" onchange="toggleApprovalRequirement()">
                        <option value="">Select Decision...</option>
                        <option value="no_violation">No Violation Found - Close Case</option>
                        <option value="verbal_warning">Verbal Warning</option>
                        <option value="written_warning">Written Warning</option>
                        <option value="suspension">Suspension</option>
                        <option value="termination">Termination</option>
                    </select>
                </div>
                
                <div class="alert alert-info" id="approvalRequirementAlert" style="display: none;">
                    <i class="fas fa-info-circle"></i> This action requires Manager approval.
                </div>
                
                <div class="form-group">
                    <label>Resolution Notes</label>
                    <textarea class="form-control" id="resolutionNotes" rows="5" placeholder="Document findings and decision..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" id="decisionRemarks" rows="2" placeholder="Additional remarks..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitDecision()"><i class="fas fa-check"></i> Submit Decision</button>
            </div>
        </div>
    </div>
</div>

<!-- Manager Approval Modal -->
<div class="modal fade" id="approvalModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-check-double"></i> Manager Approval - Incident #<span id="approvalIncidentId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i> Review the HR decision and approve or reject it.
                </div>
                <div id="approvalDetails"></div>
                
                <div class="form-group mt-3">
                    <label>Manager Comments</label>
                    <textarea class="form-control" id="managerComments" rows="3" placeholder="Enter approval or rejection comments..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="rejectDecision()"><i class="fas fa-times"></i> Reject</button>
                <button type="button" class="btn btn-success" onclick="approveDecision()"><i class="fas fa-check"></i> Approve & Resolve</button>
            </div>
        </div>
    </div>
</div>

<!-- Audit Log Modal -->
<div class="modal fade" id="auditLogModal" data-backdrop="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title"><i class="fas fa-history"></i> Audit Trail - Incident #<span id="auditLogIncidentId"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="timeline" id="auditLogTimeline"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include Incidents JavaScript -->
<script src="incidents.js"></script>

<!-- File upload handling -->
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('incidentAttachments');
    
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', function() {
            fileInput.click();
        });
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        });
        
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }
});

function handleFiles(files) {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const div = document.createElement('div');
        div.className = 'file-preview-item';
        div.innerHTML = `
            <i class="fas fa-file"></i>
            <span class="file-name">${file.name}</span>
            <span class="file-size">${formatSize(file.size)}</span>
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        preview.appendChild(div);
    }
}

function formatSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
