<?php
/**
 * QR Handler - Unified endpoint for QR code scans
 * Handles both logged-in and logged-out users
 */

// Set Philippines timezone globally
date_default_timezone_set('Asia/Manila');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the QR token from URL
$qr_token = trim($_GET['qr_token'] ?? '');

if (empty($qr_token)) {
    // No token provided, redirect to login
    header('Location: login_form.php');
    exit;
}

// Check if user is already logged in
$is_logged_in = false;
$user_id = null;

if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $is_logged_in = true;
    $user_id = $_SESSION['user']['id'];
} elseif (isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    $user_id = $_SESSION['user_id'];
}

if ($is_logged_in && $user_id) {
    // User is already logged in - process QR attendance directly
    // Store the QR token and redirect to QR attendance handler
    $_SESSION['qr_token'] = $qr_token;
    
    // Redirect to the employee portal QR attendance handler
    header('Location: employee_portal/index.php?url=qr-attendance');
    exit;
} else {
    // User is not logged in - redirect to EMPLOYEE PORTAL login form with QR token
    header('Location: employee_portal/index.php?url=auth-index&qr_token=' . urlencode($qr_token));
    exit;
}
?>
