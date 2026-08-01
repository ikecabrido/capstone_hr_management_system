<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate the AJAX POST request
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'username' => 'hr_exit', 'theme' => 'light'];

// Simulate POST data
$_POST['ajax_action'] = 'get_eligible_employees';
$_POST['controller'] = 'exit_management'; // or leave unset for default

// Now include the exit_management.php and capture output
ob_start();
include 'exit_management.php';
$output = ob_get_clean();

header('Content-Type: application/json');
echo $output;
?>
