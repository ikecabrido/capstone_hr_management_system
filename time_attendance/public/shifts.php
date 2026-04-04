<?php
/**
 * Shift Management Page
 * HR-only page for managing shifts and employee assignments
 */

// Start session and check authentication
session_start();
require_once(__DIR__ . '/../app/config/Database.php');
require_once(__DIR__ . '/../app/core/Session.php');
require_once(__DIR__ . '/../app/controllers/ShiftController.php');
require_once(__DIR__ . '/../app/helpers/Helper.php');

// Verify user is logged in and is HR
if (empty($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: ../../login_form.php');
    exit();
}

// Check if user has time & attendance permissions
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'time' && $user_role !== 'HR_ADMIN' && $user_role !== 'payroll') {
    header('Location: shifts.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$shiftController = new ShiftController($db);

// Create ta_flexible_schedules table if it doesn't exist (do this once at startup)
try {
    $create_table_sql = "CREATE TABLE IF NOT EXISTS ta_flexible_schedules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        employee_id VARCHAR(50) NOT NULL,
        schedule_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        day_of_week INT,
        repeat_until DATE,
        contract_end_date DATE,
        notes TEXT,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_employee (employee_id),
        INDEX idx_date (schedule_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($create_table_sql);
    
    // Add contract_end_date column if it doesn't exist
    try {
        $check_column = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_NAME = 'ta_flexible_schedules' AND COLUMN_NAME = 'contract_end_date'";
        $result = $db->query($check_column)->fetch();
        if (!$result) {
            $db->exec("ALTER TABLE ta_flexible_schedules ADD COLUMN contract_end_date DATE AFTER repeat_until");
        }
    } catch (Exception $e) {
        // Column already exists
    }
    $check_constraint = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
                       WHERE TABLE_NAME = 'ta_flexible_schedules' AND CONSTRAINT_NAME = 'unique_schedule'";
    $result = $db->query($check_constraint)->fetch();
    if (!$result) {
        try {
            $db->exec("ALTER TABLE ta_flexible_schedules ADD CONSTRAINT unique_schedule UNIQUE (employee_id, schedule_date, start_time)");
        } catch (Exception $e) {
            // Constraint might already exist
        }
    }
    
    // CRITICAL FIX: Ensure employee_id is VARCHAR(50), not INT
    try {
        $check_column = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_NAME = 'ta_flexible_schedules' AND COLUMN_NAME = 'employee_id'";
        $column_result = $db->query($check_column)->fetch(PDO::FETCH_ASSOC);
        
        if ($column_result && strpos($column_result['COLUMN_TYPE'], 'INT') !== false) {
            // Column is INT, need to change it to VARCHAR(50)
            error_log("FIXING: Changing ta_flexible_schedules.employee_id from INT to VARCHAR(50)");
            $db->exec("ALTER TABLE ta_flexible_schedules MODIFY employee_id VARCHAR(50) NOT NULL");
            error_log("SUCCESS: employee_id column changed to VARCHAR(50)");
        }
    } catch (Exception $e) {
        error_log("Note: Could not modify employee_id column: " . $e->getMessage());
    }
} catch (Exception $e) {
    // Table already exists or other error - ignore
}

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_shift'])) {
        $result = $shiftController->createShift([
            'shift_name' => $_POST['shift_name'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'break_duration' => $_POST['break_duration'] ?? 60,
            'description' => $_POST['description'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['update_shift'])) {
        $result = $shiftController->updateShift($_POST['shift_id'], [
            'shift_name' => $_POST['shift_name'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'break_duration' => $_POST['break_duration'] ?? 60,
            'description' => $_POST['description'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['delete_shift'])) {
        $result = $shiftController->deleteShift($_POST['shift_id']);
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'list';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['assign_shift'])) {
        $result = $shiftController->assignShiftToEmployee(
            $_POST['employee_id'],
            $_POST['shift_id'],
            $_POST['effective_from'],
            $_POST['effective_to'] ?? null
        );
        
        if ($result['success']) {
            $message = $result['message'];
            $action = 'assignments';
        } else {
            $error = $result['message'];
        }
    }

    if (isset($_POST['create_flexible'])) {
        // Handle flexible schedule creation
        $employee_id = $_POST['flex_employee_id'] ?? '';
        $date = $_POST['flex_date'] ?? '';
        $start_time = $_POST['flex_start_time'] ?? '';
        $end_time = $_POST['flex_end_time'] ?? '';
        $notes = $_POST['flex_notes'] ?? '';
        $repeat_days = $_POST['flex_days'] ?? [];
        $repeat_until = !empty($_POST['flex_repeat_end_date']) ? $_POST['flex_repeat_end_date'] : null;
        $contract_end_date = !empty($_POST['flex_contract_end_date']) ? $_POST['flex_contract_end_date'] : null;

        // DEBUG: Log entire POST data to identify what's being sent
        error_log("=== FLEXIBLE SCHEDULE SUBMISSION DEBUG ===");
        error_log("Full POST data: " . json_encode($_POST));
        error_log("flex_employee_id value: '" . var_export($employee_id, true) . "' (type: " . gettype($employee_id) . ")");
        error_log("flex_employee_id isset: " . (isset($_POST['flex_employee_id']) ? 'YES' : 'NO'));
        error_log("All POST keys: " . implode(', ', array_keys($_POST)));
        
        // Also write to a debug log file
        file_put_contents(__DIR__ . '/../flexible_debug.txt', 
            date('Y-m-d H:i:s') . " | POST data: " . json_encode($_POST) . "\n", 
            FILE_APPEND);

        try {
            // CRITICAL VALIDATION: Check if employee_id is empty BEFORE validation
            if (empty($employee_id) || $employee_id === '0' || $employee_id === 0) {
                throw new Exception("ERROR: No employee selected! Please select an employee from the dropdown. (Value received: '$employee_id')");
            }
            // Validation: Check required fields
            if (empty($employee_id)) {
                throw new Exception("Please select an employee from the dropdown.");
            }
            if (empty($date)) {
                throw new Exception("Date is required.");
            }
            if (empty($start_time)) {
                throw new Exception("Start time is required.");
            }
            if (empty($end_time)) {
                throw new Exception("End time is required.");
            }
            
            // Verify employee exists (handle both string and numeric IDs)
            $emp_check = $db->prepare("SELECT employee_id FROM employees WHERE employee_id = ? OR employee_id LIKE ?");
            $emp_check->execute([$employee_id, $employee_id]);
            if ($emp_check->rowCount() === 0) {
                throw new Exception("Selected employee does not exist in the system.");
            }
            
            // Insert the flexible schedule
            if (!empty($repeat_days)) {
                // Create weekly recurring schedules - one entry per day
                $baseDate = new DateTime($date);
                foreach ($repeat_days as $day) {
                    // Calculate the first occurrence of this day of week
                    $targetDate = clone $baseDate;
                    $currentDayOfWeek = (int)$targetDate->format('w');
                    $targetDay = (int)$day;
                    
                    // Adjust date to the target day of week
                    $daysUntilTarget = ($targetDay - $currentDayOfWeek + 7) % 7;
                    if ($daysUntilTarget == 0 && $targetDate < new DateTime()) {
                        $daysUntilTarget = 7;
                    }
                    $targetDate->modify("+{$daysUntilTarget} days");
                    
                    // Check if this schedule already exists
                    $check_sql = "SELECT id FROM ta_flexible_schedules WHERE employee_id = ? AND schedule_date = ? AND start_time = ? LIMIT 1";
                    $check_stmt = $db->prepare($check_sql);
                    $check_stmt->execute([$employee_id, $targetDate->format('Y-m-d'), $start_time]);
                    
                    if ($check_stmt->rowCount() === 0) {
                        $insert_sql = "INSERT INTO ta_flexible_schedules (employee_id, schedule_date, start_time, end_time, day_of_week, repeat_until, contract_end_date, notes, created_by)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $db->prepare($insert_sql);
                        $stmt->execute([$employee_id, $targetDate->format('Y-m-d'), $start_time, $end_time, $day, $repeat_until, $contract_end_date, $notes, $_SESSION['user_id'] ?? null]);
                        error_log("DEBUG: Inserted for Employee: $employee_id, Date: " . $targetDate->format('Y-m-d') . ", Day: $day");
                    } else {
                        error_log("DEBUG: Schedule already exists for Employee: $employee_id, Date: " . $targetDate->format('Y-m-d'));
                    }
                }
            } else {
                // Single date schedule - check if it already exists
                $check_sql = "SELECT id FROM ta_flexible_schedules WHERE employee_id = ? AND schedule_date = ? AND start_time = ? LIMIT 1";
                $check_stmt = $db->prepare($check_sql);
                $check_stmt->execute([$employee_id, $date, $start_time]);
                
                if ($check_stmt->rowCount() === 0) {
                    $insert_sql = "INSERT INTO ta_flexible_schedules (employee_id, schedule_date, start_time, end_time, contract_end_date, notes, created_by)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($insert_sql);
                    $stmt->execute([$employee_id, $date, $start_time, $end_time, $contract_end_date, $notes, $_SESSION['user_id'] ?? null]);
                } else {
                    throw new Exception("This schedule already exists for the selected employee on this date and time.");
                }
            }

            $message = 'Flexible schedule created successfully!';
            $action = 'overview';
            
            // Redirect to refresh the page and show updated data
            header('Location: ' . $_SERVER['PHP_SELF'] . '?action=overview&created=1');
            exit();
        } catch (Exception $e) {
            $error = 'Error creating flexible schedule: ' . $e->getMessage();
        }
    }

    if (isset($_POST['delete_flexible'])) {
        try {
            // Safety check: ensure we have a valid id
            if (empty($_POST['delete_flex_id'])) {
                throw new Exception("Invalid schedule ID. Cannot delete without a valid ID.");
            }
            
            $delete_id = (int)$_POST['delete_flex_id']; // Cast to int for safety
            
            if ($delete_id <= 0) {
                throw new Exception("Invalid schedule ID. ID must be a positive number.");
            }
            
            $delete_sql = "DELETE FROM ta_flexible_schedules WHERE id = ?";
            $stmt = $db->prepare($delete_sql);
            $stmt->execute([$delete_id]);
            
            if ($stmt->rowCount() > 1) {
                throw new Exception("Safety check failed: More than one record was deleted. Please contact support.");
            }
            
            $message = 'Flexible schedule deleted successfully!';
            $action = 'overview';
            
            // Redirect to refresh the page and show updated data
            header('Location: ' . $_SERVER['PHP_SELF'] . '?action=overview&deleted=1');
            exit();
        } catch (Exception $e) {
            $error = 'Error deleting flexible schedule: ' . $e->getMessage();
        }
    }

    if (isset($_POST['update_flexible'])) {
        // Handle flexible schedule update
        $flex_id = (int)($_POST['edit_flex_id'] ?? 0);
        $employee_id = $_POST['edit_flex_employee_id'] ?? '';
        $date = $_POST['edit_flex_date'] ?? '';
        $start_time = $_POST['edit_flex_start_time'] ?? '';
        $end_time = $_POST['edit_flex_end_time'] ?? '';
        $notes = $_POST['edit_flex_notes'] ?? '';
        $repeat_days = $_POST['edit_flex_days'] ?? [];
        $repeat_until = !empty($_POST['edit_flex_repeat_end_date']) ? $_POST['edit_flex_repeat_end_date'] : null;
        $contract_end_date = !empty($_POST['edit_flex_contract_end_date']) ? $_POST['edit_flex_contract_end_date'] : null;

        try {
            // CRITICAL VALIDATION: Check if employee_id is empty BEFORE validation
            if (empty($employee_id) || $employee_id === '0' || $employee_id === 0) {
                throw new Exception("ERROR: No employee selected! Please select an employee from the dropdown. (Value received: '$employee_id')");
            }
            // Validation: Check required fields
            if ($flex_id <= 0) {
                throw new Exception("Invalid schedule ID. Please try again.");
            }
            if (empty($employee_id)) {
                throw new Exception("Please select an employee from the dropdown.");
            }
            if (empty($date)) {
                throw new Exception("Date is required.");
            }
            if (empty($start_time)) {
                throw new Exception("Start time is required.");
            }
            if (empty($end_time)) {
                throw new Exception("End time is required.");
            }
            
            // Verify employee exists (handle both string and numeric IDs)
            $emp_check = $db->prepare("SELECT employee_id FROM employees WHERE employee_id = ? OR employee_id LIKE ?");
            $emp_check->execute([$employee_id, $employee_id]);
            if ($emp_check->rowCount() === 0) {
                throw new Exception("Selected employee does not exist in the system.");
            }
            
            // Get the current schedule to check what needs updating
            $current_sql = "SELECT * FROM ta_flexible_schedules WHERE id = ? LIMIT 1";
            $current_stmt = $db->prepare($current_sql);
            $current_stmt->execute([$flex_id]);
            $current = $current_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$current) {
                throw new Exception("Schedule not found.");
            }

            // Update the flexible schedule
            $update_sql = "UPDATE ta_flexible_schedules SET employee_id = ?, schedule_date = ?, start_time = ?, end_time = ?, repeat_until = ?, contract_end_date = ?, notes = ? WHERE id = ?";
            $stmt = $db->prepare($update_sql);
            $stmt->execute([$employee_id, $date, $start_time, $end_time, $repeat_until, $contract_end_date, $notes, $flex_id]);

            $message = 'Flexible schedule updated successfully!';
            $action = 'overview';
            
            // Redirect to refresh the page and show updated data
            header('Location: ' . $_SERVER['PHP_SELF'] . '?action=overview&updated=1');
            exit();
        } catch (Exception $e) {
            $error = 'Error updating flexible schedule: ' . $e->getMessage();
        }
    }
}


// Get data based on action
$shifts = $shiftController->getAllShifts();
$stats = $shiftController->getShiftStatistics();
$allAssignments = $shiftController->getEmployeesOnShift(null);

// For edit action, get specific shift
$editShift = null;
if ($action === 'edit' && isset($_GET['shift_id'])) {
    $editShift = $shiftController->getShiftById($_GET['shift_id']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed {
            margin-left: 0;
        }

        .shift-container {
            width: calc(100% - 250px);
            margin-left: 250px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            padding: 30px 20px;
            transition: width 0.3s ease, margin-left 0.3s ease;
        }

        body.sidebar-collapsed .shift-container {
            width: 100%;
            margin-left: 0;
        }

        .page-header {
            margin-bottom: 35px;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .page-title i {
            font-size: 36px;
            opacity: 0.95;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .page-title {
            color: #5fa3ff;
        }

        .shift-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body.dark-mode .shift-tabs {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .shift-tab {
            padding: 13px 24px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .shift-tab i {
            font-size: 17px;
        }

        .shift-tab:hover {
            background: #e8f1ff;
            color: #003d82;
            border-color: #003d82;
            transform: translateY(-1px);
        }

        .shift-tab.active {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
            transform: translateY(-2px);
        }

        body.dark-mode .shift-tab {
            background: #2a2a2a;
            color: #b0b0b0;
            border-color: rgba(95, 163, 255, 0.1);
        }

        body.dark-mode .shift-tab:hover {
            background: #333;
            color: #5fa3ff;
            border-color: #5fa3ff;
        }

        body.dark-mode .shift-tab.active {
            background: linear-gradient(135deg, #003d82, #005ba8);
            color: white;
            border-color: transparent;
        }

        /* Action Buttons for Modals */
        .shift-action-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body.dark-mode .shift-action-buttons {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .shift-action-buttons .btn {
            padding: 13px 24px;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 61, 130, 0.2);
        }

        .shift-action-buttons .btn i {
            font-size: 17px;
        }

        .shift-action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 61, 130, 0.4);
        }

        .shift-action-buttons .btn:active {
            transform: translateY(0);
        }

        body.dark-mode .shift-action-buttons .btn {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        body.dark-mode .shift-action-buttons .btn:hover {
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.5);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .shifts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
            margin-bottom: 35px;
        }

        .shift-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .shift-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .shift-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.05;
            transition: all 0.3s ease;
        }

        .shift-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 32px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.15);
        }

        .shift-card:hover::before {
            transform: scaleX(1);
        }

        .shift-card:hover::after {
            opacity: 0.08;
        }

        body.dark-mode .shift-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .shift-card:hover {
            box-shadow: 0 12px 32px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.2);
        }

        .shift-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .shift-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #003d82;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-card-title {
            color: #5fa3ff;
        }

        .shift-status {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            background: #d4edda;
            color: #155724;
            font-weight: 500;
        }

        .shift-status.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        body.dark-mode .shift-status {
            background: #1e5631;
        }

        body.dark-mode .shift-status.inactive {
            background: #5c2a2a;
        }

        .shift-time {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.dark-mode .shift-time {
            color: #b0b0b0;
        }

        .shift-details {
            margin-bottom: 12px;
        }

        .shift-details p {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }

        body.dark-mode .shift-details p {
            color: #888;
        }

        .shift-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .shift-card-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .shift-form {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            position: relative;
            overflow: hidden;
        }

        .shift-form::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(0, 61, 130, 0.05), rgba(0, 91, 168, 0.02));
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .shift-form:hover::before {
            opacity: 1;
        }

        body.dark-mode .shift-form {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #003d82;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        body.dark-mode .form-group label {
            color: #5fa3ff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid rgba(0, 61, 130, 0.1);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            color: #333;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background-color: #2a2a2a;
            border-color: #404040;
            color: #e0e0e0;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .form-buttons button {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #003d82;
            color: white;
        }

        .btn-primary:hover {
            background: #002a5a;
        }

        .btn-secondary {
            background: #e8eef7;
            color: #003d82;
        }

        .btn-secondary:hover {
            background: #d4dff0;
        }

        body.dark-mode .btn-secondary {
            background: #2a2a2a;
            color: #5fa3ff;
        }

        body.dark-mode .btn-secondary:hover {
            background: #333;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        body.dark-mode .alert-success {
            background: #1e5631;
            color: #81d97d;
            border-color: #2a7a38;
        }

        body.dark-mode .alert-danger {
            background: #5c2a2a;
            color: #f08080;
            border-color: #7a3838;
        }

        .shift-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.08;
            transition: all 0.3s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.2);
        }

        .stat-card:hover::before {
            opacity: 0.12;
        }

        .stat-card:hover::after {
            transform: scaleX(1);
        }

        body.dark-mode .stat-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e2a3a 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            border-color: rgba(95, 163, 255, 0.15);
        }

        body.dark-mode .stat-card:hover {
            box-shadow: 0 15px 40px rgba(95, 163, 255, 0.15);
            border-color: rgba(95, 163, 255, 0.3);
        }

        .stat-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 900;
            background: linear-gradient(135deg, #003d82, #005ba8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-number {
            background: linear-gradient(135deg, #5fa3ff, #7bb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 15px;
            color: #555;
            font-weight: 600;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }

        body.dark-mode .stat-label {
            color: #a0a0a0;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e8eef7;
            margin-bottom: 30px;
        }

        body.dark-mode .table-container {
            background: #1e1e1e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            border-color: #404040;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        body.dark-mode thead {
            background: #2a2a2a;
            border-bottom-color: #404040;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        body.dark-mode th {
            color: #e0e0e0;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #555;
        }

        body.dark-mode td {
            border-bottom-color: #404040;
            color: #b0b0b0;
        }

        tbody tr:hover {
            background: #f9fbfd;
        }

        body.dark-mode tbody tr:hover {
            background: #2a2a2a;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .shift-container {
                padding: 20px;
                margin-left: 0;
            }

            .page-title {
                font-size: 24px;
            }

            .shift-tabs {
                gap: 10px;
                padding: 12px;
            }

            .shift-tab {
                padding: 10px 16px;
                font-size: 12px;
            }

            .shift-action-buttons {
                gap: 8px;
                padding: 12px;
                flex-direction: column;
            }

            .shift-action-buttons .btn {
                width: 100%;
                justify-content: center;
                padding: 12px 16px;
                font-size: 14px;
            }

            .shift-form {
                padding: 20px;
            }

            .shifts-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .shift-stats {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .form-buttons {
                flex-direction: column;
            }

            .form-buttons button {
                width: 100%;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }

            .shift-card-actions {
                flex-direction: column;
            }
        }
    </style>
    <script src="../assets/mobile-responsive.js" defer></script>
</head>
<body>
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>
    <?php include(__DIR__ . '/../app/components/Sidebar.php'); ?>

    <div class="shift-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-clock"></i>
                Shift Management
            </h1>
            <p class="page-subtitle">
                <i class="fas fa-info-circle"></i> Create and manage shifts, assign employees, and view shift statistics
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <!-- Action Buttons for Modals -->
        <div class="shift-action-buttons">
            <button class="btn btn-primary" onclick="openModal('createShiftModal');">
                <i class="fas fa-plus-circle"></i>
                Create Shift
            </button>
            <button class="btn btn-primary" onclick="openModal('assignmentModal');">
                <i class="fas fa-user-check"></i>
                Assign Employee
            </button>
            <button class="btn btn-primary" onclick="openModal('flexibleModal');">
                <i class="fas fa-calendar-day"></i>
                Flexible Schedule
            </button>
        </div>

    <div id="overview" class="tab-content" style="display: block;">
        <h2 style="margin-bottom: 30px; font-size: 24px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-chart-bar"></i>
            Shift Management Overview
        </h2>
            
            <div class="shift-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number"><?php echo count($shifts); ?></div>
                    <div class="stat-label">Total Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number"><?php echo count(array_filter($shifts, fn($s) => $s['is_active'])); ?></div>
                    <div class="stat-label">Active Shifts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo count($allAssignments ?? []); ?></div>
                    <div class="stat-label">Total Assignments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-number">
                        <?php 
                        try {
                            $flex_count = $db->query("SELECT COUNT(*) as count FROM ta_flexible_schedules")->fetch(PDO::FETCH_ASSOC);
                            echo $flex_count['count'] ?? 0;
                        } catch (Exception $e) {
                            echo 0;
                        }
                        ?>
                    </div>
                    <div class="stat-label">Flexible Schedules</div>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-chart-bar"></i> Shift Breakdown
        </h3>
        <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-briefcase"></i> Shift Name</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-hourglass-half"></i> Break</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shifts as $shift): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($shift['shift_name']); ?></td>
                                <td><?php echo date('g:i A', strtotime($shift['start_time'])); ?> - <?php echo date('g:i A', strtotime($shift['end_time'])); ?></td>
                                <td><?php echo $shift['break_duration']; ?> minutes</td>
                                <td>
                                    <span class="shift-status <?php echo $shift['is_active'] ? '' : 'inactive'; ?>">
                                        <?php echo $shift['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td style="display: flex; gap: 8px;">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openEditShiftModal(<?php echo $shift['shift_id']; ?>, '<?php echo htmlspecialchars($shift['shift_name']); ?>', '<?php echo $shift['start_time']; ?>', '<?php echo $shift['end_time']; ?>', <?php echo $shift['break_duration']; ?>, '<?php echo htmlspecialchars($shift['description'] ?? ''); ?>', <?php echo $shift['is_active'] ? 'true' : 'false'; ?>);">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="shift_id" value="<?php echo $shift['shift_id']; ?>">
                                        <button type="submit" name="delete_shift" class="btn btn-sm btn-danger" onclick="return confirm('Delete this shift?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-user-check"></i> Shift Assignments
        </h3>
        
        <!-- Search and Controls -->
        <div style="margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <input type="text" id="assignmentSearch" placeholder="Search by employee or shift name..." style="width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px;" autocomplete="off">
                <div id="assignmentSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #e0e0e0; border-top: none; border-radius: 0 0 6px 6px; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
            <select id="assignmentSortBy" style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="employee">Sort by Employee</option>
                <option value="shift">Sort by Shift</option>
                <option value="date">Sort by Date</option>
                <option value="status">Sort by Status</option>
            </select>
            <button type="button" onclick="resetAssignmentFilters()" style="padding: 10px 20px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
        
        <div class="table-container">
            <table id="assignmentTable">
                <thead>
                    <tr>
                        <th style="cursor: pointer;" onclick="sortAssignments('employee')"><i class="fas fa-user"></i> Employee <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('shift')"><i class="fas fa-briefcase"></i> Shift <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('time')"><i class="fas fa-clock"></i> Time <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('from')"><i class="fas fa-calendar-alt"></i> From <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('to')"><i class="fas fa-calendar-check"></i> To <i class="fas fa-sort"></i></th>
                        <th style="cursor: pointer;" onclick="sortAssignments('status')"><i class="fas fa-info-circle"></i> Status <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody id="assignmentTableBody">
                    <?php
                    // Prepare data for JavaScript rendering
                    $assignmentData = [];
                    try {
                        // Get regular shift assignments
                        $query = "SELECT es.*, s.shift_name, s.start_time, s.end_time,
                                         e.full_name, e.department
                                  FROM ta_employee_shifts es
                                  INNER JOIN ta_shifts s ON es.shift_id = s.shift_id
                                  INNER JOIN employees e ON es.employee_id = e.employee_id
                                  WHERE es.is_active = 1
                                  ORDER BY s.shift_name, e.full_name";
                        
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($assignments)):
                            foreach ($assignments as $assign):
                                $isActive = strtotime($assign['effective_from']) <= time() && 
                                          (is_null($assign['effective_to']) || strtotime($assign['effective_to']) >= time());
                                
                                $assignmentData[] = [
                                    'employee' => $assign['full_name'],
                                    'shift' => $assign['shift_name'],
                                    'time' => date('g:i A', strtotime($assign['start_time'])) . ' - ' . date('g:i A', strtotime($assign['end_time'])),
                                    'startTime' => $assign['start_time'],
                                    'endTime' => $assign['end_time'],
                                    'from' => date('M d, Y', strtotime($assign['effective_from'])),
                                    'fromDate' => strtotime($assign['effective_from']),
                                    'to' => $assign['effective_to'] ? date('M d, Y', strtotime($assign['effective_to'])) : 'Ongoing',
                                    'toDate' => $assign['effective_to'] ? strtotime($assign['effective_to']) : 999999999999,
                                    'status' => $isActive ? 'Active' : 'Scheduled',
                                    'isActive' => $isActive
                                ];
                            endforeach;
                        endif;
                    } catch (Exception $e) {
                        error_log("ERROR: Failed to load assignments: " . $e->getMessage());
                    }
                    ?>
                </tbody>
            </table>
            <!-- Pagination for Assignments -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: white; border-radius: 10px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span id="assignmentInfo" style="font-size: 14px; color: #666;">Showing 0 of 0 records</span>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" onclick="previousAssignmentPage()" id="prevAssignBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <div id="assignmentPageNumbers" style="display: flex; gap: 5px;"></div>
                    <button type="button" onclick="nextAssignmentPage()" id="nextAssignBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="assignmentPerPage" style="font-size: 14px;">Records per page:</label>
                    <select id="assignmentPerPage" onchange="changeAssignmentPageSize()" style="padding: 6px 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <script>
        // Assignment Table Data
        let assignmentTableData = <?php echo json_encode($assignmentData); ?>;
        let assignmentCurrentPage = 1;
        let assignmentPageSize = 10;
        let assignmentSortField = 'employee';
        let assignmentSortAsc = true;
        let assignmentFilterText = '';

        function renderAssignmentTable() {
            const filtered = assignmentTableData.filter(row => {
                const searchTerm = assignmentFilterText.toLowerCase();
                return row.employee.toLowerCase().includes(searchTerm) || 
                       row.shift.toLowerCase().includes(searchTerm);
            });

            // Sort
            const sorted = [...filtered];
            sorted.sort((a, b) => {
                let aVal = a[assignmentSortField];
                let bVal = b[assignmentSortField];
                
                if (assignmentSortField === 'fromDate' || assignmentSortField === 'toDate') {
                    aVal = parseInt(aVal);
                    bVal = parseInt(bVal);
                }
                
                if (aVal < bVal) return assignmentSortAsc ? -1 : 1;
                if (aVal > bVal) return assignmentSortAsc ? 1 : -1;
                return 0;
            });

            const totalRecords = sorted.length;
            const totalPages = Math.ceil(totalRecords / assignmentPageSize);

            if (assignmentCurrentPage > totalPages && totalPages > 0) {
                assignmentCurrentPage = totalPages;
            }

            const start = (assignmentCurrentPage - 1) * assignmentPageSize;
            const end = start + assignmentPageSize;
            const pageData = sorted.slice(start, end);

            let html = '';
            if (pageData.length === 0) {
                html = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>No shift assignments found.</td></tr>';
            } else {
                pageData.forEach(row => {
                    html += `<tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-circle" style="font-size: 20px; color: #3498db;"></i>
                                <span>${row.employee}</span>
                            </div>
                        </td>
                        <td>
                            <span style="background-color: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 20px; font-weight: 500;">
                                ${row.shift}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-hourglass-start" style="color: #f39c12; margin-right: 4px;"></i>
                            ${row.time}
                        </td>
                        <td>
                            <i class="fas fa-calendar-plus" style="color: #27ae60; margin-right: 4px;"></i>
                            ${row.from}
                        </td>
                        <td>
                            <i class="fas fa-calendar-minus" style="color: #e74c3c; margin-right: 4px;"></i>
                            ${row.to}
                        </td>
                        <td>
                            <span style="background-color: ${row.isActive ? '#d4edda' : '#f8d7da'}; color: ${row.isActive ? '#155724' : '#721c24'}; padding: 4px 12px; border-radius: 4px; font-weight: 500; display: inline-block;">
                                <i class="fas fa-${row.isActive ? 'check-circle' : 'clock'}" style="margin-right: 4px;"></i>${row.status}
                            </span>
                        </td>
                    </tr>`;
                });
            }

            document.getElementById('assignmentTableBody').innerHTML = html;
            updateAssignmentPagination(totalRecords, totalPages);
        }

        function updateAssignmentPagination(total, pages) {
            document.getElementById('assignmentInfo').textContent = `Showing ${Math.min((assignmentCurrentPage - 1) * assignmentPageSize + 1, total)} to ${Math.min(assignmentCurrentPage * assignmentPageSize, total)} of ${total} records`;
            
            const pageNumbers = document.getElementById('assignmentPageNumbers');
            pageNumbers.innerHTML = '';
            for (let i = Math.max(1, assignmentCurrentPage - 2); i <= Math.min(pages, assignmentCurrentPage + 2); i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = i;
                btn.style.cssText = `padding: 6px 12px; border: 2px solid ${i === assignmentCurrentPage ? '#003d82' : '#ddd'}; background: ${i === assignmentCurrentPage ? '#003d82' : 'white'}; color: ${i === assignmentCurrentPage ? 'white' : '#333'}; border-radius: 6px; cursor: pointer; font-weight: ${i === assignmentCurrentPage ? '600' : '400'};`;
                btn.onclick = () => { assignmentCurrentPage = i; renderAssignmentTable(); };
                pageNumbers.appendChild(btn);
            }

            document.getElementById('prevAssignBtn').disabled = assignmentCurrentPage === 1;
            document.getElementById('nextAssignBtn').disabled = assignmentCurrentPage === pages || pages === 0;
        }

        function nextAssignmentPage() {
            const pages = Math.ceil(assignmentTableData.length / assignmentPageSize);
            if (assignmentCurrentPage < pages) assignmentCurrentPage++;
            renderAssignmentTable();
        }

        function previousAssignmentPage() {
            if (assignmentCurrentPage > 1) assignmentCurrentPage--;
            renderAssignmentTable();
        }

        function changeAssignmentPageSize() {
            assignmentPageSize = parseInt(document.getElementById('assignmentPerPage').value);
            assignmentCurrentPage = 1;
            renderAssignmentTable();
        }

        function sortAssignments(field) {
            if (assignmentSortField === field) {
                assignmentSortAsc = !assignmentSortAsc;
            } else {
                assignmentSortField = field;
                assignmentSortAsc = true;
            }
            assignmentCurrentPage = 1;
            renderAssignmentTable();
        }

        function resetAssignmentFilters() {
            assignmentFilterText = '';
            document.getElementById('assignmentSearch').value = '';
            assignmentCurrentPage = 1;
            assignmentSortField = 'employee';
            assignmentSortAsc = true;
            renderAssignmentTable();
        }

        // Search live filtering
        document.getElementById('assignmentSearch').addEventListener('keyup', function() {
            assignmentFilterText = this.value;
            assignmentCurrentPage = 1;
            showAssignmentSuggestions();
            renderAssignmentTable();
        });

        // Show suggestions for assignment search
        function showAssignmentSuggestions() {
            const searchBox = document.getElementById('assignmentSearch');
            const suggestionsBox = document.getElementById('assignmentSuggestions');
            const query = searchBox.value.toLowerCase().trim();

            if (query.length === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }

            const suggestions = new Set();
            assignmentTableData.forEach(row => {
                if (row.employee.toLowerCase().includes(query)) {
                    suggestions.add(row.employee);
                }
                if (row.shift.toLowerCase().includes(query)) {
                    suggestions.add(row.shift);
                }
            });

            if (suggestions.size === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }

            suggestionsBox.innerHTML = '';
            Array.from(suggestions).slice(0, 8).forEach(suggestion => {
                const item = document.createElement('div');
                item.style.cssText = 'padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;';
                item.innerHTML = `<i class="fas fa-search" style="color: #999; margin-right: 8px;"></i>${suggestion}`;
                item.onmouseover = () => item.style.background = '#f8f9fa';
                item.onmouseout = () => item.style.background = 'white';
                item.onclick = () => {
                    searchBox.value = suggestion;
                    assignmentFilterText = suggestion;
                    assignmentCurrentPage = 1;
                    renderAssignmentTable();
                    suggestionsBox.style.display = 'none';
                };
                suggestionsBox.appendChild(item);
            });

            suggestionsBox.style.display = 'block';
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#assignmentSearch') && !e.target.closest('#assignmentSuggestions')) {
                document.getElementById('assignmentSuggestions').style.display = 'none';
            }
        });

        // Initial render
        renderAssignmentTable();
        </script>

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-calendar-check"></i> Flexible Schedules
        </h3>
        
        <!-- Search and Controls for Flexible -->
        <div style="margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <input type="text" id="flexibleSearch" placeholder="Search by employee name or notes..." style="width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px;" autocomplete="off">
                <div id="flexibleSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 2px solid #e0e0e0; border-top: none; border-radius: 0 0 6px 6px; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
            <select id="flexibleSortBy" style="padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="employee">Sort by Employee</option>
                <option value="date">Sort by Date</option>
                <option value="time">Sort by Time</option>
            </select>
            <button type="button" onclick="resetFlexibleFilters()" style="padding: 10px 20px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
        
        <div class="table-container">
            <table id="flexibleTable">
                    <thead>
                        <tr>
                            <th style="cursor: pointer;" onclick="sortFlexible('employee')"><i class="fas fa-user"></i> Employee <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer;" onclick="sortFlexible('date')"><i class="fas fa-calendar"></i> Date <i class="fas fa-sort"></i></th>
                            <th><i class="fas fa-calendar-check"></i> Day(s)</th>
                            <th style="cursor: pointer;" onclick="sortFlexible('time')"><i class="fas fa-clock"></i> Time <i class="fas fa-sort"></i></th>
                            <th><i class="fas fa-repeat"></i> Repeat Until</th>
                            <th><i class="fas fa-calendar-times"></i> Contract End</th>
                            <th><i class="fas fa-sticky-note"></i> Notes</th>
                            <th><i class="fas fa-cog"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody id="flexibleTableBody">
                        <?php
                        // Prepare flexible schedule data
                        $flexibleData = [];
                        try {
                            $flex_query = "SELECT fs.id, fs.employee_id, fs.schedule_date, fs.start_time, fs.end_time, 
                                          fs.day_of_week, fs.repeat_until, fs.contract_end_date, fs.notes, fs.created_at,
                                          e.full_name
                                          FROM ta_flexible_schedules fs
                                          LEFT JOIN employees e ON fs.employee_id = e.employee_id
                                          ORDER BY fs.schedule_date DESC, fs.start_time ASC";
                            
                            $flex_stmt = $db->query($flex_query);
                            $flex_schedules = $flex_stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            if (!empty($flex_schedules)):
                                foreach ($flex_schedules as $flex):
                                    $day_text = $flex['day_of_week'] !== null ? $day_names[$flex['day_of_week']] . ' (Weekly)' : date('l', strtotime($flex['schedule_date']));
                                    $flexibleData[] = [
                                        'id' => $flex['id'],
                                        'employee_id' => $flex['employee_id'],
                                        'employee' => $flex['full_name'] ?? 'Unknown',
                                        'date' => date('M d, Y', strtotime($flex['schedule_date'])),
                                        'dateSort' => strtotime($flex['schedule_date']),
                                        'day' => $day_text,
                                        'time' => date('g:i A', strtotime($flex['start_time'])) . ' - ' . date('g:i A', strtotime($flex['end_time'])),
                                        'timeSort' => $flex['start_time'],
                                        'repeat' => $flex['repeat_until'] ? date('M d, Y', strtotime($flex['repeat_until'])) : '—',
                                        'contract' => $flex['contract_end_date'] ? date('M d, Y', strtotime($flex['contract_end_date'])) : '—',
                                        'notes' => $flex['notes'] ?? '',
                                        'schedule_date' => $flex['schedule_date'],
                                        'start_time' => $flex['start_time'],
                                        'end_time' => $flex['end_time'],
                                        'repeat_until' => $flex['repeat_until'],
                                        'contract_end_date' => $flex['contract_end_date']
                                    ];
                                endforeach;
                            endif;
                        } catch (Exception $e) {
                            error_log("ERROR: Flexible schedules query failed: " . $e->getMessage());
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Pagination for Flexible Schedules -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: white; border-radius: 10px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span id="flexibleInfo" style="font-size: 14px; color: #666;">Showing 0 of 0 records</span>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button type="button" onclick="previousFlexiblePage()" id="prevFlexBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <div id="flexiblePageNumbers" style="display: flex; gap: 5px;"></div>
                        <button type="button" onclick="nextFlexiblePage()" id="nextFlexBtn" style="padding: 8px 15px; background: #f0f0f0; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 500;">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="flexiblePerPage" style="font-size: 14px;">Records per page:</label>
                        <select id="flexiblePerPage" onchange="changeFlexiblePageSize()" style="padding: 6px 10px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <script>
            // Flexible Schedules Table Data
            let flexibleTableData = <?php echo json_encode($flexibleData); ?>;
            let flexibleCurrentPage = 1;
            let flexiblePageSize = 10;
            let flexibleSortField = 'employee';
            let flexibleSortAsc = true;
            let flexibleFilterText = '';

            function renderFlexibleTable() {
                const filtered = flexibleTableData.filter(row => {
                    const searchTerm = flexibleFilterText.toLowerCase();
                    return row.employee.toLowerCase().includes(searchTerm) || 
                           row.notes.toLowerCase().includes(searchTerm);
                });

                // Sort
                const sorted = [...filtered];
                sorted.sort((a, b) => {
                    let aVal = a[flexibleSortField];
                    let bVal = b[flexibleSortField];
                    
                    if (typeof aVal === 'number' && typeof bVal === 'number') {
                        return flexibleSortAsc ? aVal - bVal : bVal - aVal;
                    }
                    
                    if (aVal < bVal) return flexibleSortAsc ? -1 : 1;
                    if (aVal > bVal) return flexibleSortAsc ? 1 : -1;
                    return 0;
                });

                const totalRecords = sorted.length;
                const totalPages = Math.ceil(totalRecords / flexiblePageSize);

                if (flexibleCurrentPage > totalPages && totalPages > 0) {
                    flexibleCurrentPage = totalPages;
                }

                const start = (flexibleCurrentPage - 1) * flexiblePageSize;
                const end = start + flexiblePageSize;
                const pageData = sorted.slice(start, end);

                let html = '';
                if (pageData.length === 0) {
                    html = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>No flexible schedules found.</td></tr>';
                } else {
                    pageData.forEach(row => {
                        const notesPreview = row.notes.length > 30 ? row.notes.substring(0, 30) + '...' : row.notes || '—';
                        html += `<tr>
                            <td><i class="fas fa-user" style="margin-right: 8px; color: #3498db;"></i>${row.employee}</td>
                            <td>${row.date}</td>
                            <td>${row.day}</td>
                            <td><i class="fas fa-clock" style="margin-right: 4px; color: #f39c12;"></i>${row.time}</td>
                            <td>${row.repeat}</td>
                            <td>${row.contract}</td>
                            <td title="${row.notes}">${notesPreview}</td>
                            <td style="display: flex; gap: 8px;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openFlexibleScheduleEdit(${row.id}, '${row.employee_id}', '${row.schedule_date}', '${row.start_time}', '${row.end_time}', '${row.notes.replace(/'/g, "\\'")}', '${row.repeat_until || ''}', '${row.contract_end_date || ''}');" style="padding: 6px 12px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_flex_id" value="${row.id}">
                                    <button type="submit" name="delete_flexible" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                    });
                }

                document.getElementById('flexibleTableBody').innerHTML = html;
                updateFlexiblePagination(totalRecords, totalPages);
            }

            function updateFlexiblePagination(total, pages) {
                document.getElementById('flexibleInfo').textContent = `Showing ${Math.min((flexibleCurrentPage - 1) * flexiblePageSize + 1, total)} to ${Math.min(flexibleCurrentPage * flexiblePageSize, total)} of ${total} records`;
                
                const pageNumbers = document.getElementById('flexiblePageNumbers');
                pageNumbers.innerHTML = '';
                for (let i = Math.max(1, flexibleCurrentPage - 2); i <= Math.min(pages, flexibleCurrentPage + 2); i++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = i;
                    btn.style.cssText = `padding: 6px 12px; border: 2px solid ${i === flexibleCurrentPage ? '#003d82' : '#ddd'}; background: ${i === flexibleCurrentPage ? '#003d82' : 'white'}; color: ${i === flexibleCurrentPage ? 'white' : '#333'}; border-radius: 6px; cursor: pointer; font-weight: ${i === flexibleCurrentPage ? '600' : '400'};`;
                    btn.onclick = () => { flexibleCurrentPage = i; renderFlexibleTable(); };
                    pageNumbers.appendChild(btn);
                }

                document.getElementById('prevFlexBtn').disabled = flexibleCurrentPage === 1;
                document.getElementById('nextFlexBtn').disabled = flexibleCurrentPage === pages || pages === 0;
            }

            function nextFlexiblePage() {
                const pages = Math.ceil(flexibleTableData.length / flexiblePageSize);
                if (flexibleCurrentPage < pages) flexibleCurrentPage++;
                renderFlexibleTable();
            }

            function previousFlexiblePage() {
                if (flexibleCurrentPage > 1) flexibleCurrentPage--;
                renderFlexibleTable();
            }

            function changeFlexiblePageSize() {
                flexiblePageSize = parseInt(document.getElementById('flexiblePerPage').value);
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            }

            function sortFlexible(field) {
                if (flexibleSortField === field) {
                    flexibleSortAsc = !flexibleSortAsc;
                } else {
                    flexibleSortField = field;
                    flexibleSortAsc = true;
                }
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            }

            function resetFlexibleFilters() {
                flexibleFilterText = '';
                document.getElementById('flexibleSearch').value = '';
                flexibleCurrentPage = 1;
                flexibleSortField = 'employee';
                flexibleSortAsc = true;
                renderFlexibleTable();
            }

            // Search live filtering
            document.getElementById('flexibleSearch').addEventListener('keyup', function() {
                flexibleFilterText = this.value;
                flexibleCurrentPage = 1;
                showFlexibleSuggestions();
                renderFlexibleTable();
            });

            // Show suggestions for flexible search
            function showFlexibleSuggestions() {
                const searchBox = document.getElementById('flexibleSearch');
                const suggestionsBox = document.getElementById('flexibleSuggestions');
                const query = searchBox.value.toLowerCase().trim();

                if (query.length === 0) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                const suggestions = new Set();
                flexibleTableData.forEach(row => {
                    if (row.employee.toLowerCase().includes(query)) {
                        suggestions.add(row.employee);
                    }
                    if (row.notes.toLowerCase().includes(query)) {
                        suggestions.add(row.notes.substring(0, 50));
                    }
                });

                if (suggestions.size === 0) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                suggestionsBox.innerHTML = '';
                Array.from(suggestions).slice(0, 8).forEach(suggestion => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;';
                    item.innerHTML = `<i class="fas fa-search" style="color: #999; margin-right: 8px;"></i>${suggestion}`;
                    item.onmouseover = () => item.style.background = '#f8f9fa';
                    item.onmouseout = () => item.style.background = 'white';
                    item.onclick = () => {
                        searchBox.value = suggestion;
                        flexibleFilterText = suggestion;
                        flexibleCurrentPage = 1;
                        renderFlexibleTable();
                        suggestionsBox.style.display = 'none';
                    };
                    suggestionsBox.appendChild(item);
                });

                suggestionsBox.style.display = 'block';
            }

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#flexibleSearch') && !e.target.closest('#flexibleSuggestions')) {
                    document.getElementById('flexibleSuggestions').style.display = 'none';
                }
            });

            // Sort dropdown
            document.getElementById('assignmentSortBy').addEventListener('change', function() {
                const mapping = {
                    'employee': 'employee',
                    'shift': 'shift',
                    'date': 'fromDate',
                    'status': 'status'
                };
                assignmentSortField = mapping[this.value];
                assignmentCurrentPage = 1;
                renderAssignmentTable();
            });

            // Sort dropdown
            document.getElementById('flexibleSortBy').addEventListener('change', function() {
                const mapping = {
                    'employee': 'employee',
                    'date': 'dateSort',
                    'time': 'timeSort'
                };
                flexibleSortField = mapping[this.value];
                flexibleCurrentPage = 1;
                renderFlexibleTable();
            });

            // Initial render
            renderFlexibleTable();
            </script>

        <!-- Flexible Tab -->
        <div id="flexible" class="tab-content">
            <h2 style="margin-bottom: 30px; font-size: 24px; font-weight: 700; color: #2c3e50;">
                <i class="fas fa-calendar-day"></i> Flexible Schedules
            </h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Day(s)</th>
                            <th>Time</th>
                            <th>Repeat Until</th>
                            <th>Contract End Date</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $flex_query = "SELECT fs.id, fs.employee_id, fs.schedule_date, fs.start_time, fs.end_time, 
                                          fs.day_of_week, fs.repeat_until, fs.contract_end_date, fs.notes, fs.created_at,
                                          e.full_name
                                          FROM ta_flexible_schedules fs
                                          LEFT JOIN employees e ON fs.employee_id = e.employee_id
                                          ORDER BY fs.schedule_date DESC, fs.start_time ASC";
                            
                            $flex_stmt = $db->query($flex_query);
                            $flex_schedules = $flex_stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($flex_schedules)):
                                foreach ($flex_schedules as $flex):
                                    $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $day_text = $flex['day_of_week'] !== null ? $day_names[$flex['day_of_week']] . ' (Weekly)' : date('l', strtotime($flex['schedule_date']));
                        ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($flex['full_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($flex['schedule_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($day_text); ?></td>
                                        <td><?php echo date('g:i A', strtotime($flex['start_time'])); ?> - <?php echo date('g:i A', strtotime($flex['end_time'])); ?></td>
                                        <td><?php echo $flex['repeat_until'] ? date('M d, Y', strtotime($flex['repeat_until'])) : '—'; ?></td>
                                        <td><?php echo $flex['contract_end_date'] ? date('M d, Y', strtotime($flex['contract_end_date'])) : '—'; ?></td>
                                        <td><?php echo $flex['notes'] ? htmlspecialchars(substr($flex['notes'], 0, 50)) . (strlen($flex['notes']) > 50 ? '...' : '') : '—'; ?></td>
                                        <td style="display: flex; gap: 8px;">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openFlexibleScheduleEdit(<?php echo $flex['id']; ?>, '<?php echo $flex['employee_id']; ?>', '<?php echo $flex['schedule_date']; ?>', '<?php echo $flex['start_time']; ?>', '<?php echo $flex['end_time']; ?>', '<?php echo htmlspecialchars($flex['notes']); ?>', '<?php echo $flex['repeat_until'] ?? ''; ?>', '<?php echo $flex['contract_end_date'] ?? ''; ?>');">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="delete_flex_id" value="<?php echo $flex['id']; ?>">
                                                <button type="submit" name="delete_flexible" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-inbox"></i> No flexible schedules created yet. <a href="#" onclick="openModal('flexibleModal'); return false;" style="color: #0066cc; text-decoration: none;">Create one</a>
                                    </td>
                                </tr>
                        <?php
                            endif;
                        } catch (Exception $e) {
                        ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-inbox"></i> No flexible schedules created yet.
                                    </td>
                                </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        <h3 style="margin-top: 50px; font-size: 22px; font-weight: 700; color: #2c3e50;">
            <i class="fas fa-calendar-check"></i> Flexible Schedules
        </h3>
        <div class="table-container">
            <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Employee</th>
                            <th><i class="fas fa-briefcase"></i> Shift</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-calendar-alt"></i> From</th>
                            <th><i class="fas fa-calendar-check"></i> To</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $assignments = $shiftController->getEmployeesOnShift(null);
                            if (!empty($assignments)):
                                foreach ($assignments as $assign):
                                    $isActive = strtotime($assign['effective_from']) <= time() && 
                                              (is_null($assign['effective_to']) || strtotime($assign['effective_to']) >= time());
                        ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-user-circle" style="font-size: 20px; color: #3498db;"></i>
                                                <span><?php echo htmlspecialchars($assign['full_name']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="background-color: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 20px; font-weight: 500;">
                                                <?php echo htmlspecialchars($assign['shift_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-hourglass-start" style="color: #f39c12; margin-right: 4px;"></i>
                                            <?php echo date('g:i A', strtotime($assign['start_time'])); ?> - 
                                            <?php echo date('g:i A', strtotime($assign['end_time'])); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar-plus" style="color: #27ae60; margin-right: 4px;"></i>
                                            <?php echo date('M d, Y', strtotime($assign['effective_from'])); ?>
                                        </td>
                                        <td>
                                            <?php if ($assign['effective_to']): ?>
                                                <i class="fas fa-calendar-minus" style="color: #e74c3c; margin-right: 4px;"></i>
                                                <?php echo date('M d, Y', strtotime($assign['effective_to'])); ?>
                                            <?php else: ?>
                                                <span style="color: #999;">
                                                    <i class="fas fa-infinity" style="margin-right: 4px;"></i>Ongoing
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($isActive): ?>
                                                <span style="background-color: #d4edda; color: #155724; padding: 4px 12px; border-radius: 4px; font-weight: 500; display: inline-block;">
                                                    <i class="fas fa-check-circle" style="margin-right: 4px;"></i>Active
                                                </span>
                                            <?php else: ?>
                                                <span style="background-color: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 4px; font-weight: 500; display: inline-block;">
                                                    <i class="fas fa-clock" style="margin-right: 4px;"></i>Scheduled
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                        <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>
                                        No shift assignments yet. <a href="#" onclick="openModal('assignmentModal'); return false;" style="color: #0066cc; text-decoration: none; font-weight: 500;">Create one</a>
                                    </td>
                                </tr>
                        <?php
                            endif;
                        } catch (Exception $e) {
                            error_log("ERROR: Failed to load assignments: " . $e->getMessage());
                        ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px; color: #e74c3c;">
                                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>Error loading assignments
                                    </td>
                                </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    </div>

    <!-- MODALS -->
    <!-- Create Shift Modal -->
    <div id="createShiftModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Create New Shift</h2>
                <button class="modal-close" onclick="closeModal('createShiftModal')">&times;</button>
            </div>
            <form method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shift_name"><i class="fas fa-briefcase"></i> Shift Name *</label>
                        <input type="text" id="shift_name" name="shift_name" required placeholder="e.g., Morning Shift">
                    </div>
                    <div class="form-group">
                        <label for="start_time"><i class="fas fa-sign-in-alt"></i> Start Time *</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time"><i class="fas fa-sign-out-alt"></i> End Time *</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="break_duration"><i class="fas fa-hourglass-half"></i> Break Duration (minutes)</label>
                        <input type="number" id="break_duration" name="break_duration" min="0" max="480" value="60">
                    </div>
                    <div class="form-group">
                        <label for="description"><i class="fas fa-file-alt"></i> Description</label>
                        <textarea id="description" name="description" placeholder="Enter shift description (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="is_active" checked>
                            <span><i class="fas fa-check"></i> Active</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createShiftModal')">Cancel</button>
                    <button type="submit" name="create_shift" class="btn btn-primary"><i class="fas fa-save"></i> Create Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Shift Modal -->
    <div id="editShiftModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Shift</h2>
                <button class="modal-close" onclick="closeModal('editShiftModal')">&times;</button>
            </div>
            <form method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <input type="hidden" id="edit_shift_id" name="shift_id">
                    <div class="form-group">
                        <label for="edit_shift_name"><i class="fas fa-briefcase"></i> Shift Name *</label>
                        <input type="text" id="edit_shift_name" name="shift_name" required placeholder="e.g., Morning Shift">
                    </div>
                    <div class="form-group">
                        <label for="edit_start_time"><i class="fas fa-sign-in-alt"></i> Start Time *</label>
                        <input type="time" id="edit_start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_end_time"><i class="fas fa-sign-out-alt"></i> End Time *</label>
                        <input type="time" id="edit_end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_break_duration"><i class="fas fa-hourglass-half"></i> Break Duration (minutes)</label>
                        <input type="number" id="edit_break_duration" name="break_duration" min="0" max="480">
                    </div>
                    <div class="form-group">
                        <label for="edit_description"><i class="fas fa-file-alt"></i> Description</label>
                        <textarea id="edit_description" name="description" placeholder="Enter shift description (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" id="edit_is_active" name="is_active">
                            <span><i class="fas fa-check"></i> Active</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editShiftModal')">Cancel</button>
                    <button type="submit" name="update_shift" class="btn btn-primary"><i class="fas fa-save"></i> Update Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignment Modal -->
    <div id="assignmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-check"></i> Assign Shift to Employee</h2>
                <button class="modal-close" onclick="closeModal('assignmentModal')">&times;</button>
            </div>
            <form method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id"><i class="fas fa-user"></i> Employee *</label>
                        <select id="employee_id" name="employee_id" required>
                            <option value="">Select an employee...</option>
                            <?php
                            $stmt = $db->query("SELECT employee_id, full_name FROM employees ORDER BY full_name");
                            while ($emp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $emp['employee_id'] . '">' . htmlspecialchars($emp['full_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="shift_id"><i class="fas fa-briefcase"></i> Shift *</label>
                        <select id="shift_id" name="shift_id" required>
                            <option value="">Select a shift...</option>
                            <?php foreach ($shifts as $shift): ?>
                                <option value="<?php echo $shift['shift_id']; ?>">
                                    <?php echo htmlspecialchars($shift['shift_name']); ?> 
                                    (<?php echo date('g:i A', strtotime($shift['start_time'])); ?> - 
                                    <?php echo date('g:i A', strtotime($shift['end_time'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="effective_from"><i class="fas fa-calendar-check"></i> Effective From *</label>
                        <input type="date" id="effective_from" name="effective_from" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="effective_to"><i class="fas fa-calendar-times"></i> Effective To (Optional)</label>
                        <input type="date" id="effective_to" name="effective_to">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('assignmentModal')">Cancel</button>
                    <button type="submit" name="assign_shift" class="btn btn-primary"><i class="fas fa-check"></i> Assign Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Flexible Schedule Modal -->
    <div id="editFlexibleModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Flexible Schedule</h2>
                <button class="modal-close" onclick="closeModal('editFlexibleModal')">&times;</button>
            </div>
            <form method="POST" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <input type="hidden" id="edit_flex_id" name="edit_flex_id">
                    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> Update the schedule details for this employee.
                    </p>
                    <div class="form-group">
                        <label for="edit_flex_employee_id"><i class="fas fa-user"></i> Employee *</label>
                        <select id="edit_flex_employee_id" name="edit_flex_employee_id" required onchange="console.log('Edit - Employee selected:', this.value)">
                            <option value="">Select an employee...</option>
                            <?php
                            try {
                                $emp_stmt = $db->query("SELECT employee_id, full_name FROM employees ORDER BY full_name");
                                $emp_count = 0;
                                $emp_list = [];
                                while ($emp = $emp_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($emp['employee_id']) . '">' . htmlspecialchars($emp['full_name']) . '</option>';
                                    $emp_count++;
                                    $emp_list[] = $emp['employee_id'];
                                }
                                if ($emp_count === 0) {
                                    echo '<option value="" disabled style="color: red;">❌ No employees found in system</option>';
                                } else {
                                    echo '<script>console.log("✓ Edit form - Employees loaded: ' . implode(', ', $emp_list) . '");</script>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="" disabled>Error loading employees: ' . htmlspecialchars($e->getMessage()) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_flex_date"><i class="fas fa-calendar"></i> Date *</label>
                        <input type="date" id="edit_flex_date" name="edit_flex_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_flex_day_of_week"><i class="fas fa-calendar-week"></i> Repeat on Day of Week (Optional)</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="1"> Monday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="2"> Tuesday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="3"> Wednesday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="4"> Thursday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="5"> Friday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="edit_flex_days[]" value="6"> Saturday</label>
                        </div>
                        <p style="font-size: 12px; color: #999; margin-top: 8px;">Leave empty to assign only the selected date, or check days to repeat weekly</p>
                    </div>
                    <div class="form-group">
                        <label for="edit_flex_start_time"><i class="fas fa-clock"></i> Start Time *</label>
                        <input type="time" id="edit_flex_start_time" name="edit_flex_start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_flex_end_time"><i class="fas fa-clock"></i> End Time *</label>
                        <input type="time" id="edit_flex_end_time" name="edit_flex_end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_flex_notes"><i class="fas fa-sticky-note"></i> Notes (Optional)</label>
                        <textarea id="edit_flex_notes" name="edit_flex_notes" style="height: 100px; resize: vertical;"></textarea>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                            <input type="checkbox" name="edit_flex_repeat_until" id="edit_flex_repeat_until">
                            <span>Set Repeat End Date</span>
                        </label>
                    </div>
                    <div class="form-group" id="edit_flex_repeat_until_container" style="display: none;">
                        <label for="edit_flex_repeat_end_date"><i class="fas fa-calendar-times"></i> Repeat Until (Optional)</label>
                        <input type="date" id="edit_flex_repeat_end_date" name="edit_flex_repeat_end_date">
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                            <input type="checkbox" name="edit_flex_contract_end" id="edit_flex_contract_end">
                            <span>Set Contract End Date</span>
                        </label>
                    </div>
                    <div class="form-group" id="edit_flex_contract_end_container" style="display: none;">
                        <label for="edit_flex_contract_end_date"><i class="fas fa-briefcase"></i> Contract Ends On (Optional)</label>
                        <input type="date" id="edit_flex_contract_end_date" name="edit_flex_contract_end_date">
                        <p style="font-size: 12px; color: #999; margin-top: 8px;">Use this for temporary contracts with a specific end date</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editFlexibleModal')">Cancel</button>
                    <button type="submit" name="update_flexible" class="btn btn-primary"><i class="fas fa-save"></i> Update Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flexible Schedule Modal -->
    <div id="flexibleModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-day"></i> Create Flexible Schedule</h2>
                <button class="modal-close" onclick="closeModal('flexibleModal')">&times;</button>
            </div>
            <form method="POST" action="" class="shift-form" style="padding: 0;">
                <div class="modal-body">
                    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> Assign specific days and times for part-time or contract employees.
                    </p>
                    <div class="form-group">
                        <label for="flex_employee_id"><i class="fas fa-user"></i> Employee *</label>
                        <select id="flex_employee_id" name="flex_employee_id" required onchange="console.log('Employee selected:', this.value)">
                            <option value="">-- Select an employee --</option>
                            <?php
                            try {
                                $emp_stmt = $db->query("SELECT employee_id, full_name FROM employees ORDER BY full_name");
                                $emp_count = 0;
                                $emp_list = [];
                                while ($emp = $emp_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($emp['employee_id']) . '">' . htmlspecialchars($emp['full_name']) . '</option>';
                                    $emp_count++;
                                    $emp_list[] = $emp['employee_id'];
                                }
                                if ($emp_count === 0) {
                                    echo '<option value="" disabled style="color: red;">❌ No employees found in system</option>';
                                    echo '<script>console.error("WARNING: No employees in database. Employee dropdown is empty!");</script>';
                                } else {
                                    echo '<script>console.log("✓ Employees loaded: ' . implode(', ', $emp_list) . '");</script>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="" disabled>Error loading employees: ' . htmlspecialchars($e->getMessage()) . '</option>';
                                echo '<script>console.error("Employee query error:", ' . json_encode($e->getMessage()) . ');</script>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="flex_date"><i class="fas fa-calendar"></i> Date *</label>
                        <input type="date" id="flex_date" name="flex_date" required>
                    </div>
                    <div class="form-group">
                        <label for="flex_day_of_week"><i class="fas fa-calendar-week"></i> Repeat on Day of Week (Optional)</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="1"> Monday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="2"> Tuesday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="3"> Wednesday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="4"> Thursday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="5"> Friday</label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="flex_days[]" value="6"> Saturday</label>
                        </div>
                        <p style="font-size: 12px; color: #999; margin-top: 8px;">Leave empty to assign only the selected date, or check days to repeat weekly</p>
                    </div>
                    <div class="form-group">
                        <label for="flex_start_time"><i class="fas fa-clock"></i> Start Time *</label>
                        <input type="time" id="flex_start_time" name="flex_start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="flex_end_time"><i class="fas fa-clock"></i> End Time *</label>
                        <input type="time" id="flex_end_time" name="flex_end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="flex_notes"><i class="fas fa-sticky-note"></i> Notes (Optional)</label>
                        <textarea id="flex_notes" name="flex_notes" style="height: 100px; resize: vertical;"></textarea>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                            <input type="checkbox" name="flex_repeat_until" id="flex_repeat_until">
                            <span>Set Repeat End Date</span>
                        </label>
                    </div>
                    <div class="form-group" id="flex_repeat_until_container" style="display: none;">
                        <label for="flex_repeat_end_date"><i class="fas fa-calendar-times"></i> Repeat Until (Optional)</label>
                        <input type="date" id="flex_repeat_end_date" name="flex_repeat_end_date">
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                            <input type="checkbox" name="flex_contract_end" id="flex_contract_end">
                            <span>Set Contract End Date</span>
                        </label>
                    </div>
                    <div class="form-group" id="flex_contract_end_container" style="display: none;">
                        <label for="flex_contract_end_date"><i class="fas fa-briefcase"></i> Contract Ends On (Optional)</label>
                        <input type="date" id="flex_contract_end_date" name="flex_contract_end_date">
                        <p style="font-size: 12px; color: #999; margin-top: 8px;">Use this for temporary contracts with a specific end date</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('flexibleModal')">Cancel</button>
                    <button type="submit" name="create_flexible" class="btn btn-primary"><i class="fas fa-save"></i> Create Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 28px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: white;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            opacity: 0.8;
        }

        .modal-body {
            padding: 28px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding: 20px 28px;
            border-top: 1px solid #e0e0e0;
            background: #f8f9fa;
        }

        body.dark-mode .modal-content {
            background: #1e1e1e;
            color: #ffffff;
        }

        body.dark-mode .modal-header {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            border-bottom-color: #333;
        }

        body.dark-mode .modal-footer {
            background: #252525;
            border-top-color: #333;
        }

        body.dark-mode .modal-close {
            color: white;
        }

        /* Form styling within modals */
        .modal-body .form-group {
            margin-bottom: 20px;
        }

        .modal-body .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #2c3e50;
        }

        body.dark-mode .modal-body .form-group label {
            color: #e8e8e8;
        }

        .modal-body .form-group input[type="text"],
        .modal-body .form-group input[type="email"],
        .modal-body .form-group input[type="date"],
        .modal-body .form-group input[type="time"],
        .modal-body .form-group input[type="number"],
        .modal-body .form-group select,
        .modal-body .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .modal-body .form-group input:focus,
        .modal-body .form-group select:focus,
        .modal-body .form-group textarea:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        body.dark-mode .modal-body .form-group input,
        body.dark-mode .modal-body .form-group select,
        body.dark-mode .modal-body .form-group textarea {
            background: #2a2a2a;
            border-color: #444;
            color: #e8e8e8;
        }

        .modal-body .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal .btn {
            padding: 10px 24px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal .btn-primary {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
        }

        .modal .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
        }

        .modal .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .modal .btn-secondary:hover {
            background: #d0d0d0;
        }

        body.dark-mode .modal .btn-secondary {
            background: #444;
            color: #e8e8e8;
        }

        body.dark-mode .modal .btn-secondary:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .modal-content {
                max-width: 95%;
                max-height: 95vh;
            }

            .modal-header {
                padding: 16px;
            }

            .modal-header h2 {
                font-size: 16px;
            }

            .modal-body {
                padding: 16px;
            }

            .modal-footer {
                padding: 12px 16px;
                flex-direction: column;
                gap: 10px;
            }

            .modal .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                // Reset any forms inside the modal
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => form.reset());
            }
        }

        function openEditShiftModal(shiftId, shiftName, startTime, endTime, breakDuration, description, isActive) {
            // Populate the edit modal with current values
            document.getElementById('edit_shift_id').value = shiftId;
            document.getElementById('edit_shift_name').value = shiftName;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_end_time').value = endTime;
            document.getElementById('edit_break_duration').value = breakDuration;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_is_active').checked = isActive;
            
            // Open the modal
            openModal('editShiftModal');
        }

        function openFlexibleScheduleEdit(flexId, employeeId, date, startTime, endTime, notes, repeatUntil, contractEndDate) {
            // Populate the edit modal with current values
            document.getElementById('edit_flex_id').value = flexId;
            document.getElementById('edit_flex_employee_id').value = employeeId;
            document.getElementById('edit_flex_date').value = date;
            document.getElementById('edit_flex_start_time').value = startTime;
            document.getElementById('edit_flex_end_time').value = endTime;
            document.getElementById('edit_flex_notes').value = notes;
            
            // Handle repeat until date
            if (repeatUntil) {
                document.getElementById('edit_flex_repeat_until').checked = true;
                document.getElementById('edit_flex_repeat_end_date').value = repeatUntil;
                document.getElementById('edit_flex_repeat_until_container').style.display = 'block';
            } else {
                document.getElementById('edit_flex_repeat_until').checked = false;
                document.getElementById('edit_flex_repeat_end_date').value = '';
                document.getElementById('edit_flex_repeat_until_container').style.display = 'none';
            }
            
            // Handle contract end date
            if (contractEndDate) {
                document.getElementById('edit_flex_contract_end').checked = true;
                document.getElementById('edit_flex_contract_end_date').value = contractEndDate;
                document.getElementById('edit_flex_contract_end_container').style.display = 'block';
            } else {
                document.getElementById('edit_flex_contract_end').checked = false;
                document.getElementById('edit_flex_contract_end_date').value = '';
                document.getElementById('edit_flex_contract_end_container').style.display = 'none';
            }
            
            // Clear day selections
            document.querySelectorAll('input[name="edit_flex_days[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Open the edit modal
            openModal('editFlexibleModal');
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.shift-tab');
            buttons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }

            // Add active class to clicked button (only if event exists and has a target)
            if (event && event.target && event.target.classList) {
                event.target.classList.add('active');
            }
        }

        // Flexible Schedule: Toggle repeat end date field
        document.addEventListener('DOMContentLoaded', function() {
            const repeatUntilCheckbox = document.getElementById('flex_repeat_until');
            const repeatUntilContainer = document.getElementById('flex_repeat_until_container');
            const editRepeatUntilCheckbox = document.getElementById('edit_flex_repeat_until');
            const editRepeatUntilContainer = document.getElementById('edit_flex_repeat_until_container');
            
            const contractEndCheckbox = document.getElementById('flex_contract_end');
            const contractEndContainer = document.getElementById('flex_contract_end_container');
            const editContractEndCheckbox = document.getElementById('edit_flex_contract_end');
            const editContractEndContainer = document.getElementById('edit_flex_contract_end_container');

            if (repeatUntilCheckbox) {
                repeatUntilCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        repeatUntilContainer.style.display = 'block';
                        document.getElementById('flex_repeat_end_date').focus();
                    } else {
                        repeatUntilContainer.style.display = 'none';
                        document.getElementById('flex_repeat_end_date').value = '';
                    }
                });
            }

            if (editRepeatUntilCheckbox) {
                editRepeatUntilCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        editRepeatUntilContainer.style.display = 'block';
                        document.getElementById('edit_flex_repeat_end_date').focus();
                    } else {
                        editRepeatUntilContainer.style.display = 'none';
                        document.getElementById('edit_flex_repeat_end_date').value = '';
                    }
                });
            }
            
            if (contractEndCheckbox) {
                contractEndCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        contractEndContainer.style.display = 'block';
                        document.getElementById('flex_contract_end_date').focus();
                    } else {
                        contractEndContainer.style.display = 'none';
                        document.getElementById('flex_contract_end_date').value = '';
                    }
                });
            }

            if (editContractEndCheckbox) {
                editContractEndCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        editContractEndContainer.style.display = 'block';
                        document.getElementById('edit_flex_contract_end_date').focus();
                    } else {
                        editContractEndContainer.style.display = 'none';
                        document.getElementById('edit_flex_contract_end_date').value = '';
                    }
                });
            }

            // Set minimum date to today
            const dateInput = document.getElementById('flex_date');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
                dateInput.value = today;
            }

            const editDateInput = document.getElementById('edit_flex_date');
            if (editDateInput) {
                const today = new Date().toISOString().split('T')[0];
                editDateInput.setAttribute('min', today);
            }

            // Auto-switch to flexible tab if action is flexible
            const currentAction = '<?php echo $action; ?>';
            if (currentAction === 'flexible') {
                switchTab('flexible');
                const flexTab = document.querySelector('[onclick="switchTab(\'flexible\')"]');
                if (flexTab) {
                    flexTab.classList.add('active');
                }
            }
        });

        // Form validation for flexible schedule creation - using event delegation
        console.log('Script loaded - setting up form delegation');
        
        document.addEventListener('submit', function(e) {
            // Check if this is the flexible schedule form
            if (e.target && e.target.closest('#flexibleModal')) {
                console.log('✓ Flexible schedule form submission detected');
                const flexForm = e.target.closest('#flexibleModal form');
                const formData = new FormData(flexForm);
                
                console.log('=== FORM DATA BEING SUBMITTED ===');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }
                console.log('===================================');
                
                const employeeId = document.getElementById('flex_employee_id').value;
                const date = document.getElementById('flex_date').value;
                const startTime = document.getElementById('flex_start_time').value;
                const endTime = document.getElementById('flex_end_time').value;

                console.log('Direct element values:');
                console.log('  flex_employee_id field exists:', !!document.getElementById('flex_employee_id'));
                console.log('  flex_employee_id value:', employeeId, '| Type:', typeof employeeId, '| Length:', employeeId ? employeeId.length : 0);
                console.log('  flex_date value:', date);
                console.log('  flex_start_time value:', startTime);
                console.log('  flex_end_time value:', endTime);

                if (!employeeId || employeeId === '' || employeeId === '0') {
                    e.preventDefault();
                    console.error('❌ Employee validation failed! Employee ID is empty or 0');
                    alert('ERROR: Please select an employee from the dropdown before submitting!');
                    document.getElementById('flex_employee_id').focus();
                    return false;
                }
                if (!date) {
                    e.preventDefault();
                    alert('ERROR: Please select a date!');
                    return false;
                }
                if (!startTime) {
                    e.preventDefault();
                    alert('ERROR: Please select a start time!');
                    return false;
                }
                if (!endTime) {
                    e.preventDefault();
                    alert('ERROR: Please select an end time!');
                    return false;
                }
                console.log('✓ All validations passed. Form will submit with employee_id:', employeeId);
            }
        });
    </script>
</body>
</html>
