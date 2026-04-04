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
            $qualification = $_POST['qualification'] ?? '';
            $totalUnits = (float)($_POST['total_units'] ?? 0);

            // Validate inputs
            if (!$employeeId || !$academicYear || !$semester || !$qualification || $totalUnits <= 0) {
                header('Location: teacherLoadManagement.php?error=Missing required fields');
                exit;
            }

            // Insert teacher load
            $stmt = $db->prepare("
                INSERT INTO pr_teacher_loads 
                (employee_id, academic_year, semester, qualification, total_units, created_by)
                VALUES (:eid, :year, :sem, :qual, :units, :creator)
                ON DUPLICATE KEY UPDATE
                qualification = :qual,
                total_units = :units,
                updated_at = CURRENT_TIMESTAMP
            ");

            $result = $stmt->execute([
                ':eid' => $employeeId,
                ':year' => $academicYear,
                ':sem' => $semester,
                ':qual' => $qualification,
                ':units' => $totalUnits,
                ':creator' => $_SESSION['user']['name'] ?? 'system'
            ]);

            if ($result) {
                header('Location: teacherLoadManagement.php?success=added');
            } else {
                header('Location: teacherLoadManagement.php?error=Failed to save');
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
            header('Location: teacherLoadManagement.php?error=Invalid action');
    }
} catch (Exception $e) {
    header('Location: teacherLoadManagement.php?error=' . urlencode($e->getMessage()));
}
