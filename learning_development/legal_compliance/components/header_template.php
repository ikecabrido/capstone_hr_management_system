<?php
/**
 * Header Template for Legal Compliance Views
 * Include this at the beginning of each view file
 */
$pageTitle = $pageTitle ?? 'Legal & Compliance';
$currentPage = $currentPage ?? '';

// Detect the current folder level based on PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path based on current folder
// URL: http://localhost/capstone_hr_management_system/legal_compliance/leave_management.php
// PHP_SELF: /capstone_hr_management_system/legal_compliance/leave_management.php
// currentFolder: legal_compliance
if ($currentFolder === 'views') {
    // From legal_compliance/views/ folder
    $basePath = '../../';
    $isInViews = true;
} elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
    // From parallel folders at root level (leave_management, employee_management, payroll, etc.)
    $basePath = '../';
    $isInViews = false;
} elseif ($currentFolder === 'legal_compliance') {
    // From legal_compliance root folder
    $basePath = '../';
    $isInViews = false;
} else {
    // Default fallback
    $basePath = '../';
    $isInViews = false;
}

// Get theme from session
$theme = $_SESSION['user']['theme'] ?? 'light';

// Debug: Uncomment to troubleshoot path issues
// error_log("Current folder: $currentFolder, Base path: $basePath");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $pageTitle ?> | BCP</title>
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/fontawesome-free/css/all.min.css" />
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/dist/css/adminlte.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $basePath ?>legal_compliance/custom.css" />
    
    <style>
        /* Override to ensure dark mode works properly */
        body.dark-mode {
            color: #c2c7d0;
        }
        body.dark-mode .card {
            background-color: #454d5e;
            color: #c2c7d0;
        }
        body.dark-mode .content-wrapper {
            background-color: #343a40;
        }
        body.dark-mode .main-sidebar {
            background-color: #343a40;
        }
    </style>
</head>

<body class="hold-transition <?= $theme ?> sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__wobble" src="<?= $basePath ?>assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60">
            </div>
            
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $pageTitle ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?= $isInViews ? '../legal_compliance.php' : 'legal_compliance.php' ?>">Home</a></li>
                                <li class="breadcrumb-item active"><?= $currentPage ?: $pageTitle ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
