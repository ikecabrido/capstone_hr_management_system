<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/GrievanceController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    $controller = new GrievanceController($pdo, $user);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // Admin and HR can view all, Employees see only their own (confidential)
        Auth::requirePermission('grievances', 'view');
        
        // Log audit action for viewing grievances
        logAuditAction($user['id'], 'VIEW_GRIEVANCES', 'grievances', null, 'User accessed grievance list');
        
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['grievances' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // Only Employees can file grievances
        Auth::requirePermission('grievances', 'create');
        
        // Log audit action
        logAuditAction($user['id'], 'FILE_GRIEVANCE', 'grievances', null, 'Employee filed new grievance');
        
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Only HR can update grievance status/assignment
        Auth::requirePermission('grievances', 'edit');
        
        // Log audit action
        $grievance_id = $_GET['id'] ?? 'unknown';
        logAuditAction($user['id'], 'UPDATE_GRIEVANCE', 'grievances', $grievance_id, 'HR updated grievance status');
        
        $controller->handleRequest();
    } 
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Log audit action for sensitive operations
 */
function logAuditAction($user_id, $action, $module, $record_id = null, $details = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO audit_logs (action, performed_by, target_type, target_id, details, performed_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $action,
            $user_id,
            $module,
            $record_id,
            $details,
        ]);
    } catch (Exception $e) {
        // Silently fail - don't break main operation if audit logging fails
    }
}
