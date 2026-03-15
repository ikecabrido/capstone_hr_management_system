<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    // If DB connection fails, the controller will throw, so we catch and return a fallback.
    $controller = new EmployeeController($pdo, $user);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // All roles can view employees (with different data levels)
        Auth::requirePermission('employees', 'view');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['employees' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // Only Admin can create employees
        Auth::requirePermission('employees', 'create');
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Only Admin can update employee records
        Auth::requirePermission('employees', 'edit');
        logAuditAction($user['id'], 'UPDATE_EMPLOYEE', 'employees', $_GET['id'] ?? 'unknown', 'Admin updated employee record');
        $controller->handleRequest();
    } 
    elseif ($method === 'DELETE') {
        // Only Admin can delete
        Auth::requirePermission('employees', 'delete');
        logAuditAction($user['id'], 'DELETE_EMPLOYEE', 'employees', $_GET['id'] ?? 'unknown', 'Admin deleted employee record');
        $controller->handleRequest();
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    // If DB is unreachable, return a stub list so the frontend still renders.
    http_response_code(200);
    echo json_encode([
        'employees' => [
            ['id' => 1, 'name' => 'Guest Employee', 'department_id' => 0, 'email' => 'guest@example.com', 'role' => 'employee', 'status' => 'active'],
            ['id' => 2, 'name' => 'Demo Employee', 'department_id' => 0, 'email' => 'demo@example.com', 'role' => 'employee', 'status' => 'active'],
        ],
        'error' => 'Database unavailable: ' . $e->getMessage()
    ]);
}

/**
 * Log audit action for sensitive operations
 */
function logAuditAction($user_id, $action, $module, $record_id = null, $details = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs 
            (user_id, action, module, record_id, details, ip_address, timestamp)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user_id,
            $action,
            $module,
            $record_id,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        // Silently fail
    }
}
