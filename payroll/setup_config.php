<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/auth_check.php';

$db = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_payroll_config'])) {
    try {
        $db->beginTransaction();

        // 1. Setup Position Deduction Rates (if empty)
        $stmt = $db->query("SELECT COUNT(*) FROM pr_position_deduction_rates WHERE is_active = 1");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $stmt = $db->prepare("
                INSERT INTO pr_position_deduction_rates 
                (position_type, absence_deduction_amount, late_per_minute_rate, late_per_hour_rate, is_active)
                VALUES (?, ?, ?, ?, 1)
            ");
            
            $stmt->execute(['Admin', 1020, 2.00, 120.00]);
            $stmt->execute(['Teacher', 1536, 3.00, 180.00]);
            $stmt->execute(['Manager', 1500, 3.50, 210.00]);
            
            echo "<div class='alert alert-success'>✓ Position deduction rates created</div>";
        }

        // 2. Setup Employee Details for all active employees
        $stmt = $db->query("
            SELECT e.employee_id 
            FROM employees e
            WHERE e.employment_status = 'Active'
            AND NOT EXISTS (SELECT 1 FROM pr_employee_details WHERE employee_id = e.employee_id)
        ");
        $missingEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($missingEmployees)) {
            $stmt = $db->prepare("
                INSERT IGNORE INTO pr_employee_details 
                (employee_id, base_salary, position_type, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            foreach ($missingEmployees as $emp) {
                // Default: Admin position with min salary
                $stmt->execute([$emp['employee_id'], 20000, 'Admin']);
            }
            
            echo "<div class='alert alert-success'>✓ Employee details created for " . count($missingEmployees) . " employees</div>";
        }

        // 3. Setup Employee Benefits for all active employees
        $stmt = $db->query("
            SELECT e.employee_id 
            FROM employees e
            WHERE e.employment_status = 'Active'
            AND NOT EXISTS (SELECT 1 FROM pr_employee_benefits WHERE employee_id = e.employee_id)
        ");
        $missingBenefits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($missingBenefits)) {
            $stmt = $db->prepare("
                INSERT IGNORE INTO pr_employee_benefits 
                (employee_id, has_sss, has_philhealth, has_pagibig, created_at)
                VALUES (?, 1, 1, 1, NOW())
            ");
            
            foreach ($missingBenefits as $emp) {
                $stmt->execute([$emp['employee_id']]);
            }
            
            echo "<div class='alert alert-success'>✓ Employee benefits configured for " . count($missingBenefits) . " employees</div>";
        }

        // 4. Setup Teacher Qualification Rates (if needed)
        $stmt = $db->query("SELECT COUNT(*) FROM pr_teacher_qualification_rates WHERE is_active = 1");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $stmt = $db->prepare("
                INSERT INTO pr_teacher_qualification_rates 
                (qualification, pay_per_unit, is_active, created_at)
                VALUES (?, ?, 1, NOW())
            ");
            
            $stmt->execute(['ProfEd', 128]);
            $stmt->execute(['Masters', 180]);
            $stmt->execute(['Doctorate', 250]);
            
            echo "<div class='alert alert-success'>✓ Teacher qualification rates created</div>";
        }

        $db->commit();
        echo "<div class='alert alert-info'>✓ Payroll configuration setup complete!</div>";
        echo "<p><a href='payrollProcess.php' class='btn btn-primary'>Go to Payroll Processing</a></p>";
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll Configuration Setup</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0">Payroll Configuration Setup</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h4>Setup Payroll System</h4>
                            <p>Click the button below to initialize all required payroll configuration tables:</p>
                            
                            <form method="POST">
                                <button type="submit" name="setup_payroll_config" value="1" class="btn btn-success btn-lg">
                                    <i class="fas fa-cog"></i> Setup Payroll Configuration
                                </button>
                            </form>
                            
                            <hr>
                            
                            <h5>What will be configured:</h5>
                            <ul>
                                <li>✓ Position deduction rates (Admin, Teacher, Manager)</li>
                                <li>✓ Employee details (base salary, position type)</li>
                                <li>✓ Employee benefits (SSS, PhilHealth, Pag-IBIG)</li>
                                <li>✓ Teacher qualification rates (if using teacher module)</li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <strong>Note:</strong> After setup, you can edit individual employee configurations in the Salary Overview page.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
