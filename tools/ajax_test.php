<?php
// Simple CLI test to invoke the exit_management AJAX router
// Change working directory to exit_management so relative includes resolve correctly
chdir(__DIR__ . '/../exit_management');

// Start session if not active and set a minimal user to pass auth_check
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['user'] = ['id' => 1, 'name' => 'CLI Test', 'role' => 'admin'];

// Prepare POST payload that the router expects
$_POST = ['ajax_action' => 'get_settlements', 'controller' => 'settlement'];
// Ensure the router sees this as a POST request when included
$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the router (now running from exit_management dir)
include 'exit_management.php';
