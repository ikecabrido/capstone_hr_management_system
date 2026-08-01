<?php
/**
 * Labor Law Compliance Module
 * HR Legal & Compliance System
 * Bestlink College of the Philippines
 * 
 * This is the main entry point that includes:
 * - Controller (PHP logic and database queries)
 * - View (HTML template)
 * - CSS and JavaScript
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Form Protection utility
require_once __DIR__ . '/../includes/FormProtection.php';

// Determine base paths for assets
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';

// Include data controller (PHP logic)
require_once __DIR__ . '/includes/labor_law_controller.php';

// Get current view
$currentView = $_GET['view'] ?? 'checklist';

// Handle PHP-dependent modal
$showAddForm = isset($_GET['add']) && $_GET['add'] == '1';

// Get data using controller functions
$stats = getComplianceStats($db);
$complianceRate = getComplianceRate($stats);
$categories = getComplianceByCategory($db);
$upcomingDeadlines = getUpcomingDeadlines($db);
$overdueItems = getOverdueItems($db);
$employees = getEmployees($db);

// Get filters FROM lc_request
$filters = [
    'status' => $_GET['status'] ?? '',
    'category' => $_GET['category'] ?? '',
    'search' => $_GET['search'] ?? ''
];
$complianceItems = getComplianceItems($db, $filters);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Law Compliance - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- Dashboard CSS (separated) -->
    <link rel="stylesheet" href="css/dashboard.css">
    
    <!-- Labor Law Compliance CSS -->
    <link rel="stylesheet" href="css/labor_law_compliance.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <?php include $componentsPath . 'header.php'; ?>
    
    <!-- Main Sidebar -->
    <?php include $componentsPath . 'sidebar.php'; ?>
    
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-balance-scale mr-2"></i>
                            Labor Law Compliance
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Labor Law Compliance</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- View Template -->
                <?php include 'includes/labor_law_view.php'; ?>
            </div>
        </div>
    </div>
    
    <!-- Main Footer -->
</div>

<?php include __DIR__ . '/includes/labor_law_modals.php'; ?>

<!-- Scripts -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>



<!-- Dashboard JavaScript (separated) -->
<script src="js/dashboard.js"></script>

<!-- Labor Law Compliance JavaScript -->
<script src="js/labor_law_compliance.js"></script>
</body>
</html>
