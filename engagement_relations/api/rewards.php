<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../controllers/RewardController.php';

$user = Auth::requireAuth();
$method = $_SERVER['REQUEST_METHOD'];

try {
    $controller = new RewardController($pdo, $user);
    
    if ($method === 'GET') {
        Auth::requirePermission('rewards', 'view');
        $result = $controller->handleRequest();
        if (is_array($result)) {
            echo json_encode(['rewards' => $result]);
        }
    } elseif ($method === 'POST') {
        Auth::requirePermission('rewards', 'create');
        $controller->handleRequest();
    } elseif ($method === 'PUT') {
        Auth::requirePermission('rewards', 'edit');
        $controller->handleRequest();
    } elseif ($method === 'DELETE') {
        Auth::requirePermission('rewards', 'delete');
        $controller->handleRequest();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
