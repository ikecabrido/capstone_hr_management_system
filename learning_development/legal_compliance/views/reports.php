<?php
/**
 * Reports & Audits View
 * Displays compliance reports and audit summaries
 */

// Check if being included from router
$isFromRouter = defined('COMPLIANCE_INCLUDED') && COMPLIANCE_INCLUDED;

if (!$isFromRouter) {
    session_start();
    require_once "../auth/database.php";
    require_once "controllers/legalComplianceController.php";
    require_once "../auth/auth_check.php";
    
    // Page configuration
    $pageTitle = 'Reports & Audits';
    $currentPage = 'Reports';
    
    $db = Database::getInstance()->getConnection();
    $controller = new LegalComplianceController($db);
    
    // Get dashboard stats for reports
    $stats = [];
    try {
        $stats = $controller->getDashboardStats() ?? [];
    } catch (Exception $e) {
        // Database might not be ready
    }
    
    include "components/header_template.php";
}

// Get stats from controller or use defaults
if (empty($stats)) {
    $stats = [
        'total_employees' => 0,
        'compliant_count' => 0,
        'at_risk_count' => 0,
        'non_compliant_count' => 0
    ];
}
?>

<!-- Custom CSS -->
<link rel="stylesheet" href="compliance.css">

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Stats Cards -->
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?= $stats['total_employees'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Compliant</span>
                        <span class="info-box-number"><?= $stats['compliant_count'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">At Risk</span>
                        <span class="info-box-number"><?= $stats['at_risk_count'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Non-Compliant</span>
                        <span class="info-box-number"><?= $stats['non_compliant_count'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Types -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Available Reports</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-balance-scale"></i> Compliance Report</h5>
                                        <p class="text-muted">Overview of compliance status across all employees</p>
                                        <button class="btn btn-primary btn-sm">Generate Report</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-file-policy"></i> Policy Report</h5>
                                        <p class="text-muted">Policy acknowledgment and compliance summary</p>
                                        <button class="btn btn-primary btn-sm">Generate Report</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5><i class="fas fa-clipboard-check"></i> Audit Report</h5>
                                        <p class="text-muted">Internal audit findings and recommendations</p>
                                        <button class="btn btn-primary btn-sm">Generate Report</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "components/footer_template.php"; ?>
