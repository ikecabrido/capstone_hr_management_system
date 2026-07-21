<?php
/**
 * Simple Time & Attendance Dashboard View
 * This file is included by the layout - keep it minimal
 */

// Prevent direct access
if (!isset($employee_id)) {
    echo "Unauthorized access";
    exit;
}

?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Time & Attendance Dashboard</h3>
            </div>
            <div class="card-body">
                <p>Welcome, <?php echo htmlspecialchars($employee['full_name'] ?? 'Employee'); ?></p>
                <p>Employee ID: <?php echo htmlspecialchars($employee_id); ?></p>
                <p>Department: <?php echo htmlspecialchars($employee['department'] ?? 'N/A'); ?></p>
                <p>Position: <?php echo htmlspecialchars($employee['position'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>
</div>
