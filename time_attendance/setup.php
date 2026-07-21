<?php
/**
 * Time & Attendance System - Database Setup Script
 * Creates all necessary tables for shift validation and emergency holidays
 * 
 * Run this once to initialize the database schema
 */

session_start();
require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/app/models/ShiftValidator.php';
require_once __DIR__ . '/app/models/UnexpectedHoliday.php';

// Check if user is authenticated and has HR role
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? $_SESSION['role'] ?? '') !== 'HR') {
    http_response_code(403);
    die('Unauthorized. HR role required.');
}

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    echo "<h2>Time & Attendance System - Database Setup</h2>";
    echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
    
    // Initialize ShiftValidator (creates ta_shift_assignments table)
    echo "<p><strong>Creating Shift Assignments Table...</strong></p>";
    $shiftValidator = new ShiftValidator();
    echo "<p style='color: green;'>✓ ta_shift_assignments table ready</p>";
    
    // Initialize UnexpectedHoliday (creates ta_unexpected_holidays table)
    echo "<p><strong>Creating Unexpected Holidays Table...</strong></p>";
    $unexpectedHoliday = new UnexpectedHoliday();
    echo "<p style='color: green;'>✓ ta_unexpected_holidays table ready</p>";
    
    // Verify shifts exist
    echo "<p><strong>Checking Shifts...</strong></p>";
    $shiftsQuery = "SELECT COUNT(*) as count FROM ta_shifts WHERE is_active = 1";
    $shiftsStmt = $conn->prepare($shiftsQuery);
    $shiftsStmt->execute();
    $shiftsResult = $shiftsStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($shiftsResult['count'] > 0) {
        echo "<p style='color: green;'>✓ Found " . $shiftsResult['count'] . " active shifts</p>";
    } else {
        echo "<p style='color: orange;'>⚠ No shifts configured yet. HR must add shifts in the system.</p>";
    }
    
    // Verify employees exist
    echo "<p><strong>Checking Employees...</strong></p>";
    $empQuery = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'";
    $empStmt = $conn->prepare($empQuery);
    $empStmt->execute();
    $empResult = $empStmt->fetch(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✓ Found " . $empResult['count'] . " active employees</p>";
    
    // Get unassigned count
    $unassignedCount = $shiftValidator->getUnassignedShiftCount();
    echo "<p><strong>Unassigned Employees:</strong> " . $unassignedCount . "</p>";
    
    echo "</div>";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Database tables are now ready. You can now:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='views/shift_management.php'>Shift Management</a> to assign shifts to employees</li>";
    echo "<li>Go to <a href='public/dashboard.php'>Dashboard</a> to see the alert banner</li>";
    echo "<li>Use <a href='api/shift_assignment.php?action=get_unassigned_count'>API endpoints</a> for automation</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    http_response_code(500);
    echo "<h3 style='color: red;'>Setup Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
