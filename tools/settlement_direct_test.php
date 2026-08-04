<?php
chdir(__DIR__ . '/../exit_management');
// Load base controller first so subclasses can extend it
require_once 'controllers/ExitManagementController.php';
require_once 'controllers/SettlementController.php';
// start session minimal
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$_SESSION['user'] = ['id' => 1, 'name' => 'CLI Test', 'role' => 'admin'];

$ctrl = new SettlementController();
// Call controller method directly
$response = $ctrl->handleAjaxRequest('get_settlements', []);
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
