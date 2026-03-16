<?php

require_once "auth/Auth.php";

$auth = new Auth();

if (!$auth->check()) {
    header("Location: login.php");
    exit;
}

$role = $auth->role();

switch ($role) {

    case 'admin':
        header("Location: admin_dashboard.php");
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
    case 'compliance':
        header("Location: compliance_legal/compliance.php");
        break;
    case 'clinic':
        header("Location: clinic/clinic.php");
        break;
    case 'workforce':
        header("Location: workforce/workforce.php");
        break;
    case 'employee':
        header("Location: employee/employee.php");
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
        echo "No module assigned.";
}
