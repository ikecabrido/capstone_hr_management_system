<?php
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../../auth/auth_check.php';


try {
    $db = Database::getInstance()->getConnection();
    $action = $_POST['action'] ?? null;

    switch ($action) {
        case 'add_load':
            $employeeId = (int)($_POST['employee_id'] ?? 0);
            $academicYear = $_POST['academic_year'] ?? '';
            $semester = $_POST['semester'] ?? '';
            $totalUnits = (float)($_POST['total_units'] ?? 0);

            // Validate inputs (qualification now sourced from employees table)
            if (!$employeeId || !$academicYear || !$semester || $totalUnits <= 0) {
                die("Validation failed: emp=$employeeId, year=$academicYear, sem=$semester, units=$totalUnits");
            }

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
                $result = $stmt->execute([
                    ':eid' => $employeeId,
                    ':year' => $academicYear,
                    ':sem' => $semester,
                    ':units' => $totalUnits,
                    ':creator' => $_SESSION['user']['name'] ?? 'system'
                ]);

                if ($result) {
                    echo "✓ Record inserted successfully! Redirecting...";
                    header('Location: teacherLoadManagement.php?success=added');
                    exit;
                } else {
                    $errors = $stmt->errorInfo();
                    echo "INSERT Failed: " . json_encode($errors) . "\n";
                    echo "Error Detail: " . $errors[2];
                    die();
                }
            } catch (Exception $insertEx) {
                echo "Exception during INSERT: " . $insertEx->getMessage();
                die();
            }
            break;

        case 'delete_load':
            $loadId = (int)($_POST['load_id'] ?? 0);

            if (!$loadId) {
                header('Location: teacherLoadManagement.php?error=Invalid load ID');
                exit;
            }

            $stmt = $db->prepare("DELETE FROM pr_teacher_loads WHERE id = :id");
            $result = $stmt->execute([':id' => $loadId]);

            if ($result) {
                header('Location: teacherLoadManagement.php?success=deleted');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to delete');
            }
            break;

        case 'approve_load':
            $loadId = (int)($_POST['load_id'] ?? 0);

            if (!$loadId) {
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

            if ($result) {
                header('Location: teacherLoadManagement.php?success=approved');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to approve');
            }
            break;

        default:
            die("Invalid action: $action");
    }
} catch (Exception $e) {
    die("Exception: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
}
