<?php
/**
 * Payroll Tables Verification Script
 * This script checks if all required payroll tables exist and have the correct structure
 */
require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $tables = [
        'pr_periods' => [
            'period_id' => 'INT',
            'period_name' => 'VARCHAR',
            'start_date' => 'DATE',
            'end_date' => 'DATE',
            'pay_date' => 'DATE',
            'status' => 'VARCHAR'
        ],
        'pr_employee_details' => [
            'id' => 'INT',
            'employee_id' => 'VARCHAR',
            'base_salary' => 'DECIMAL',
            'position_type' => 'VARCHAR'
        ],
        'pr_employee_benefits' => [
            'id' => 'INT',
            'employee_id' => 'VARCHAR',
            'has_sss' => 'TINYINT',
            'has_philhealth' => 'TINYINT',
            'has_pagibig' => 'TINYINT'
        ],
        'pr_position_deduction_rates' => [
            'id' => 'INT',
            'position_type' => 'VARCHAR',
            'absence_deduction_amount' => 'DECIMAL'
        ],
        'pr_teacher_qualification_rates' => [
            'id' => 'INT',
            'qualification' => 'VARCHAR',
            'pay_per_unit' => 'DECIMAL'
        ],
        'ta_attendance' => [
            'id' => 'INT',
            'employee_id' => 'VARCHAR',
            'attendance_date' => 'DATE'
        ]
    ];
    
    echo "<h2>Payroll Tables Verification Report</h2>";
    echo "<hr>";
    
    $allOk = true;
    
    foreach ($tables as $tableName => $columns) {
        echo "<h4>Table: <code>$tableName</code></h4>";
        
        try {
            $result = $db->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName'");
            $existingColumns = $result->fetchAll(PDO::FETCH_KEY_PAIR);
            
            if (empty($existingColumns)) {
                echo "<span style='color:red'>❌ TABLE DOES NOT EXIST</span>";
                $allOk = false;
                echo "<br><br>";
                continue;
            }
            
            echo "<span style='color:green'>✓ Table exists</span><br>";
            
            echo "<strong>Columns:</strong><br>";
            echo "<ul>";
            foreach ($existingColumns as $colName => $colType) {
                echo "<li><code>$colName</code> : <code>$colType</code></li>";
            }
            echo "</ul>";
            
            // Check row count
            $countResult = $db->query("SELECT COUNT(*) as cnt FROM $tableName");
            $count = $countResult->fetch(PDO::FETCH_ASSOC)['cnt'];
            echo "<strong>Row count:</strong> $count records<br>";
            
        } catch (Exception $e) {
            echo "<span style='color:red'>❌ Error: " . $e->getMessage() . "</span>";
            $allOk = false;
        }
        
        echo "<hr>";
    }
    
    echo "<h3>Summary</h3>";
    if ($allOk) {
        echo "<span style='color:green; font-size:18px;'>✓ All tables exist and are configured correctly</span>";
    } else {
        echo "<span style='color:red; font-size:18px;'>❌ Some tables are missing or misconfigured</span>";
        echo "<br><br>";
        echo "<strong>Action:</strong> Import the SQL file: <code>payroll/payroll_employee_config.sql</code>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Database Connection Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
