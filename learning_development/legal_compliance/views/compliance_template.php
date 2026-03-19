<?php
/**
 * Compliance Template
 * This template displays the compliance monitoring dashboard
 * Variables expected: $stats, $categories, $riskFlags, $employees, $laws
 * $totalEmployees, $compliantCount, $atRiskCount, $nonCompliantCount
 * $overallScore, $criticalIssues, $highRisks, $pendingAcks, $activeCases
 * $categoryScores
 */
?>

<!-- Main content starts here -->
<section class="content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row mb-3">
            <div class="col-12">
                <h2 class="text-dark" style="font-weight: 700;">
                    <i class="fas fa-balance-scale mr-2"></i>Compliance Monitoring
                </h2>
                <p class="text-muted">Monitor and manage employee compliance with Philippine labor laws</p>
            </div>
        </div>

        <!-- Compliance Score Alert -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="compliance-score-circle <?= $overallScore >= 80 ? 'excellent' : ($overallScore >= 60 ? 'warning' : 'danger') ?> mr-3">
                                <?= $overallScore ?>%
                            </div>
                            <div>
                                <h5 class="mb-0">Overall Compliance Score</h5>
                                <small class="text-muted">Based on Philippine labor law compliance</small>
                            </div>
                        </div>
                        <span class="badge badge-lg badge-<?= $overallScore >= 80 ? 'success' : ($overallScore >= 60 ? 'warning' : 'danger') ?>">
                            <?= $overallScore >= 80 ? 'Compliant' : ($overallScore >= 60 ? 'At Risk' : 'Non-Compliant') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="row mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box clickable" data-card-type="total-employees" data-title="Total Employees" data-subtitle="<?= $totalEmployees ?> registered employees" data-description="View all employee compliance records in the system." data-icon-class="bg-primary">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?= $totalEmployees ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box clickable" data-card-type="compliant" data-title="Compliant Employees" data-subtitle="<?= $compliantCount ?> fully compliant" data-description="Employees with 80% or higher compliance score." data-icon-class="bg-success">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Compliant</span>
                        <span class="info-box-number"><?= $compliantCount ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box clickable" data-card-type="at-risk" data-title="At Risk Employees" data-subtitle="<?= $atRiskCount ?> at risk" data-description="Employees with 60-79% compliance score requiring attention." data-icon-class="bg-warning">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">At Risk</span>
                        <span class="info-box-number"><?= $atRiskCount ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box clickable" data-card-type="non-compliant" data-title="Non-Compliant Employees" data-subtitle="<?= $nonCompliantCount ?> non-compliant" data-description="Employees with below 60% compliance score requiring immediate action." data-icon-class="bg-danger">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Non-Compliant</span>
                        <span class="info-box-number"><?= $nonCompliantCount ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Indicators Row -->
        <div class="row mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Critical Issues</span>
                        <span class="info-box-number"><?= $criticalIssues ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">High Risks</span>
                        <span class="info-box-number"><?= $highRisks ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Acks</span>
                        <span class="info-box-number"><?= $pendingAcks ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-briefcase"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Cases</span>
                        <span class="info-box-number"><?= $activeCases ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout: Categories & Risk Flags -->
        <div class="row mb-3">
            <!-- Category Scores -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Compliance by Category</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($categoryScores)): ?>
                            <?php foreach ($categoryScores as $cat): ?>
                                <div class="progress-group mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="font-weight-bold"><?= htmlspecialchars($cat['name'] ?? 'Unknown') ?></span>
                                        <span class="<?= ($cat['score'] ?? 0) >= 80 ? 'score-excellent' : (($cat['score'] ?? 0) >= 60 ? 'score-warning' : 'score-danger') ?>">
                                            <?= $cat['score'] ?? 0 ?>%
                                        </span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar <?= ($cat['score'] ?? 0) >= 80 ? 'bg-success' : (($cat['score'] ?? 0) >= 60 ? 'bg-warning' : 'bg-danger') ?>" 
                                             role="progressbar" 
                                             style="width: <?= $cat['score'] ?? 0 ?>%" 
                                             aria-valuenow="<?= $cat['score'] ?? 0 ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">No category data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Risk Flags -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Active Risk Flags</h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($riskFlags)): ?>
                            <div class="table-responsive p-0">
                                <table class="table table-hover table-compliance">
                                    <thead>
                                        <tr>
                                            <th>Severity</th>
                                            <th>Employee</th>
                                            <th>Issue</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($riskFlags as $flag): ?>
                                            <tr class="risk-flag-<?= strtolower($flag['severity']) ?>" onclick="showRiskFlagDetails(<?= $flag['id'] ?>)" style="cursor: pointer;">
                                                <td>
                                                    <span class="badge badge-<?= $flag['severity'] ?>">
                                                        <?= htmlspecialchars($flag['severity']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars(trim(($flag['first_name'] ?? '') . ' ' . ($flag['last_name'] ?? ''))) ?: 'N/A' ?></td>
                                                <td><?= htmlspecialchars($flag['rule_name'] ?? ($flag['issue'] ?? 'N/A')) ?></td>
                                                <td><?= date('M d, Y', strtotime($flag['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle text-success"></i>
                                <p>No active risk flags</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Philippine Laws -->
        <div class="row mb-3">
            <div class="col-12">
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
                                    <th>Category</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Compliance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($laws)): 
                                    // Remove duplicates based on law code
                                    $uniqueLaws = [];
                                    foreach ($laws as $law) {
                                        $code = $law['code'] ?? '';
                                        if (!isset($uniqueLaws[$code])) {
                                            $uniqueLaws[$code] = $law;
                                        }
                                    }
                                ?>
                                    <?php foreach ($uniqueLaws as $law): ?>
                                    <tr>
                                        <td><span class="badge badge-primary"><?= htmlspecialchars($law['code'] ?? 'N/A') ?></span></td>
                                        <td><?= htmlspecialchars($law['title'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($law['category'] ?? 'General') ?></td>
                                        <td><?= !empty($law['effective_date']) ? date('M d, Y', strtotime($law['effective_date'])) : 'N/A' ?></td>
                                        <td><span class="badge badge-success">Active</span></td>
                                        <td>
                                            <?php 
                                                $lawScore = $law['compliance_score'] ?? 100;
                                                $statusClass = $lawScore >= 80 ? 'success' : ($lawScore >= 60 ? 'warning' : 'danger');
                                            ?>
                                            <span class="badge badge-<?= $statusClass ?>"><?= $lawScore ?>%</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No laws registered</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Compliance Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employee Compliance Status</h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <a href="?page=compliance&filter=all" class="btn btn-sm btn-outline-primary filter-btn <?= (($GLOBALS['currentFilter'] ?? 'all') === 'all') ? 'active' : '' ?>">All</a>
                                <a href="?page=compliance&filter=compliant" class="btn btn-sm btn-outline-success filter-btn <?= (($GLOBALS['currentFilter'] ?? 'all') === 'compliant') ? 'active' : '' ?>">Compliant</a>
                                <a href="?page=compliance&filter=at-risk" class="btn btn-sm btn-outline-warning filter-btn <?= (($GLOBALS['currentFilter'] ?? 'all') === 'at-risk') ? 'active' : '' ?>">At Risk</a>
                                <a href="?page=compliance&filter=non-compliant" class="btn btn-sm btn-outline-danger filter-btn <?= (($GLOBALS['currentFilter'] ?? 'all') === 'non-compliant') ? 'active' : '' ?>">Non-Compliant</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-compliance" id="employeeComplianceTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Compliance Score</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($employees)): ?>
                                    <?php foreach ($employees as $emp): 
                                        $score = $emp['overall_score'] ?? 0;
                                        $empStatus = $emp['compliance_status'] ?? ($score >= 80 ? 'compliant' : ($score >= 60 ? 'at_risk' : 'non_compliant'));
                                        $status = $empStatus == 'compliant' ? 'compliant' : ($empStatus == 'at_risk' ? 'at-risk' : 'non-compliant');
                                        $statusLabel = $empStatus == 'compliant' ? 'Compliant' : ($empStatus == 'at_risk' ? 'At Risk' : 'Non-Compliant');
                                    ?>
                                    <tr class="employee-compliance-row" data-status="<?= $status ?>" data-employee-id="<?= $emp['id'] ?>">
                                        <td><?= htmlspecialchars($emp['employee_id'] ?? $emp['id'] ?? 'N/A') ?></td>
                                        <td><a href="#" class="employee-link text-primary" data-employee-id="<?= $emp['id'] ?>"><?= htmlspecialchars(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? '')) ?></a></td>
                                        <td><?= htmlspecialchars($emp['department'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($emp['position'] ?? 'N/A') ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2" style="height: 8px; width: 60px;">
                                                    <div class="progress-bar <?= $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-warning' : 'bg-danger') ?>" 
                                                         role="progressbar" 
                                                         style="width: <?= $score ?>%">
                                                    </div>
                                                </div>
                                                <span class="<?= $score >= 80 ? 'score-excellent' : ($score >= 60 ? 'score-warning' : 'score-danger') ?>">
                                                    <?= $score ?>%
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-<?= $status ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td><?= !empty($emp['last_checked']) ? date('M d, Y', strtotime($emp['last_checked'])) : (isset($emp['last_updated']) && !empty($emp['last_updated']) ? date('M d, Y', strtotime($emp['last_updated'])) : 'N/A') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="window.currentEmployeeId = <?= $emp['id'] ?>; showEmployeeDetails(<?= $emp['id'] ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if ($status !== 'compliant'): ?>
                                            <button class="btn btn-sm btn-info" onclick="window.currentEmployeeId = <?= $emp['id'] ?>; sendReminderToEmployee()">
                                                <i class="fas fa-bell"></i> Remind
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No employee data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="compliance-legend">
                    <div class="legend-item">
                        <span class="legend-dot compliant"></span>
                        <span>Compliant (≥80%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot at-risk"></span>
                        <span>At Risk (60-79%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot non-compliant"></span>
                        <span>Non-Compliant (<60%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1" role="dialog" aria-labelledby="employeeDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="employeeDetailsModalLabel">Employee Compliance Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body emp-compliance-modal" id="employeeDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading employee details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Card Details Modal -->
<div class="modal fade" id="cardDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cardDetailsModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="cardDetailsModalLabel">Card Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div id="cardDetailsIcon" class="info-box-icon bg-primary elevation-1 mx-auto mb-3">
                        <i class="fas fa-info"></i>
                    </div>
                    <h4 id="cardDetailsTitle">Card Title</h4>
                    <p class="text-muted" id="cardDetailsSubtitle"></p>
                    <hr>
                    <p id="cardDetailsDescription"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Risk Flag Details Modal -->
<div class="modal fade" id="riskFlagDetailsModal" tabindex="-1" role="dialog" aria-labelledby="riskFlagDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="riskFlagDetailsModalLabel">Risk Flag Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="riskFlagDetailsContent">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading risk flag details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="escalateRiskFlag()">Escalate</button>
                <button type="button" class="btn btn-success" onclick="resolveRiskFlag()">Mark Resolved</button>
                <button type="button" class="btn btn-info" onclick="sendReminderToEmployee()">Send Reminder</button>
            </div>
        </div>
    </div>
</div>

<!-- Send Reminder Modal -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" role="dialog" aria-labelledby="sendReminderModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="sendReminderModalLabel">Send Compliance Reminder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reminderForm">
                    <input type="hidden" id="reminderEmployeeId" name="employee_id">
                    <div class="form-group">
                        <label for="reminderSubject">Subject</label>
                        <input type="text" class="form-control" id="reminderSubject" name="subject" value="Compliance Reminder - Action Required">
                    </div>
                    <div class="form-group">
                        <label for="reminderMessage">Message</label>
                        <textarea class="form-control" id="reminderMessage" name="message" rows="4" placeholder="Enter reminder message...">Please complete your pending compliance requirements as soon as possible.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="submitReminder()">Send Reminder</button>
            </div>
        </div>
    </div>
</div>
