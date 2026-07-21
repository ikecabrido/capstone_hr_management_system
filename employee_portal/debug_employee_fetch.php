<?php
session_start();

require 'app/config/Database.php';
require 'app/models/Employee.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('Not logged in. Session user_id: ' . json_encode($_SESSION));
}

$user_id = $_SESSION['user_id'];
echo "User ID from session: " . htmlspecialchars($user_id) . "<br>";

// Test database connection
try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "Database connection: OK<br>";
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Test employee model
$employeeModel = new Employee();
$employee = $employeeModel->findByUserId($user_id);

if ($employee) {
    echo "Employee found:<br>";
    echo "<pre>" . print_r($employee, true) . "</pre>";
} else {
    echo "Employee NOT found for user_id: " . htmlspecialchars($user_id) . "<br>";
    
    // Check if user exists
    $userQuery = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($userQuery);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User exists:<br>";
        echo "<pre>" . print_r($user, true) . "</pre>";
        
        // Check employees table for this user_id
        $empQuery = "SELECT * FROM employees WHERE user_id = :user_id";
        $stmt = $conn->prepare($empQuery);
        $stmt->execute([':user_id' => $user_id]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($emp) {
            echo "Employee record found in database:<br>";
            echo "<pre>" . print_r($emp, true) . "</pre>";
        } else {
            echo "No employee record found for this user_id<br>";
            
            // List all employees
            $allEmps = "SELECT employee_id, user_id, full_name FROM employees LIMIT 5";
            $stmt = $conn->prepare($allEmps);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "First 5 employees:<br>";
            echo "<pre>" . print_r($employees, true) . "</pre>";
        }
    } else {
        echo "User does NOT exist in database for user_id: " . htmlspecialchars($user_id) . "<br>";
    }
}

?>
