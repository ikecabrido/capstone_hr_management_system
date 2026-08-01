<?php
/**
 * Sync employees from employees table to pr_employee_details
 * This script populates payroll data for new employees
 */

require_once "../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    // Map position types to POSITION_CONFIG categories with sample base salaries
    $positionMapping = [
        // Corporate/IT Positions
        'Software Engineer' => ['category' => 'professional', 'base_salary' => 35000],
        'Software Developer' => ['category' => 'professional', 'base_salary' => 35000],
        'Junior Developer' => ['category' => 'professional', 'base_salary' => 25000],
        
        // HR Positions
        'HR Manager' => ['category' => 'admin', 'base_salary' => 30000],
        'HR Specialist' => ['category' => 'admin', 'base_salary' => 25000],
        
        // Finance Positions
        'Accountant' => ['category' => 'professional', 'base_salary' => 28000],
        'Financial Analyst' => ['category' => 'professional', 'base_salary' => 30000],
        
        // Operations Positions
        'Operations Manager' => ['category' => 'admin', 'base_salary' => 32000],
        'Staff Coordinator' => ['category' => 'admin', 'base_salary' => 22000],
        'Administrative Officer' => ['category' => 'admin', 'base_salary' => 22000],
        
        // Academic/Teaching Positions (Teacher category)
        'Teacher' => ['category' => 'teacher', 'base_salary' => 30000],
        'Professor' => ['category' => 'teacher', 'base_salary' => 40000],
        'Associate Professor' => ['category' => 'teacher', 'base_salary' => 35000],
        'Instructor' => ['category' => 'teacher', 'base_salary' => 28000],
        
        // Support Positions
        'Janitor' => ['category' => 'support', 'base_salary' => 15000],
        'Maintenance Worker' => ['category' => 'support', 'base_salary' => 18000],
    ];

    // Get all active employees that don't have payroll details
    $stmt = $db->prepare("
        SELECT e.employee_id, e.position, e.full_name
        FROM employees e
        LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
        WHERE pd.employee_id IS NULL
        AND e.employment_status = 'Active'
        ORDER BY e.employee_id
    ");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($employees) . " employees without payroll details\n\n";

    if (count($employees) === 0) {
        echo "All employees already have payroll details configured.\n";
        exit(0);
    }

    $inserted = 0;
    $skipped = 0;

    // Insert missing employees into pr_employee_details
    foreach ($employees as $emp) {
        $position = $emp['position'];
        
        // Get mapping for this position
        $mapping = $positionMapping[$position] ?? null;
        
        if (!$mapping) {
            echo "⚠️  SKIPPED: {$emp['full_name']} (Position: {$position}) - No mapping found\n";
            $skipped++;
            continue;
        }

        // Insert into pr_employee_details
        $insertStmt = $db->prepare("
            INSERT INTO pr_employee_details (employee_id, base_salary, position_type)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                base_salary = VALUES(base_salary),
                position_type = VALUES(position_type)
        ");

        $insertStmt->execute([
            $emp['employee_id'],
            $mapping['base_salary'],
            ucfirst($mapping['category'])
        ]);

        echo "✅ INSERTED: {$emp['full_name']} - Position: {$position} (Category: {$mapping['category']}, Base: ₱{$mapping['base_salary']})\n";
        $inserted++;

        // Also insert or update benefits record
        $benefitsStmt = $db->prepare("
            INSERT INTO pr_employee_benefits (employee_id, has_sss, has_philhealth, has_pagibig)
            VALUES (?, 1, 1, 1)
            ON DUPLICATE KEY UPDATE
                has_sss = 1,
                has_philhealth = 1,
                has_pagibig = 1
        ");
        $benefitsStmt->execute([$emp['employee_id']]);
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Summary:\n";
    echo "  ✅ Inserted: $inserted\n";
    echo "  ⚠️  Skipped: $skipped\n";
    echo "  📊 Total Processed: " . ($inserted + $skipped) . "\n";
    echo str_repeat("=", 60) . "\n\n";

    // Display created records
    $stmt = $db->prepare("
        SELECT 
            e.employee_id,
            e.full_name,
            e.position,
            pd.base_salary,
            pd.position_type
        FROM employees e
        JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
        WHERE e.employment_status = 'Active'
        ORDER BY e.employee_id
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "📋 Payroll Configuration (All Active Employees):\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-4s %-30s %-25s %-15s %-12s\n", "ID", "Name", "Position", "Category", "Base Salary");
    echo str_repeat("-", 80) . "\n";

    foreach ($results as $row) {
        printf("%-4s %-30s %-25s %-15s ₱%-11s\n",
            $row['employee_id'],
            substr($row['full_name'], 0, 28),
            substr($row['position'], 0, 23),
            ucfirst($row['position_type']),
            number_format($row['base_salary'], 2)
        );
    }
    echo str_repeat("-", 80) . "\n";

    echo "\n✅ Payroll synchronization complete! The payroll processing page should now work.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
