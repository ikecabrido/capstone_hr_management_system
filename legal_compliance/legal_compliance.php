<?php
/**
 * Legal & Compliance Dashboard
 * HR Legal & Compliance System - Bestlink College of the Philippines
 * Main entry point - includes separated concerns
 */

// Include data controller (PHP logic)
require_once __DIR__ . '/includes/dashboard_data.php';

// Determine base paths for assets
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal & Compliance Dashboard - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Chart.js -->
    <script src="<?= $basePath ?>assets/plugins/chart.js/Chart.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- Dashboard CSS (separated) -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <?php include $componentsPath . 'header.php'; ?>
    
    <!-- Main Sidebar -->
    <?php include $componentsPath . 'sidebar.php'; ?>
    
    <!-- Include HTML View Template -->
    <?php include __DIR__ . '/includes/dashboard_view.php'; ?>
    
    <!-- Main Footer -->
</div>

<!-- Scripts -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>

<!-- Pass PHP data to JavaScript -->
<script>
    window.dashboardData = {
        incidentByStatus: {
            submitted: <?= $incidentByStatus['submitted'] ?? 0 ?>,
            under_review: <?= $incidentByStatus['under_review'] ?? 0 ?>,
            investigation: <?= $incidentByStatus['investigation'] ?? 0 ?>,
            escalated: <?= $incidentByStatus['escalated'] ?? 0 ?>,
            resolved: <?= $incidentByStatus['resolved'] ?? 0 ?>,
            closed: <?= $incidentByStatus['closed'] ?? 0 ?>
        },
        risksByLevel: {
            low: <?= $risksByLevel['low'] ?? 0 ?>,
            medium: <?= $risksByLevel['medium'] ?? 0 ?>,
            high: <?= $risksByLevel['high'] ?? 0 ?>
        }
    };
</script>

<!-- Dashboard JavaScript (separated) -->
<script src="js/dashboard.js"></script>

</body>
</html>