<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    $controller = new AnnouncementController($pdo);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // All roles can view announcements (filtered by role/department)
        Auth::requirePermission('announcements', 'view');
        $result = $controller->handleRequest();
        echo json_encode(['announcements' => $result]);
    } 
    elseif ($method === 'POST') {
        // Only Admin and HR Manager can create
        Auth::requirePermission('announcements', 'create');
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Only Admin can edit all, HR can edit own
        Auth::requirePermission('announcements', 'edit');
        $controller->handleRequest();
    } 
    elseif ($method === 'DELETE') {
        // Only Admin can delete
        Auth::requirePermission('announcements', 'delete');
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
