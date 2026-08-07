<?php
// IMMEDIATE DEBUG - Log the moment this file is accessed
error_log("teacherLoadHandler.php was called");
file_put_contents(__DIR__ . '/handler_called.txt', date('Y-m-d H:i:s') . " - Handler called\nGET:" . json_encode($_GET) . "\nPOST: " . json_encode($_POST) . "\n", FILE_APPEND);

// Create a detailed log of every action
$logFile = __DIR__ . '/teacherLoadHandler.log';
function logMessage($msg)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $msg\n", FILE_APPEND);
}

logMessage("=== REQUEST STARTED ===");
logMessage("Method: " . $_SERVER['REQUEST_METHOD']);
logMessage("POST: " . json_encode($_POST));
logMessage("SESSION: " . json_encode($_SESSION ?? []));

require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../../auth/auth_check.php';

logMessage("Auth files loaded");

try {
    $db = Database::getInstance()->getConnection();
    logMessage("Database connected");

    $action = $_POST['action'] ?? null;
    logMessage("Action: $action");

    switch ($action) {
        case 'add_load':
            logMessage("Processing ADD_LOAD");
            $employeeId = (int)($_POST['employee_id'] ?? 0);
            $academicYear = $_POST['academic_year'] ?? '';
            $semester = $_POST['semester'] ?? '';
            $totalUnits = (float)($_POST['total_units'] ?? 0);

            logMessage("Parsed: empId=$employeeId, year=$academicYear, sem=$semester, units=$totalUnits");

            // Validate inputs (qualification now sourced from employees table)
            if (!$employeeId || !$academicYear || !$semester || $totalUnits <= 0) {
                logMessage("VALIDATION FAILED");
                header('Location: teacherLoadManagement.php?error=Missing required fields');
                exit;
            }

            logMessage("Checking for duplicate combination...");

            // First check if this combination already exists
            $checkStmt = $db->prepare("
                SELECT id FROM pr_teacher_loads 
                WHERE employee_id = ? AND academic_year = ? AND semester = ?
            ");
            $checkStmt->execute([$employeeId, $academicYear, $semester]);

            if ($checkStmt->rowCount() > 0) {
                logMessage("DUPLICATE FOUND - Cannot add same employee/year/semester combination");
                header('Location: teacherLoadManagement.php?error=This teacher already has a load assigned for ' . htmlspecialchars($academicYear) . ' ' . htmlspecialchars($semester));
                exit;
            }

            logMessage("No duplicate - proceeding with INSERT");

            // Insert teacher load (no qualification field - sourced from employees table)
            $stmt = $db->prepare("
                INSERT INTO pr_teacher_loads 
                (employee_id, academic_year, semester, total_units, created_by)
                VALUES (:eid, :year, :sem, :units, :creator)
            ");

            logMessage("Statement prepared, executing...");

            $result = $stmt->execute([
                ':eid' => $employeeId,
                ':year' => $academicYear,
                ':sem' => $semester,
                ':units' => $totalUnits,
                ':creator' => $_SESSION['user']['name'] ?? 'system'
            ]);

            logMessage("Execute returned: " . ($result ? "TRUE" : "FALSE"));
            logMessage("Rows affected: " . $stmt->rowCount());
            logMessage("PDO errorInfo: " . json_encode($stmt->errorInfo()));

            if ($result && $stmt->rowCount() > 0) {
                logMessage("SUCCESS - Redirecting to success page");
                header('Location: teacherLoadManagement.php?success=added');
                exit;
            } else {
                $errors = $stmt->errorInfo();
                logMessage("FAILED - Error: " . json_encode($errors));
                header('Location: teacherLoadManagement.php?error=' . urlencode($errors[2] ?? 'Unknown error'));
                exit;
            }
            break;

        case 'delete_load':
            logMessage("Processing DELETE_LOAD");
            $loadId = (int)($_POST['load_id'] ?? 0);
            logMessage("Load ID: $loadId");

            if (!$loadId) {
                logMessage("Invalid load ID");
                header('Location: teacherLoadManagement.php?error=Invalid load ID');
                exit;
            }

            $stmt = $db->prepare("DELETE FROM pr_teacher_loads WHERE id = :id");
            $result = $stmt->execute([':id' => $loadId]);

            logMessage("Delete result: " . ($result ? "OK - " . $stmt->rowCount() . " rows" : "FAILED"));

            if ($result) {
                header('Location: teacherLoadManagement.php?success=deleted');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to delete');
            }
            exit;
            break;

        case 'approve_load':
            logMessage("Processing APPROVE_LOAD");
            $loadId = (int)($_POST['load_id'] ?? 0);
            logMessage("Load ID: $loadId");

            if (!$loadId) {
                logMessage("Invalid load ID");
                header('Location: teacherLoadManagement.php?error=Invalid load ID');
                exit;
            }

            $stmt = $db->prepare("
                UPDATE pr_teacher_loads 
                SET approved_by = :approver,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $result = $stmt->execute([
                ':id' => $loadId,
                ':approver' => $_SESSION['user']['name'] ?? 'system'
            ]);

            logMessage("Approve result: " . ($result ? "OK - " . $stmt->rowCount() . " rows" : "FAILED"));

            if ($result) {
                header('Location: teacherLoadManagement.php?success=approved');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to approve');
            }
            exit;
            break;

        default:
            logMessage("Invalid action: $action");
            header('Location: teacherLoadManagement.php?error=Invalid action');
            exit;
    }
} catch (Exception $e) {
    logMessage("EXCEPTION: " . $e->getMessage());
    logMessage("Trace: " . $e->getTraceAsString());
    header('Location: teacherLoadManagement.php?error=' . urlencode($e->getMessage()));
    exit;
}

logMessage("=== REQUEST ENDED ===");
