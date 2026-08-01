<?php
/**
 * Footer Component
 * Main footer with copyright and links
 * Used in payroll and other modules
 */

// Detect the current folder level based on PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path based on current folder
if ($currentFolder === 'views') {
    $basePath = '../../';
} elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
    $basePath = '../';
} elseif ($currentFolder === 'legal_compliance') {
    $basePath = '../';
} else {
    $basePath = '../';
}
?>

<!-- Main Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; 2026-2027 Bestlink College of the Philippines.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
    </div>
</footer>
