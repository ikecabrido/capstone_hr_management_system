<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/EventController.php';

// Verify user is authenticated
$user = Auth::requireAuth();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    $controller = new EventController($pdo);
    
    // Route based on method and user role
    if ($method === 'GET') {
        // All roles can view events
        Auth::requirePermission('events', 'view');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['events' => $result]);
        }
    } 
    elseif ($method === 'POST') {
        // Check if registering for event or creating new event
        if (strpos($_SERVER['REQUEST_URI'], 'register') !== false) {
            // All roles can register for events
            Auth::requirePermission('events', 'view');
        } else {
            // Only Admin and HR can create events
            Auth::requirePermission('events', 'create');
        }
        $controller->handleRequest();
    } 
    elseif ($method === 'PUT') {
        // Only Admin and HR can update events
        Auth::requirePermission('events', 'edit');
        $controller->handleRequest();
    } 
    elseif ($method === 'DELETE') {
        // Only Admin can delete events
        Auth::requirePermission('events', 'delete');
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
