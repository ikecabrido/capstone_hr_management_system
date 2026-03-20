<?php
/**
 * Index / Home Page - Time & Attendance System
 * Redirects based on authentication status and role
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/core/Session.php";

Session::start();

// If user is authenticated, redirect to appropriate dashboard
if (AuthController::isAuthenticated()) {
    if (AuthController::hasRole('HR_ADMIN')) {
        header("Location: dashboard.php");
    } else {
        header("Location: employee_dashboard.php");
    }
    exit;
}

// Not authenticated, redirect to root login
header("Location: ../../login_form.php");
exit;
