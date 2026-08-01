<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Set a fake session for testing
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'theme' => 'light'];

require_once "../auth/database.php";
require_once "models/ExitManagementModel.php";
require_once "controllers/ExitManagementController.php";

header('Content-Type: application/json');

try {
    // Test database connection
    $db = Database::getInstance()->getConnection();
    
    // Query employees directly
    $query = "SELECT 
                e.employee_id AS id,
                e.full_name,
                COALESCE(u.username, e.employee_id) AS username,
                e.email,
                e.department,
                e.position,
                e.employment_status AS employee_status
            FROM employees e
            LEFT JOIN users u ON e.user_id = u.id
            ORDER BY e.full_name LIMIT 10";
    
    $stmt = $db->query($query);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'count' => count($employees),
        'sample_employees' => $employees,
        'message' => 'Database query successful'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
?>
