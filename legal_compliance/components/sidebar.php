<?php
/**
 * Sidebar Component
 * Main sidebar with navigation menu
 * Used in payroll and other modules
 */

// Include database connection
require_once __DIR__ . '/../../auth/database.php';

// Detect the current folder level based on PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path based on current folder
if ($currentFolder === 'views') {
    $basePath = '../../';
} elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
    $basePath = '../';
} elseif ($currentFolder === 'legal_compliance') {
    $basePath = '../';
} else {
    $basePath = '../';
}

if (!function_exists('getHomePagePath')) {
    // Determine home page path based on current folder
    function getHomePagePath($currentFolder) {
        if ($currentFolder === 'payroll') {
            return '../payroll/payroll.php';
        } elseif ($currentFolder === 'leave_management') {
            return '../leave_management/leave_management.php';
        } elseif ($currentFolder === 'employee_management' || $currentFolder === 'employee') {
            return '../employee/employee.php';
        } elseif ($currentFolder === 'views') {
            return '../legal_compliance.php';
        } elseif ($currentFolder === 'legal_compliance') {
            return 'legal_compliance.php';
        } else {
            return 'legal_compliance.php';
        }
    }
}

// Determine path to leave management
function getLeaveManagementPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../leave_management/leave_management.php';
    } elseif ($currentFolder === 'leave_management') {
        return 'leave_management.php';
    } elseif ($currentFolder === 'views') {
        return '../leave_management.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'leave_management.php';
    } else {
        return 'leave_management.php';
    }
}

// Determine path to employee management
function getEmployeeManagementPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../employee/employee.php';
    } elseif ($currentFolder === 'leave_management') {
        return '../employee/employee.php';
    } elseif ($currentFolder === 'views') {
        return '../employee/employee.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return '../employee/employee.php';
    } else {
        return 'employee.php';
    }
}

// Determine path to labor law compliance
function getLaborLawCompliancePath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/labor_law_compliance.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/labor_law_compliance.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'labor_law_compliance.php';
    } else {
        return 'labor_law_compliance.php';
    }
}

// Determine path to incident reporting
function getIncidentReportingPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/incident_reporting.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/incident_reporting.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'incident_reporting.php';
    } else {
        return 'incident_reporting.php';
    }
}

// Determine path to policy documentation (old - kept for compatibility)
function getPolicyDocPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/policy_documentation.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/policy_documentation.php';
    } elseif ($currentFolder === 'views') {
        return '../legal_compliance/labor_law_compliance.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'labor_law_compliance.php';
    } else {
        return 'labor_law_compliance.php';
    }
}

// Determine path to policy documentation
function getPolicyDocumentationPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/policy_documentation.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/policy_documentation.php';
    } elseif ($currentFolder === 'views') {
        return '../legal_compliance/policy_documentation.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'policy_documentation.php';
    } else {
        return 'policy_documentation.php';
    }
}

// Determine path to employee contributions
function getEmployeeContributionsPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/employee_contributions.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/employee_contributions.php';
    } elseif ($currentFolder === 'views') {
        return '../legal_compliance/employee_contributions.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'employee_contributions.php';
    } else {
        return 'employee_contributions.php';
    }
}

// Determine path to medical emergencies
function getMedicalEmergenciesPath($currentFolder) {
    if ($currentFolder === 'payroll') {
        return '../legal_compliance/medical_emergencies.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management') {
        return '../legal_compliance/medical_emergencies.php';
    } elseif ($currentFolder === 'views') {
        return '../legal_compliance/medical_emergencies.php';
    } elseif ($currentFolder === 'legal_compliance') {
        return 'medical_emergencies.php';
    } else {
        return 'medical_emergencies.php';
    }
}

// Determine logout path
function getLogoutPath($currentFolder) {
    if ($currentFolder === 'views') {
        return '../../logout.php';
    } elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../../logout.php';
    } else {
        return '../logout.php';
    }
}

$homePage = getHomePagePath($currentFolder);
$leaveManagementPath = getLeaveManagementPath($currentFolder);
$employeeManagementPath = getEmployeeManagementPath($currentFolder);
$laborLawCompliancePath = getLaborLawCompliancePath($currentFolder);
$policyDocPath = getPolicyDocumentationPath($currentFolder);
$incidentReportingPath = getIncidentReportingPath($currentFolder);
$employeeContributionsPath = getEmployeeContributionsPath($currentFolder);
$medicalEmergenciesPath = getMedicalEmergenciesPath($currentFolder);
$logoutPath = getLogoutPath($currentFolder);

// Get user name FROM lc_session
$userName = $_SESSION['user']['name'] ?? 'Admin';
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= $homePage ?>" class="brand-link">
        <img src="<?= $basePath ?>assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    HR Admin <?= htmlspecialchars($userName) ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= $homePage ?>" class="nav-link <?= ($currentFolder === 'payroll' || basename($_SERVER['PHP_SELF']) === 'payroll.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Leave Management -->
                <li class="nav-item">
                    <a href="<?= $leaveManagementPath ?>" class="nav-link <?= ($currentFolder === 'leave_management' || basename($_SERVER['PHP_SELF']) === 'leave_management.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Leave Management</p>
                    </a>
                </li>

                <!-- Labor Law Compliance -->
                <li class="nav-item">
                    <a href="<?= $laborLawCompliancePath ?>" class="nav-link <?= ($currentFolder === 'labor_law_compliance' || basename($_SERVER['PHP_SELF']) === 'labor_law_compliance.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-balance-scale"></i>
                        <p>Labor Law Compliance</p>
                    </a>
                </li>

                <!-- Policy Documentation -->
                <li class="nav-item">
                    <a href="<?= $policyDocPath ?>" class="nav-link <?= ($currentFolder === 'legal_compliance' && basename($_SERVER['PHP_SELF']) === 'policy_documentation.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Policy Documentation</p>
                    </a>
                </li>

                <!-- Incident Reporting -->
                <li class="nav-item">
                    <a href="<?= $incidentReportingPath ?>" class="nav-link <?= ($currentFolder === 'legal_compliance' && basename($_SERVER['PHP_SELF']) === 'incident_reporting.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>Incident Reporting</p>
                    </a>
                </li>

                <!-- Employee Contributions -->
                <li class="nav-item">
                    <a href="<?= $employeeContributionsPath ?>" class="nav-link <?= ($currentFolder === 'legal_compliance' && basename($_SERVER['PHP_SELF']) === 'employee_contributions.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>Employee Contributions</p>
                    </a>
                </li>

                <!-- Medical Emergencies -->
                <li class="nav-item">
                    <a href="<?= $medicalEmergenciesPath ?>" class="nav-link <?= ($currentFolder === 'legal_compliance' && basename($_SERVER['PHP_SELF']) === 'medical_emergencies.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-ambulance"></i>
                        <p>Medical Emergencies</p>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-header">OTHER</li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="<?= $logoutPath ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
