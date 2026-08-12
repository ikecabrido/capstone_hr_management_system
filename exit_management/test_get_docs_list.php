<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate an authenticated session
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'username' => 'tester', 'name' => 'Tester', 'theme' => 'light'];

// Simulate POST data for AJAX
$_POST['ajax_action'] = 'get_exit_case_documentation_list';
$_POST['controller'] = 'exit_management';
$_POST['status'] = 'all';
$_POST['page'] = 1;
$_POST['limit'] = 10;
$_POST['search'] = '';

// Include the main router which will dispatch to the controller
ob_start();
include __DIR__ . '/exit_management.php';
$output = ob_get_clean();

// Print the raw output (should be JSON)
echo $output;
