<!-- Legal & Compliance Dashboard - HTML Template -->
<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Legal & Compliance Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- ============================================ -->
            <!-- SECTION 1: SUMMARY CARDS (6 boxes) -->
            <!-- ============================================ -->
            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Total Employees</div>
                                <div class="number text-primary"><?= number_format($totalEmployees) ?></div>
                            </div>
                            <div class="icon text-primary"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Total Incidents</div>
                                <div class="number text-info"><?= number_format($totalIncidents) ?></div>
                            </div>
                            <div class="icon text-info"><i class="fas fa-exclamation-triangle"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Open Incidents</div>
                                <div class="number text-warning"><?= number_format($openIncidents) ?></div>
                            </div>
                            <div class="icon text-warning"><i class="fas fa-folder-open"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Resolved</div>
                                <div class="number text-success"><?= number_format($resolvedIncidents) ?></div>
                            </div>
                            <div class="icon text-success"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Active Risks</div>
                                <div class="number text-danger"><?= number_format($totalRisks) ?></div>
                            </div>
                            <div class="icon text-danger"><i class="fas fa-shield-alt"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="label">Compliance Rate</div>
                                <div class="number <?= $complianceRate >= 80 ? 'text-success' : 'text-danger' ?>"><?= $complianceRate ?>%</div>
                            </div>
                            <div class="icon <?= $complianceRate >= 80 ? 'text-success' : 'text-danger' ?>"><i class="fas fa-percentage"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SECTION 2: CHARTS (Incident & Risk) -->
            <!-- ============================================ -->
            <div class="row">
                <!-- Incident Overview Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>Incident Overview
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container mb-3" style="height: 300px;">
                                <canvas id="incidentChart"></canvas>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Submitted</small>
                                    <span class="badge badge-secondary"><?= $incidentByStatus['submitted'] ?? 0 ?></span>
                                </div>
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Review</small>
                                    <span class="badge badge-info"><?= $incidentByStatus['under_review'] ?? 0 ?></span>
                                </div>
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Investig.</small>
                                    <span class="badge badge-warning"><?= $incidentByStatus['investigation'] ?? 0 ?></span>
                                </div>
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Escalated</small>
                                    <span class="badge badge-danger"><?= $incidentByStatus['escalated'] ?? 0 ?></span>
                                </div>
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Resolved</small>
                                    <span class="badge badge-success"><?= $incidentByStatus['resolved'] ?? 0 ?></span>
                                </div>
                                <div class="col-4 mb-2 text-center">
                                    <small class="text-muted d-block">Closed</small>
                                    <span class="badge badge-dark"><?= $incidentByStatus['closed'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Risk Overview Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>Risk Overview
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="riskChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SECTION 3: Compliance & Recent Activities -->
            <!-- ============================================ -->
            <div class="row">
                <!-- Compliance Status -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-balance-scale mr-2"></i>Compliance Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php
                            $categoryData = [];
                            $excludedCategories = ['Health & Safety', 'Data Privacy', 'Payroll'];
                            
                            foreach ($complianceItems as $item) {
                                $cat = $item['category'];
                                
                                // Skip excluded categories
                                if (in_array($cat, $excludedCategories)) {
                                    continue;
                                }
                                
                                if (!isset($categoryData[$cat])) {
                                    $categoryData[$cat] = [
                                        'compliant' => 0,
                                        'pending' => 0,
                                        'overdue' => 0
                                    ];
                                }
                                
                                if ($item['status'] === 'Compliant') {
                                    $categoryData[$cat]['compliant'] = $item['count'];
                                } elseif ($item['status'] === 'Pending') {
                                    $categoryData[$cat]['pending'] = $item['count'];
                                } elseif ($item['status'] === 'Overdue' || $item['status'] === 'Non-Compliant' || $item['status'] === 'Non-compliant') {
                                    $categoryData[$cat]['overdue'] += $item['count'];
                                }
                            }
                            ?>
                            
                            <!-- Employee Contributions Status (Manual Section) -->
                            <div class="compliance-status <?= $contributionStats['pending'] > 0 ? 'pending' : 'compliant' ?>">
                                <div class="mr-3">
                                    <?php if ($contributionStats['pending'] > 0): ?>
                                        <i class="fas fa-exclamation-circle text-warning fa-2x"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong>Employee Contributions</strong><br>
                                    <small>Completed: <?= $contributionStats['completed'] ?> | Pending: <?= $contributionStats['pending'] ?></small>
                                </div>
                            </div>

                            <?php
                            if (empty($categoryData) && $contributionStats['completed'] == 0 && $contributionStats['pending'] == 0): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-balance-scale fa-3x mb-3"></i>
                                    <p>No compliance data available</p>
                                </div>
                            <?php else:
                                foreach ($categoryData as $cat => $data):
                                    $status = $data['overdue'] > 0 ? 'overdue' : ($data['pending'] > 0 ? 'pending' : 'compliant');
                            ?>
                                    <div class="compliance-status <?= $status ?>">
                                        <div class="mr-3">
                                            <?php if ($status === 'overdue'): ?>
                                                <i class="fas fa-times-circle text-danger fa-2x"></i>
                                            <?php elseif ($status === 'pending'): ?>
                                                <i class="fas fa-exclamation-circle text-warning fa-2x"></i>
                                            <?php else: ?>
                                                <i class="fas fa-check-circle text-success fa-2x"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($cat) ?></strong><br>
                                            <small>Compliant: <?= $data['compliant'] ?> | Pending: <?= $data['pending'] ?> | Overdue: <?= $data['overdue'] ?></small>
                                        </div>
                                    </div>
                            <?php 
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>Recent Activities
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive-scroll data-grid-scroll-vertical">
                                <table class="table table-striped table-fixed-header mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>User</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($recentActivities) > 0): ?>
                                            <?php foreach ($recentActivities as $activity): ?>
                                                <tr>
                                                    <td><small><?= date('M d, H:i', strtotime($activity['created_at'])) ?></small></td>
                                                    <td><small><?= htmlspecialchars($activity['user_name'] ?? $activity['user'] ?? 'System') ?></small></td>
                                                    <td><small><?= htmlspecialchars($activity['action'] ?? 'N/A') ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No recent activities</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SECTION 4: Quick Actions & Notifications -->
            <!-- ============================================ -->
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-2"></i>Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <button class="quick-action-btn" onclick="window.location.href='incident_reporting.php?create=1'">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <div>Report Incident</div>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="quick-action-btn" onclick="window.location.href='leave_management.php'">
                                        <i class="fas fa-calendar-check"></i>
                                        <div>Leave Requests</div>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="quick-action-btn" onclick="window.location.href='policy_documentation.php?view=list&action=add'">
                                        <i class="fas fa-file-upload"></i>
                                        <div>Upload Policy</div>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="quick-action-btn" onclick="window.location.href='incident_reporting.php'">
                                        <i class="fas fa-file-alt"></i>
                                        <div>View Reports</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications / Alerts -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-bell mr-2"></i>Notifications & Alerts
                            </h3>
                        </div>
                        <div class="card-body card-body-scroll">
                            <?php 
                            $hasAlerts = false;
                            
                            // Check for high risks
                            if (count($highRisks) > 0): 
                                $hasAlerts = true;
                                foreach ($highRisks as $risk): 
                            ?>
                                    <div class="alert-item danger">
                                        <strong><i class="fas fa-exclamation-triangle mr-2"></i>High Risk Alert</strong>
                                        <p class="mb-0"><?= htmlspecialchars($risk['title'] ?? 'Risk identified') ?></p>
                                        <small class="text-muted">Severity: <span class="risk-badge high">High</span></small>
                                    </div>
                            <?php 
                                endforeach; 
                            endif;

                            // Check for overdue requirements
                            if (count($overdueRequirements) > 0): 
                                $hasAlerts = true;
                                foreach ($overdueRequirements as $item): 
                            ?>
                                    <div class="alert-item danger">
                                        <strong><i class="fas fa-calendar-times mr-2"></i>Overdue Requirement</strong>
                                        <p class="mb-0"><?= htmlspecialchars($item['name'] ?? 'Compliance requirement overdue') ?></p>
                                        <small class="text-muted">Status: <span class="badge badge-danger"><?= htmlspecialchars($item['status']) ?></span> | Due: <?= date('M d, Y', strtotime($item['due_date'])) ?></small>
                                    </div>
                            <?php 
                                endforeach; 
                            endif;
                            
                            // Check for upcoming deadlines
                            if (count($upcomingDeadlines) > 0): 
                                $hasAlerts = true;
                                foreach ($upcomingDeadlines as $deadline): 
                            ?>
                                    <div class="alert-item">
                                        <strong><i class="fas fa-clock mr-2"></i>Upcoming Deadline</strong>
                                        <p class="mb-0"><?= htmlspecialchars($deadline['message'] ?? 'Compliance item due') ?></p>
                                        <small class="text-muted">Priority: <?= strtoupper($deadline['priority'] ?? 'medium') ?></small>
                                    </div>
                            <?php 
                                endforeach; 
                            endif;
                            
                            // Check for open incidents
                            if ($openIncidents > 3): 
                                $hasAlerts = true;
                            ?>
                                <div class="alert-item">
                                    <strong><i class="fas fa-folder-open mr-2"></i>Open Incidents</strong>
                                    <p class="mb-0">You have <?= $openIncidents ?> open incidents requiring attention.</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!$hasAlerts): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p>No pending alerts</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================ -->
            <!-- SECTION 5: Recent Incidents List -->
            <!-- ============================================ -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header card-header-custom">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>Recent Incidents (Latest 5)
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive-scroll data-grid-scroll-both">
                                <table class="table table-striped table-fixed-header mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Employee</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($recentIncidents) > 0): ?>
                                            <?php foreach ($recentIncidents as $incident): ?>
                                                <tr>
                                                    <td>#<?= $incident['id'] ?></td>
                                                    <td><?= htmlspecialchars($incident['title'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <small>
                                                            <?= htmlspecialchars(($incident['first_name'] ?? '') . ' ' . ($incident['last_name'] ?? '')) ?>
                                                            <?php if (!empty($incident['employee_no'])): ?>
                                                                <br><span class="text-muted"><?= $incident['employee_no'] ?></span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </td>
                                                    <td><small><?= htmlspecialchars($incident['employee_category'] ?? 'N/A') ?></small></td>
                                                    <td>
                                                        <?php 
                                                        $statusLabel = getIncidentStatusLabel($incident['status']);
                                                        $statusClass = getIncidentStatusClass($incident['status']);
                                                        $statusIcon = getIncidentStatusIcon($incident['status']);
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>">
                                                            <i class="fas fa-<?= $statusIcon ?> mr-1"></i>
                                                            <?= $statusLabel ?>
                                                        </span>
                                                    </td>
                                                    <td><small><?= date('M d, Y', strtotime($incident['created_at'])) ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No incidents reported</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>