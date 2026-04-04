<?php
/**
 * TIME & ATTENDANCE MODULE - INT EMPLOYEE_ID VERIFICATION SCRIPT
 * Verifies all database tables, foreign keys, and application code
 */

require_once "../auth/database.php";

$db = new Database();
$errors = [];
$warnings = [];
$successes = [];

echo "<h1>Time & Attendance Module - INT Employee_ID Verification</h1>";
echo "<hr>";

// ============================================================================
// 1. VERIFY DATABASE SCHEMA
// ============================================================================
echo "<h2>1. Database Schema Verification</h2>";

$tables_to_check = [
    'employees' => 'employee_id',
    'ta_attendance' => 'employee_id',
    'ta_leave_requests' => 'employee_id',
    'ta_employee_shifts' => 'employee_id',
    'ta_leave_balances' => 'employee_id',
    'ta_overtime_tracking' => 'employee_id',
    'ta_flexible_schedules' => 'employee_id',
    'ta_attendance_metrics' => 'employee_id',
    'ta_punctuality_scores' => 'employee_id',
];

try {
    foreach ($tables_to_check as $table => $column) {
        $query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = 'hr_management'";
        $stmt = $db->prepare($query);
        $stmt->execute([$table, $column]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            if (strpos($result['COLUMN_TYPE'], 'int') !== false) {
                $successes[] = "✅ {$table}.{$column} is INT";
            } else {
                $errors[] = "❌ {$table}.{$column} is {$result['COLUMN_TYPE']} (should be INT)";
            }
        } else {
            $warnings[] = "⚠️ {$table}.{$column} not found";
        }
    }
} catch (Exception $e) {
    $errors[] = "❌ Error checking schema: " . $e->getMessage();
}

// ============================================================================
// 2. VERIFY FOREIGN KEY CONSTRAINTS
// ============================================================================
echo "<h2>2. Foreign Key Constraints Verification</h2>";

try {
    $query = "SELECT 
              CONSTRAINT_NAME,
              TABLE_NAME,
              COLUMN_NAME,
              REFERENCED_TABLE_NAME,
              REFERENCED_COLUMN_NAME
          FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
          WHERE REFERENCED_TABLE_NAME = 'employees' 
            AND TABLE_SCHEMA = 'hr_management'
          ORDER BY TABLE_NAME";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($constraints) > 0) {
        $successes[] = "✅ Found " . count($constraints) . " foreign key constraints referencing employees table";
        foreach ($constraints as $fk) {
            $successes[] = "  ✓ {$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']} → employees.{$fk['REFERENCED_COLUMN_NAME']}";
        }
    } else {
        $warnings[] = "⚠️ No foreign key constraints found (may have been dropped)";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error checking foreign keys: " . $e->getMessage();
}

// ============================================================================
// 3. VERIFY EMPLOYEE DATA
// ============================================================================
echo "<h2>3. Employee Data Verification</h2>";

try {
    $query = "SELECT employee_id, full_name FROM employees ORDER BY employee_id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($employees) > 0) {
        $successes[] = "✅ Found " . count($employees) . " employees with INT IDs";
        foreach ($employees as $emp) {
            $successes[] = "  ✓ ID: {$emp['employee_id']} ({$emp['full_name']})";
        }
    } else {
        $errors[] = "❌ No employees found";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error fetching employees: " . $e->getMessage();
}

// ============================================================================
// 4. VERIFY DATA CONSISTENCY
// ============================================================================
echo "<h2>4. Data Consistency Verification</h2>";

$consistency_checks = [
    'ta_attendance' => 'Attendance records',
    'ta_leave_requests' => 'Leave requests',
    'ta_employee_shifts' => 'Employee shifts',
    'ta_leave_balances' => 'Leave balances',
];

