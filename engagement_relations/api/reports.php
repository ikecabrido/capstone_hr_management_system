<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/ReportController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Only Admin, HR, and IT can access reports (with different data)
    Auth::requirePermission('reports', 'view');
    
    $controller = new ReportController($pdo);
    
    // Route based on method
    if ($method === 'GET') {
        // Different reports based on role
        if (Auth::isAdmin()) {
            // Admin can access all reports
        } elseif (Auth::isHRManager()) {
            // HR can only access HR reports
        }
        
        // Log audit action for report access
        logAuditAction($user['id'], 'VIEW_REPORTS', 'reports', null, 'User accessed reports');
        
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['reports' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // Only Admin can create custom reports
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Only Admin can create reports']);
            exit;
        }
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
        // Silently fail
    }
}
