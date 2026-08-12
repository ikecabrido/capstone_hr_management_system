<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate an authenticated session
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'username' => 'tester', 'name' => 'Tester', 'theme' => 'light'];

// Params: change these to match an existing case in your DB
$exit_case_type = $argv[1] ?? 'resignation';
$exit_case_id = (int)($argv[2] ?? 1);

$_POST['ajax_action'] = 'get_exit_case_documentation';
$_POST['controller'] = 'exit_management';
$_POST['exit_case_type'] = $exit_case_type;
$_POST['exit_case_id'] = $exit_case_id;

ob_start();
include __DIR__ . '/exit_management.php';
$output = ob_get_clean();

// Also dump recent php error log tail if available
$errorLog = '';
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    $errorLog = "\n--- last 100 lines of PHP error_log ({$logFile}) ---\n" . implode("\n", array_slice(file($logFile), -100));
}

echo "OUTPUT:\n" . $output . "$errorLog\n";
