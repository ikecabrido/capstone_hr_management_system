<?php
/**
 * Test script for archive functionality
 */

session_start();
require_once "../auth/auth_check.php";
require_once "models/ResignationModel.php";

// Simulate user session
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 1,
        'role' => 'exit'
    ];
}

try {
    $resignationModel = new ResignationModel();

    // Test archiving resignation ID 1
    echo "Testing archive of resignation ID 1...<br>";
    $result = $resignationModel->archiveResignation(1, "Test archive reason");

    if ($result) {
        echo "SUCCESS: Resignation archived<br>";
    } else {
        echo "FAILED: Could not archive resignation<br>";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}
?>