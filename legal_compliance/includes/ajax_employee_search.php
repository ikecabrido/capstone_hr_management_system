<?php
/**
 * AJAX Employee Search Handler
 * HR Legal & Compliance System
 * Bestlink College of the Philippines
 * 
 * Handles AJAX requests for employee search (autocomplete)
 * Uses sample_hr database employees table
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../auth/database.php';
$db = Database::getInstance()->getConnection();

// Set headers for JSON response
header('Content-Type: application/json');

// Check if search query is provided
$searchTerm = isset($_POST['query']) ? trim($_POST['query']) : (isset($_GET['query']) ? trim($_GET['query']) : '');

if (empty($searchTerm)) {
    echo json_encode([]);
    exit;
}

// Search employees by ID or name in sample_hr database
$searchPattern = '%' . $searchTerm . '%';

// Use correct columns FROM lc_sample_hr.employees table
// Table structure: employee_id, employee_no, full_name, status, etc.
$sql = "SELECT MIN(employee_id) as id, employee_no, full_name as first_name, '' as last_name, employment_status as status 
        FROM employees 
        WHERE employment_status = 'active' 
        AND (employee_no LIKE ? OR full_name LIKE ?)
        GROUP BY full_name, employee_no, department, position
        ORDER BY full_name
        LIMIT 10";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute([$searchPattern, $searchPattern]);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    $results = [];
    foreach ($employees as $emp) {
        $fullName = trim($emp['first_name'] . ' ' . $emp['last_name']);
        $results[] = [
            'id' => $emp['id'],
            'employee_id' => $emp['employee_no'],
            'name' => $fullName,
            'first_name' => $emp['first_name'],
            'last_name' => $emp['last_name'],
            'status' => $emp['status']
        ];
    }
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    error_log("Employee search error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}
