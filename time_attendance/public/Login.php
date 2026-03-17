<?php
/**
 * DEPRECATED: Old Login Page - Time & Attendance System
 * 
 * This file is DEPRECATED and should not be used.
 * All login requests should use the root-level login_form.php instead.
 * 
 * This is kept for reference only. All redirects have been updated
 * to point to ../../login_form.php from the root directory.
 */

// Redirect all requests to the root login form
// Preserve any query parameters like qr_token
$redirectUrl = '../../login_form.php';
if (!empty($_SERVER['QUERY_STRING'])) {
    $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
}

header("Location: " . $redirectUrl);
exit;
