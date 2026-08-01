<?php
/**
 * Migration Script: Update Resignations with Last Attendance Dates
 * 
 * This script updates existing resignations to populate their last_working_date
 * based on the most recent attendance_date from ta_attendance table for each employee.
 * 
 * Usage: Run this script once to backfill existing resignation data.
 */

session_start();
require_once "../auth/auth_check.php";
require_once "models/ResignationModel.php";

// Verify user is logged in (any role can run migrations)
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
    http_response_code(403);
    die('Access denied. Please log in first.');
}

// Check if user has a valid role
$validRoles = ['recruitment', 'payroll', 'time', 'compliance', 'workforce', 'employee', 'learning', 'performance', 'engagement_relations', 'exit', 'clinic'];
if (!in_array($_SESSION['user']['role'], $validRoles)) {
    http_response_code(403);
    echo "<p>Current role: " . ($_SESSION['user']['role'] ?? 'Not set') . "</p>";
    echo "<p>Valid roles: " . implode(', ', $validRoles) . "</p>";
    die('Access denied. Insufficient permissions.');
}

try {
    $resignationModel = new ResignationModel();
    $db = $resignationModel->getDb();
    
    // Start transaction
    $db->beginTransaction();
    
    // Get all resignations that might need updating
    $stmt = $db->query("
        SELECT id, employee_id, last_working_date, created_at
        FROM exit_resignations
        WHERE status IN ('pending', 'approved')
        ORDER BY id ASC
    ");
    $resignations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updated = 0;
    $skipped = 0;
    $errors = [];
    
    foreach ($resignations as $resignation) {
        $employeeId = $resignation['employee_id'];
        $currentLastWorkingDate = $resignation['last_working_date'];
        
        // Get the last attendance date from ta_attendance
        $lastAttendanceDateResult = $resignationModel->getEmployeeLastAttendanceDate($employeeId);
        
        if ($lastAttendanceDateResult) {
            // Update the resignation with the last attendance date
            $updateStmt = $db->prepare("
                UPDATE exit_resignations 
                SET last_working_date = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $success = $updateStmt->execute([
                $lastAttendanceDateResult,
                $resignation['id']
            ]);
            
            if ($success) {
                $updated++;
                echo "✓ Resignation ID {$resignation['id']}: Employee {$employeeId} - Updated to {$lastAttendanceDateResult}<br>";
            } else {
                $skipped++;
                $errors[] = "Failed to update Resignation ID {$resignation['id']}";
            }
        } else {
            $skipped++;
            echo "⊘ Resignation ID {$resignation['id']}: Employee {$employeeId} - No attendance records found<br>";
        }
    }
    
    $db->commit();
    
    // Summary
    echo "<hr>";
    echo "<h3>Migration Summary</h3>";
    echo "<p><strong>Total Resignations Processed:</strong> " . count($resignations) . "</p>";
    echo "<p><strong>Updated:</strong> <span style='color:green;'>{$updated}</span></p>";
    echo "<p><strong>Skipped (no attendance data):</strong> <span style='color:orange;'>{$skipped}</span></p>";
    
    if (!empty($errors)) {
        echo "<h4>Errors:</h4>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li style='color:red;'>{$error}</li>";
        }
        echo "</ul>";
    }
    
    echo "<p style='margin-top: 20px;'><a href='exit_management.php' class='btn btn-primary'>Back to Exit Management</a></p>";
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
    error_log("Migration Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Resignations - Last Working Date Migration</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <h2>Resignation Last Working Date Migration</h2>
    <p>Processing resignations and updating last working dates from attendance records...</p>
</body>
</html>
