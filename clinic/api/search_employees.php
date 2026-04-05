<?php
header('Content-Type: application/json');
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

$search = $_GET['q'] ?? '';

if (empty($search)) {
    echo json_encode([]);
    exit;
}

try {
    $search_term = "%$search%";
    $results = [];
    
    // 1. Try exact ID match if numeric
    if (is_numeric($search)) {
        $sql = "SELECT employee_id, full_name, email, contact_number as phone, department, position 
                FROM employees 
                WHERE employee_id = ? 
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$search]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 2. If no exact ID match found, or not numeric, perform broad search
    if (empty($results)) {
        $sql = "SELECT employee_id, full_name, email, contact_number as phone, department, position 
                FROM employees 
                WHERE full_name LIKE ? 
                   OR email LIKE ? 
                   OR contact_number LIKE ? 
                   OR department LIKE ? 
                   OR position LIKE ?
                LIMIT 20";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $search_term, 
            $search_term, 
            $search_term, 
            $search_term, 
            $search_term
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
