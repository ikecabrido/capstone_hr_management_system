<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/AuditLogController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Only Admin, HR (limited), and IT can access audit logs
    Auth::requirePermission('audit_logs', 'view');
    
    $controller = new AuditLogController($pdo);
    
    if ($method === 'GET') {
        // Log the access to audit logs (meta-audit)
        logAuditAction($user['id'], 'VIEW_AUDIT_LOGS', 'audit_logs', null, 'User accessed audit logs');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['audit_logs' => $result]);
        }
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
        // Silently fail
    }
}
