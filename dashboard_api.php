<?php
session_start();
require_once "auth/auth_check.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

require_once "auth/database.php";

$action = $_POST['action'] ?? '';
$response = [];

try {
    $db = Database::getInstance()->getConnection();

    switch ($action) {
        case 'get_pending_resignations_count':
            $stmt = $db->query("SELECT COUNT(*) as count FROM resignations WHERE status = 'pending'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = ['count' => (int)$result['count'], 'success' => true];
            break;

        case 'get_total_employees':
            $stmt = $db->query("SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active' OR employment_status = 'active'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = ['count' => (int)$result['count'], 'success' => true];
            break;

        default:
            $response = ['success' => false, 'error' => 'Unknown action'];
            http_response_code(400);
    }
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
    http_response_code(500);
}

header('Content-Type: application/json');
echo json_encode($response);
?>
