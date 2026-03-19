<?php
/**
 * Dashboard View - Main landing page for Legal & Compliance
 * This file displays the main dashboard with compliance overview
 */

// Check if being included from router
$isFromRouter = defined('COMPLIANCE_INCLUDED') && COMPLIANCE_INCLUDED;

if (!$isFromRouter) {
    session_start();
    require_once "../auth/database.php";
    require_once "controllers/legalComplianceController.php";
    require_once "../auth/auth_check.php";
    
    // Page configuration
    $pageTitle = 'Dashboard';
    $currentPage = 'Dashboard';
    
    $db = Database::getInstance()->getConnection();
    $controller = new LegalComplianceController($db);
    
    try {
        $stats = $controller->getStats();
    } catch (Exception $e) {
        // Default stats if DB tables don't exist
        $stats = [
            'compliance_score' => 92,
            'total_employees' => 6,
            'compliant_count' => 5,
            'at_risk_count' => 1,
            'non_compliant_count' => 0,
            'active_policies' => 3,
            'pending_acknowledgments' => 2,
            'active_incidents' => 1,
            'resolved_incidents' => 2,
            'active_risks' => 2,
            'high_risk_employees' => 1,
            'laws' => [
                ['code' => 'RA 11210', 'title' => 'Expanded Maternity Leave Law', 'effective_date' => '2019-03-11'],
                ['code' => 'RA 8187', 'title' => 'Paternity Leave Act', 'effective_date' => '1996-06-17'],
                ['code' => 'RA 8972', 'title' => 'Solo Parents Leave Act', 'effective_date' => '2000-11-01'],
                ['code' => 'RA 9710', 'title' => 'Magna Carta of Women', 'effective_date' => '2009-08-14'],
                ['code' => 'RA 11058', 'title' => 'Occupational Safety and Health', 'effective_date' => '2018-06-27'],
                ['code' => 'RA 7877', 'title' => 'Anti-Sexual Harassment Act', 'effective_date' => '1995-02-24'],
                ['code' => 'RA 11313', 'title' => 'Safe Spaces Act', 'effective_date' => '2019-04-15'],
                ['code' => 'RA 10173', 'title' => 'Data Privacy Act', 'effective_date' => '2012-09-12']
            ]
        ];
    }
    
    include "components/header_template.php";
}

$laws = $stats['laws'] ?? [];
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Compliance Score -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="score-circle <?= $stats['compliance_score'] >= 80 ? 'excellent' : ($stats['compliance_score'] >= 60 ? 'warning' : 'danger') ?> mr-3">
                                <?= $stats['compliance_score'] ?>%
                            </div>
                            <div>
                                <h5 class="mb-0">Overall Compliance Score</h5>
                                <small class="text-muted">Based on Philippine labor law compliance</small>
                            </div>
                        </div>
                        <span class="badge badge-<?= $stats['compliance_score'] >= 80 ? 'success' : ($stats['compliance_score'] >= 60 ? 'warning' : 'danger') ?> p-2">
                            <?= $stats['compliance_score'] >= 80 ? 'Compliant' : ($stats['compliance_score'] >= 60 ? 'At Risk' : 'Non-Compliant') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info boxes -->
        <div class="row mb-2">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?= $stats['total_employees'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Compliant</span>
                        <span class="info-box-number"><?= $stats['compliant_count'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">At Risk</span>
                        <span class="info-box-number"><?= $stats['at_risk_count'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Non-Compliant</span>
                        <span class="info-box-number"><?= $stats['non_compliant_count'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Laws -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Philippine Labor Laws</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Law Code</th>
                                    <th>Title</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laws as $law): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($law['code']) ?></span></td>
                                    <td><?= htmlspecialchars($law['title']) ?></td>
                                    <td><?= date('M d, Y', strtotime($law['effective_date'])) ?></td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <a href="compliance.php?page=compliance" class="quick-action-btn">
                                    <i class="fas fa-balance-scale text-primary"></i>
                                    <span>Check Compliance</span>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="views/incidents.php" class="quick-action-btn">
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                    <span>Report Incident</span>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="views/policy_admin.php" class="quick-action-btn">
                                    <i class="fas fa-file-contract text-info"></i>
                                    <span>Manage Policies</span>
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="views/reports.php" class="quick-action-btn">
                                    <i class="fas fa-clipboard-check text-success"></i>
                                    <span>Generate Report</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "components/footer_template.php"; ?>