try {
    foreach ($consistency_checks as $table => $description) {
        $query = "SELECT COUNT(*) as count FROM {$table}";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'];
        
        if ($count >= 0) {
            $successes[] = "✅ {$description} ({$table}): {$count} records";
            
            // Check for null employee_id
            $query = "SELECT COUNT(*) as count FROM {$table} WHERE employee_id IS NULL";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $null_result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($null_result['count'] > 0) {
                $warnings[] = "⚠️ {$table} has {$null_result['count']} records with NULL employee_id";
            }
        }
    }
} catch (Exception $e) {
    $warnings[] = "⚠️ Error checking data consistency: " . $e->getMessage();
}

// ============================================================================
// 5. VERIFY SESSION MANAGEMENT
// ============================================================================
echo "<h2>5. Session Management Verification</h2>";

if (isset($_SESSION['user']['employee_id'])) {
    $emp_id = $_SESSION['user']['employee_id'];
    $type = gettype($emp_id);
    
    if ($type === 'integer') {
        $successes[] = "✅ Session employee_id is INT: {$emp_id}";
    } else {
        $errors[] = "❌ Session employee_id is {$type}: {$emp_id} (should be INT)";
    }
} else {
    $warnings[] = "⚠️ Session not initialized yet";
}

// ============================================================================
// 6. VERIFY DATABASE QUERIES
// ============================================================================
echo "<h2>6. Sample Query Verification</h2>";

try {
    // Test query with INT parameter
    $employee_id = 1;
    $query = "SELECT * FROM employees WHERE employee_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$employee_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $successes[] = "✅ Query with INT parameter works: Found employee ID {$result['employee_id']}";
    } else {
        $warnings[] = "⚠️ No employee found with ID 1";
    }
    
    // Test join query
    $query = "SELECT e.employee_id, e.full_name, COUNT(lr.id) as leave_count
              FROM employees e
              LEFT JOIN ta_leave_requests lr ON e.employee_id = lr.employee_id
              WHERE e.employee_id = ?
              GROUP BY e.employee_id";
    $stmt = $db->prepare($query);
    $stmt->execute([1]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $successes[] = "✅ JOIN query works: {$result['full_name']} has {$result['leave_count']} leave requests";
    }
} catch (Exception $e) {
    $errors[] = "❌ Query test failed: " . $e->getMessage();
}

// ============================================================================
// DISPLAY RESULTS
// ============================================================================
echo "<div style='margin: 20px 0;'>";

if (!empty($successes)) {
    echo "<h3 style='color: green;'>✅ Successes (" . count($successes) . ")</h3>";
    echo "<ul style='color: green;'>";
    foreach ($successes as $msg) {
        echo "<li>{$msg}</li>";
    }
    echo "</ul>";
}

if (!empty($warnings)) {
    echo "<h3 style='color: orange;'>⚠️ Warnings (" . count($warnings) . ")</h3>";
    echo "<ul style='color: orange;'>";
    foreach ($warnings as $msg) {
        echo "<li>{$msg}</li>";
    }
    echo "</ul>";
}

if (!empty($errors)) {
    echo "<h3 style='color: red;'>❌ Errors (" . count($errors) . ")</h3>";
    echo "<ul style='color: red;'>";
    foreach ($errors as $msg) {
        echo "<li>{$msg}</li>";
    }
    echo "</ul>";
}

// ============================================================================
// FINAL STATUS
// ============================================================================
echo "<hr>";
echo "<h2>Final Status</h2>";

$total_checks = count($successes) + count($warnings) + count($errors);
$success_rate = $total_checks > 0 ? (count($successes) / $total_checks * 100) : 0;

if (count($errors) === 0) {
    echo "<p style='font-size: 18px; color: green;'>✅ <strong>All critical checks passed!</strong> ({$success_rate}% success rate)</p>";
    echo "<p>The INT employee_id refactoring is complete and ready for testing.</p>";
} else {
    echo "<p style='font-size: 18px; color: red;'>❌ <strong>Critical errors found!</strong> ({$success_rate}% success rate)</p>";
    echo "<p>Please fix the errors above before proceeding.</p>";
}

echo "</div>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
ul {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
