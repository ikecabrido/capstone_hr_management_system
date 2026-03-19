<?php

require_once "auth/Auth.php";

$auth = new Auth();

if (!$auth->check()) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'] ?? null;
$role = $auth->role();

// Check if this is an employee-based login with department redirect
if ($user && isset($user['is_employee_auth']) && $user['is_employee_auth'] === true) {
    // Employee-based login - use department-specific redirect if available
    if (!empty($user['redirect_page'])) {
        header("Location: " . $user['redirect_page']);
        exit;
    }
}

// Fall back to role-based routing for traditional users
switch ($role) {

    case 'admin':
    case 'hr_admin':
    case 'compliance':
        header("Location: legal_compliance/legal_compliance.php");
        break;
    case 'it_admin':
        // IT Admin - redirect to Time & Attendance (as specified in task)
        header("Location: time_attendance/time_attendance.php");
        break;
    case 'recruitment':
        header("Location: recruitment/recruitment.php");
        break;
    case 'payroll':
        header("Location: payroll/payroll.php");
        break;
    case 'time':
        header("Location: time_attendance/time_attendance.php");
        break;
    case 'clinic':
        header("Location: clinic/clinic.php");
        break;
    case 'workforce':
        header("Location: workforce/workforce.php");
        break;
    case 'employee':
    case 'employee_portal':
        // Try employee portal first, fallback to employee
        if (file_exists("employee_portal/employee_portal.php")) {
            header("Location: employee_portal/employee_portal.php");
        } else {
            header("Location: employee/employee.php");
        }
        break;
    case 'learning':
        header("Location: learning_development/learning_development.php");
        break;
    case 'performance':
        header("Location: performance/performance.php");
        break;
    case 'engagement_relations':
        header("Location: engagement_relations/engagement_relations.php");
        break;
    case 'exit':
        header("Location: exit_management/exit_management.php");
        break;

    default:
        // Default to employee portal for unknown roles
        if (file_exists("employee_portal/employee_portal.php")) {
            header("Location: employee_portal/employee_portal.php");
        } else {
            echo "No module assigned for this role.";
        }
}
