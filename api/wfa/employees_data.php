<?php
/**
 * Employee Data API
 * Returns all employee information from the employees table
 */

header('Content-Type: application/json');
error_reporting(0); // Suppress PHP errors, return JSON instead

// Get database connection
$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get all employees
    $sql = "SELECT 
                e.employee_id,
                e.full_name,
                e.department,
                e.position,
                e.email,
                e.contact_number,
                e.date_hired,
                e.employment_status,
                YEAR(CURDATE()) - YEAR(e.date_hired) as years_employed
            FROM employees e
            ORDER BY e.full_name ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    $response = [
        'success' => true,
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            'total_employees' => count($employees),
            'employees' => $employees
        ]
    ];
    
    echo json_encode($response);
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
