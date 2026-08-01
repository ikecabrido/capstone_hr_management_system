<?php
/**
 * Policy Documentation Module
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

// Determine base paths for assets
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$basePath = ($currentFolder === 'legal_compliance') ? '../' : '../';
$componentsPath = $basePath . 'legal_compliance/components/';

// Include data controller (PHP logic)
require_once __DIR__ . '/includes/policy_controller.php';

// Get current view
$currentView = $_GET['view'] ?? 'list';

// Get filters FROM lc_request
$filters = [
    'category' => $_GET['category'] ?? '',
    'department' => $_GET['department'] ?? '',
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? ''
];

// Handle AJAX requests
if (isset($_GET['action']) && $_GET['action'] === 'get_versions') {
    $policyId = isset($_GET['policy_id']) ? (int)$_GET['policy_id'] : 0;
    if ($policyId > 0) {
        $versions = getPolicyVersions($db, $policyId);
        
        // Check if files exist for each version
        foreach ($versions as &$version) {
            if (!empty($version['file_path'])) {
                $version['file_exists'] = file_exists(__DIR__ . '/' . $version['file_path']);
            } else {
                $version['file_exists'] = false;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'versions' => $versions]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid policy ID']);
        exit;
    }
}

// Get data using controller functions
$stats = getPolicyStats($db);
$policies = getPolicies($db, $filters);
$categories = getPolicyCategories($db);
$departments = getPolicyDepartments($db);
$recentUpdates = getRecentPolicyUpdates($db);
$pendingAcknowledgments = getPendingAcknowledgments($db);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policy Documentation - BCP HR System</title>
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $componentsPath ?>custom.css">
    
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    
    <!-- Policy Documentation CSS -->
    <link rel="stylesheet" href="css/policy_documentation.css">
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
                            <i class="fas fa-file-alt mr-2"></i>Policy Documentation
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php">Home</a></li>
                            <li class="breadcrumb-item active">Policy Documentation</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- View Template -->
                <?php include 'includes/policy_view.php'; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
</div>

<!-- jQuery -->
<script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap 4 -->
<script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE App -->
<script src="<?= $basePath ?>assets/dist/js/adminlte.min.js"></script>

<!-- Policy Documentation JS -->
<script src="js/policy_documentation.js"></script>
</body>
</html>
