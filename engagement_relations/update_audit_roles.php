<?php
/**
 * One-off script to migrate existing audit_logs LOGIN entries
 * so that their target_type matches the user's role (admin/hr/employee).
 *
 * Usage (CLI):
 *   php update_audit_roles.php
 */

require_once __DIR__ . '/config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $selectStmt = $pdo->query("SELECT id, performed_by, target_type FROM audit_logs WHERE action = 'LOGIN' AND (target_type = 'employees' OR target_type IS NULL OR target_type = 'employee')");
    $rows = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "No LOGIN audit rows requiring update were found.\n";
        exit(0);
    }

    $empStmt = $pdo->prepare('SELECT role FROM employees WHERE id = ? LIMIT 1');
    $updateStmt = $pdo->prepare('UPDATE audit_logs SET target_type = ? WHERE id = ?');

    $updated = 0;
    foreach ($rows as $r) {
        $performedBy = $r['performed_by'];
        $auditId = $r['id'];

        $empStmt->execute([$performedBy]);
        $emp = $empStmt->fetch(PDO::FETCH_ASSOC);

        $newType = 'employee';
        if ($emp && !empty($emp['role'])) {
            $newType = $emp['role'];
        }

        $updateStmt->execute([$newType, $auditId]);
        $updated++;
        echo "Updated audit_log id={$auditId} performed_by={$performedBy} -> target_type={$newType}\n";
    }

    echo "Migration complete. Total updated: {$updated}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
