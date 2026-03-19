<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/FeedbackController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    $controller = new FeedbackController($pdo, $user);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // Admin and HR can view all, Employees see only their own
        Auth::requirePermission('feedback', 'view');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['feedback' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // Only Employees can submit feedback
        Auth::requirePermission('feedback', 'create');
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Only HR and Employees can edit (HR respond, Employee edit own)
        Auth::requirePermission('feedback', 'edit');
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
