<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/RecognitionController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    $controller = new RecognitionController($pdo);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // All roles can view recognition
        Auth::requirePermission('recognition', 'view');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['recognitions' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // All roles can give recognition
        Auth::requirePermission('recognition', 'create');
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Check if approving recognition
        if (strpos($_SERVER['REQUEST_URI'], 'approve') !== false) {
            // Only Admin and HR can approve
            Auth::requirePermission('recognition', 'edit');
            logAuditAction($user['id'], 'APPROVE_RECOGNITION', 'recognition', $_GET['id'] ?? 'unknown', 'Approval action');
        } else {
            // Admin can edit all
            Auth::requirePermission('recognition', 'edit');
        }
        $controller->handleRequest();
    } 
    elseif ($method === 'DELETE') {
        // Only Admin can delete
        Auth::requirePermission('recognition', 'delete');
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
