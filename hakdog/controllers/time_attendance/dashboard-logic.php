<?php
/**
 * Time & Attendance Dashboard Logic
 * Loads all data and variables needed for the dashboard view
 */

// Set error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Use root-level database connector
    $rootDir = dirname(__DIR__, 3);
    require_once $rootDir . '/auth/database.php';
    require_once $rootDir . '/auth/auth.php';
    
    // Load employee portal dependencies
    $portalDir = dirname(__DIR__, 2);
    require_once $portalDir . '/app/controllers/AttendanceController.php';
    require_once $portalDir . '/app/models/Employee.php';
    require_once $portalDir . '/app/models/Attendance.php';
    require_once $portalDir . '/app/models/Leave.php';
    require_once $portalDir . '/app/models/EmployeeShift.php';
    require_once $portalDir . '/app/models/Shift.php';
    require_once $portalDir . '/app/helpers/Helper.php';
    
    // Check authentication
    $auth = new Auth();
    if (!$auth->check()) {
        header("Location: " . dirname(__DIR__, 3) . "/login_form.php");
        exit;
    }

    // Get user ID from session
    $user_id = $_SESSION['user']['id'] ?? null;
    if (!$user_id) {
        throw new Exception("No user ID in session");
    }

    // Initialize models
    $employeeModel = new Employee();
    $attendanceModel = new Attendance();
    $leaveModel = new Leave();
    
    // Get database connection
    $conn = Database::getInstance()->getConnection();
    $employeeShiftModel = new EmployeeShift($conn);
    $attendanceController = new AttendanceController();

    // Get employee details
    $employee = $employeeModel->getByUserId($user_id);
    if (!is_array($employee) || !isset($employee['employee_id'])) {
        throw new Exception("Employee record not found");
    }
    
    $employee_id = $employee['employee_id'];
    
    // Load dashboard view
    $viewFile = __DIR__ . '/view.php';
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        throw new Exception("View file not found: " . $viewFile);
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error Loading Dashboard</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<small>File: " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</small>";
    echo "</div>";
}
?>
