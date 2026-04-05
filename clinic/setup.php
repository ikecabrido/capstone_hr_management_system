<?php
/**
 * Clinic Management System Setup Script
 * 
 * This script helps set up the database and initialize the system
 * Run this script once to create the database and tables
 */

session_start();
require_once "../auth/auth_check.php";
require_once "../auth/database.php";

// Check if user is admin
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied. Admin privileges required.");
}

$setup_step = $_GET['step'] ?? 'welcome';
$error = '';
$success = '';

// Handle setup steps
switch ($setup_step) {
    case 'database':
        // Test database connection
        try {
            $database = Database::getInstance();
            $db = $database->getConnection();
            
            if ($db === null) {
                $error = "Database connection failed. Please check your configuration.";
            } else {
                $success = "Database connection successful!";
                header('Location: setup.php?step=tables');
                exit;
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
        break;
        
    case 'tables':
        // Create database tables
        try {
            $database = Database::getInstance();
            $db = $database->getConnection();
            
            // Read and execute SQL file
            $sql_file = __DIR__ . '/database/simple_database.sql';
            if (!file_exists($sql_file)) {
                $error = "Database schema file not found.";
            } else {
                $sql = file_get_contents($sql_file);
                
                // Split SQL into individual statements
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement) && !preg_match('/^--/', $statement)) {
                        try {
                            $db->exec($statement);
                        } catch (PDOException $e) {
                            // Ignore errors for CREATE DATABASE if it already exists
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                throw $e;
                            }
                        }
                    }
                }
                
                $success = "Database tables created successfully!";
                header('Location: setup.php?step=complete');
                exit;
            }
        } catch (Exception $e) {
            $error = "Error creating tables: " . $e->getMessage();
        }
        break;
        
    case 'complete':
        $success = "Setup completed successfully! Your Clinic Management System is ready to use.";
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            margin: 0 5px;
            background: #f8f9fa;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="text-center mb-4">
            <h1><i class="fas fa-clinic-medical"></i> Clinic Management System</h1>
            <p class="lead">Setup Wizard</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step <?php echo $setup_step === 'welcome' ? 'active' : ($setup_step !== 'welcome' ? 'completed' : ''); ?>">
                <i class="fas fa-info-circle"></i>
                <div>Welcome</div>
            </div>
            <div class="step <?php echo $setup_step === 'database' ? 'active' : ($in_array($setup_step, ['tables', 'complete']) ? 'completed' : ''); ?>">
                <i class="fas fa-database"></i>
                <div>Database</div>
            </div>
            <div class="step <?php echo $setup_step === 'tables' ? 'active' : ($setup_step === 'complete' ? 'completed' : ''); ?>">
                <i class="fas fa-table"></i>
                <div>Tables</div>
            </div>
            <div class="step <?php echo $setup_step === 'complete' ? 'active' : ''; ?>">
                <i class="fas fa-check"></i>
                <div>Complete</div>
            </div>
        </div>

        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Step Content -->
        <?php if ($setup_step === 'welcome'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Welcome to Clinic Management System</h3>
                </div>
                <div class="card-body">
                    <p>This setup wizard will help you configure your Clinic Management System with all the necessary modules:</p>
                    
                    <h4>System Features:</h4>
                    <ul class="feature-list">
                        <li><strong>Employee Management</strong> - Manage school staff with medical profile integration</li>
                        <li><strong>Medical Records</strong> - Store health records, diagnoses, treatments, and vital signs</li>
                        <li><strong>Medicines Inventory</strong> - Track stock levels, expiry dates, and usage logs</li>
                        <li><strong>Clinic Reports</strong> - Generate daily/weekly/monthly reports with health trends</li>
                        <li><strong>Emergency Module</strong> - Handle urgent cases with incident logging and response tracking</li>
                    </ul>
                    
                    <h4>Technical Features:</h4>
                    <ul class="feature-list">
                        <li>Object-Oriented Programming (OOP) Architecture</li>
                        <li>MySQL Database Integration</li>
                        <li>Cross-Module Data Connectivity</li>
                        <li>Professional AdminLTE Interface</li>
                        <li>Responsive Design for All Devices</li>
                        <li>Secure Session Management</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Prerequisites:</strong> Make sure you have MySQL database access and proper file permissions.
                    </div>
                </div>
                <div class="card-footer">
                    <a href="setup.php?step=database" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-right"></i> Begin Setup
                    </a>
                </div>
            </div>

        <?php elseif ($setup_step === 'database'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-database"></i> Database Configuration</h3>
                </div>
                <div class="card-body">
                    <p>The setup wizard will now test your database connection and create the necessary tables.</p>
                    
                    <h5>Database Configuration:</h5>
                    <ul>
                        <li><strong>Host:</strong> localhost</li>
                        <li><strong>Database:</strong> bcp_clinic_system</li>
                        <li><strong>Tables:</strong> 10+ tables for complete clinic management</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Make sure your MySQL server is running and you have proper permissions.
                    </div>
                </div>
                <div class="card-footer">
                    <form method="POST" action="setup.php?step=database">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play"></i> Test Database Connection
                        </button>
                        <a href="setup.php?step=welcome" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </form>
                </div>
            </div>

        <?php elseif ($setup_step === 'tables'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-table"></i> Creating Database Tables</h3>
                </div>
                <div class="card-body">
                    <p>The setup wizard will now create all necessary database tables:</p>
                    
                    <h5>Tables to be created:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>cm_employees - Staff management</li>
                                <li>cm_patients - Patient records</li>
                                <li>cm_medical_records - Health records</li>
                                <li>cm_medicine_inventory - Medicine stock</li>
                                <li>cm_medicine_usage_logs - Usage tracking</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>cm_emergency_cases - Emergency tracking</li>
                                <li>cm_clinic_reports - Report storage</li>
                                <li>cm_vital_signs - Vital signs data</li>
                                <li>cm_document_attachments - File storage</li>
                                <li>cm_departments - Department info</li>
                                <li>cm_suppliers - Supplier management</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        The setup will also create sample data and stored procedures for optimal performance.
                    </div>
                </div>
                <div class="card-footer">
                    <form method="POST" action="setup.php?step=tables">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-database"></i> Create Tables
                        </button>
                        <a href="setup.php?step=database" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </form>
                </div>
            </div>

        <?php elseif ($setup_step === 'complete'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-check-circle"></i> Setup Complete!</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h4><i class="fas fa-check"></i> Congratulations!</h4>
                        <p>Your Clinic Management System has been successfully set up and is ready to use.</p>
                    </div>
                    
                    <h4>What's Next?</h4>
                    <ol>
                        <li><strong>Access the System:</strong> Go to the main dashboard at <code>clinic.php</code></li>
                        <li><strong>Explore Modules:</strong> Navigate through all 5 modules using the sidebar</li>
                        <li><strong>Add Data:</strong> Start adding employees, patients, and medicines</li>
                        <li><strong>Generate Reports:</strong> Create your first clinic reports</li>
                        <li><strong>Customize:</strong> Adjust settings to match your clinic's needs</li>
                    </ol>
                    
                    <h4>Quick Links:</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="clinic.php" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-tachometer-alt"></i> Main Dashboard
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="Employee_Patient.php" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-users"></i> Employee Management
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="MedicalRecordsHistory.php" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-file-medical"></i> Medical Records
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="MedicinesInventory.php" class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-pills"></i> Medicines Inventory
                            </a>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb"></i> 
                        <strong>Tip:</strong> Bookmark the main dashboard for quick access to your clinic system.
                    </div>
                </div>
                <div class="card-footer">
                    <a href="clinic.php" class="btn btn-success btn-lg">
                        <i class="fas fa-rocket"></i> Launch Clinic System
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
