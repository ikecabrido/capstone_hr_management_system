<?php
/**
 * Migration Script: Migrate existing archived resignations to exit_archive table
 *
 * This script moves resignations with status='archived' from exit_resignations
 * to the exit_archive table, then deletes them from exit_resignations.
 *
 * Usage: Run this script once to migrate existing archived data.
 */

session_start();
require_once "../auth/auth_check.php";
require_once "models/ResignationModel.php";

// Verify user is logged in (any role can run migrations)
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
    http_response_code(403);
    echo "<p>User not logged in or role not set</p>";
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

    // Get all archived resignations
    $stmt = $db->query("SELECT * FROM exit_resignations WHERE status = 'archived'");
    $archivedResignations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $migrated = 0;
    $errors = [];

    foreach ($archivedResignations as $resignation) {
        try {
            // Insert into exit_archive
            $archiveStmt = $db->prepare("
                INSERT INTO exit_archive (
                    archive_type, original_id, employee_id, title, description, content,
                    status, original_created_by, archived_by, archive_reason, archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $title = "Resignation - Employee " . ($resignation['employee_id'] ?? 'Unknown');
            $description = "Migrated archived resignation record";
            $content = json_encode($resignation);
            $archivedBy = $_SESSION['user']['id'] ?? 1;

            $archiveStmt->execute([
                'resignation',
                $resignation['id'],
                $resignation['employee_id'],
                $title,
                $description,
                $content,
                $resignation['status'],
                $resignation['created_by'],
                $archivedBy,
                'System migration',
                $content
            ]);

            // Delete from exit_resignations
            $deleteStmt = $db->prepare("DELETE FROM exit_resignations WHERE id = ?");
            $deleteStmt->execute([$resignation['id']]);

            $migrated++;
            echo "✓ Migrated Resignation ID {$resignation['id']}: Employee {$resignation['employee_id']}<br>";
        } catch (Exception $e) {
            $errors[] = "Failed to migrate Resignation ID {$resignation['id']}: " . $e->getMessage();
        }
    }

    $db->commit();

    // Summary
    echo "<hr>";
    echo "<h3>Migration Summary</h3>";
    echo "<p><strong>Total Archived Resignations Migrated:</strong> {$migrated}</p>";

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
?></content>
<parameter name="filePath">c:\xampp\htdocs\capstone_hr_management_system\exit_management\migrate_archived_resignations.php