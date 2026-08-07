<?php
// Debug version of teacherLoadHandler to log all activity

// Log function
function debug_log($message) {
    $log_file = __DIR__ . '/../../teacherLoadHandler.debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " | " . $message . "\n", FILE_APPEND);
}

debug_log("=== Handler called ===");
debug_log("METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
debug_log("POST data: " . json_encode($_POST));

require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../../auth/auth_check.php';

try {
    debug_log("Auth includes loaded");
    $db = Database::getInstance()->getConnection();
    debug_log("Database connection established");
    
    $action = $_POST['action'] ?? null;
    debug_log("Action: " . $action);

    switch ($action) {
        case 'add_load':
            debug_log("Processing add_load");
            $employeeId = (int)($_POST['employee_id'] ?? 0);
            $academicYear = $_POST['academic_year'] ?? '';
            $semester = $_POST['semester'] ?? '';
            $totalUnits = (float)($_POST['total_units'] ?? 0);

            debug_log("Parsed: empId=$employeeId, year=$academicYear, sem=$semester, units=$totalUnits");

            // Validate inputs (qualification now sourced from employees table)
            if (!$employeeId || !$academicYear || !$semester || $totalUnits <= 0) {
                debug_log("Validation FAILED");
                die("Validation failed: emp=$employeeId, year=$academicYear, sem=$semester, units=$totalUnits");
            }

            debug_log("Validation PASSED");

            // Insert teacher load (no qualification field - sourced from employees table)
            $stmt = $db->prepare("
                INSERT INTO pr_teacher_loads 
                (employee_id, academic_year, semester, total_units, created_by)
                VALUES (:eid, :year, :sem, :units, :creator)
                ON DUPLICATE KEY UPDATE
                total_units = :units,
                updated_at = CURRENT_TIMESTAMP
            ");

            try {
                debug_log("Executing INSERT");
                $result = $stmt->execute([
                    ':eid' => $employeeId,
                    ':year' => $academicYear,
                    ':sem' => $semester,
                    ':units' => $totalUnits,
                    ':creator' => $_SESSION['user']['name'] ?? 'system'
                ]);

                debug_log("INSERT execute returned: " . ($result ? "TRUE" : "FALSE"));
                debug_log("Rows affected: " . $stmt->rowCount());

                if ($result) {
                    debug_log("INSERT successful, redirecting");
                    echo "✓ Record inserted successfully! Redirecting...";
                    header('Location: teacherLoadManagement.php?success=added');
                    exit;
                } else {
                    $errors = $stmt->errorInfo();
                    debug_log("INSERT failed: " . json_encode($errors));
                    echo "INSERT Failed: " . json_encode($errors) . "\n";
                    echo "Error Detail: " . $errors[2];
                    die();
                }
            } catch (Exception $insertEx) {
                debug_log("Exception during INSERT: " . $insertEx->getMessage());
                echo "Exception during INSERT: " . $insertEx->getMessage();
                die();
            }
            break;

        case 'delete_load':
            debug_log("Processing delete_load");
            $loadId = (int)($_POST['load_id'] ?? 0);

            if (!$loadId) {
                debug_log("Delete failed: no loadId");
                header('Location: teacherLoadManagement.php?error=Invalid load ID');
                exit;
            }

            $stmt = $db->prepare("DELETE FROM pr_teacher_loads WHERE id = :id");
            $result = $stmt->execute([':id' => $loadId]);
            debug_log("Delete result: " . ($result ? "OK" : "FAILED"));

            if ($result) {
                header('Location: teacherLoadManagement.php?success=deleted');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to delete');
            }
            break;

        case 'approve_load':
            debug_log("Processing approve_load");
            $loadId = (int)($_POST['load_id'] ?? 0);

            if (!$loadId) {
                debug_log("Approve failed: no loadId");
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

            debug_log("Approve result: " . ($result ? "OK" : "FAILED"));

            if ($result) {
                header('Location: teacherLoadManagement.php?success=approved');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to approve');
            }
            break;

        default:
            debug_log("Invalid action: $action");
            die("Invalid action: $action");
    }
} catch (Exception $e) {
    debug_log("Exception: " . $e->getMessage());
    die("Exception: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
}
?>
