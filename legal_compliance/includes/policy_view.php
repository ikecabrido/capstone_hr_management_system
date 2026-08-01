<!-- Policy Documentation View Template -->

<!-- Alert Messages -->
<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible auto-dismiss" data-auto-dismiss="3000">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Dashboard Summary Cards -->
<div class="row equal-height mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Total Policies</div>
                    <div class="number text-primary"><?= number_format($stats['total']) ?></div>
                </div>
                <div class="icon text-primary"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Active Policies</div>
                    <div class="number text-success"><?= number_format($stats['active']) ?></div>
                </div>
                <div class="icon text-success"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Pending Ack.</div>
                    <div class="number text-warning"><?= number_format($stats['pending_ack']) ?></div>
                </div>
                <div class="icon text-warning"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Recent Updates</div>
                    <div class="number text-info"><?= number_format($stats['recent_updates']) ?></div>
                </div>
                <div class="icon text-info"><i class="fas fa-history"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- View Navigation Tabs -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills" id="policyTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentView === 'list' ? 'active' : '' ?>" data-tab="list" href="?view=list">
                            <i class="fas fa-list-alt mr-1"></i> Policy List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentView === 'acknowledgments' ? 'active' : '' ?>" data-tab="acknowledgments" href="?view=acknowledgments">
                            <i class="fas fa-check-double mr-1"></i> Acknowledgments
                            <?php if (count($pendingAcknowledgments) > 0): ?>
                                <span class="badge badge-warning ml-1"><?= count($pendingAcknowledgments) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentView === 'history' ? 'active' : '' ?>" data-tab="history" href="?view=history">
                            <i class="fas fa-history mr-1"></i> Recent Updates
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- VIEW: Policy List -->
<?php if ($currentView === 'list'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPolicyModal">
                                <i class="fas fa-plus mr-1"></i> Add Policy
                            </button>
                        </div>
                        <form method="GET" class="form-inline">
                            <input type="hidden" name="view" value="list">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search policies..." value="<?= htmlspecialchars($filters['search']) ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            <select name="category" class="form-control form-control-sm ml-2" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= $filters['category'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="status" class="form-control form-control-sm ml-2" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="unread" <?= $filters['status'] === 'unread' ? 'selected' : '' ?>>Unread</option>
                                <option value="acknowledged" <?= $filters['status'] === 'acknowledged' ? 'selected' : '' ?>>Acknowledged</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 500px;">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Department</th>
                                <th>Version</th>
                                <th>Last Updated</th>
                                <th>Acknowledgments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($policies) > 0): ?>
                                <?php foreach ($policies as $policy): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($policy['title']) ?></strong>
                                            <?php if ($policy['requires_acknowledgment']): ?>
                                                <span class="badge badge-info ml-1">Required</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($policy['category']) ?></td>
                                        <td><?= htmlspecialchars($policy['department_owner'] ?? 'N/A') ?></td>
                                        <td><span class="badge badge-secondary">v<?= $policy['version_number'] ?></span></td>
                                        <td><?= date('M d, Y', strtotime($policy['updated_at'])) ?></td>
                                        <td>
                                            <?php if ($policy['unread_count'] > 0): ?>
                                                <span class="badge badge-warning"><?= $policy['unread_count'] ?> Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">All Read</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewPolicyModal<?= $policy['id'] ?>" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPolicyModal<?= $policy['id'] ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deletePolicyModal<?= $policy['id'] ?>" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>No policies found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- VIEW: Acknowledgments -->
<?php if ($currentView === 'acknowledgments'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Pending Acknowledgments</h3>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 500px;">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Policy</th>
                                <th>Status</th>
                                <th>Date Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($pendingAcknowledgments) > 0): ?>
                                <?php foreach ($pendingAcknowledgments as $ack): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($ack['first_name'] . ' ' . $ack['last_name']) ?></strong>
                                            <br><small class="text-muted"><?= htmlspecialchars($ack['employee_no']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($ack['policy_title']) ?></td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td><?= date('M d, Y', strtotime($ack['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success remind-btn" 
                                                    title="Send Reminder"
                                                    data-ack-id="<?= $ack['id'] ?>"
                                                    data-employee-id="<?= $ack['employee_id'] ?>"
                                                    data-employee-name="<?= htmlspecialchars($ack['first_name'] . ' ' . $ack['last_name']) ?>"
                                                    data-policy-id="<?= $ack['policy_id'] ?>"
                                                    data-policy-title="<?= htmlspecialchars($ack['policy_title']) ?>"
                                                    data-toggle="modal" 
                                                    data-target="#reminderModal">
                                                <i class="fas fa-bell"></i> Remind
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                        <p>All policies have been acknowledged!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- VIEW: Recent Updates -->
<?php if ($currentView === 'history'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recently Updated Policies</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($recentUpdates as $update): ?>
                            <div class="time-label">
                                <span class="bg-primary"><?= date('M d, Y', strtotime($update['updated_at'])) ?></span>
                            </div>
                            <div>
                                <i class="fas fa-file-alt bg-info"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">
                                        <span class="text-dark font-weight-bold">
                                            <?= htmlspecialchars($update['title']) ?>
                                        </span>
                                        <span class="badge badge-secondary ml-2">v<?= $update['version_number'] ?></span>
                                    </h3>
                                    <div class="timeline-body">
                                        <?= htmlspecialchars($update['change_notes'] ?? $update['description']) ?>
                                    </div>
                                    <div class="timeline-footer">
                                        <span class="text-muted"><?= htmlspecialchars($update['category']) ?> - <?= htmlspecialchars($update['department_owner'] ?? 'N/A') ?></span>
                                        <button class="btn btn-sm btn-outline-primary ml-2 view-versions-btn" 
                                                data-policy-id="<?= $update['id'] ?>"
                                                data-policy-title="<?= htmlspecialchars($update['title']) ?>">
                                            <i class="fas fa-history mr-1"></i> View Past Versions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Version History Modal -->
    <div class="modal fade" id="versionHistoryModal" tabindex="-1" role="dialog" aria-labelledby="versionHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="versionHistoryModalLabel">
                        <i class="fas fa-history mr-2"></i>Version History
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="versionHistoryContent">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Loading version history...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle click on "View Past Versions" button
        const viewVersionsBtns = document.querySelectorAll('.view-versions-btn');
        viewVersionsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const policyId = this.getAttribute('data-policy-id');
                const policyTitle = this.getAttribute('data-policy-title');
                
                // Update modal title
                document.getElementById('versionHistoryModalLabel').innerHTML = 
                    '<i class="fas fa-history mr-2"></i>Version History: ' + policyTitle;
                
                // Show modal
                $('#versionHistoryModal').modal('show');
                
                // Load version history via AJAX
                fetch('policy_documentation.php?action=get_versions&policy_id=' + policyId)
                    .then(response => response.json())
                    .then(data => {
                        const content = document.getElementById('versionHistoryContent');
                        if (data.success && data.versions.length > 0) {
                            let html = '<div class="table-responsive"><table class="table table-striped">';
                            html += '<thead><tr><th>Version</th><th>File</th><th>Change Notes</th><th>Updated By</th><th>Date</th><th>Actions</th></tr></thead>';
                            html += '<tbody>';
                            
                            data.versions.forEach(version => {
                                const isCurrent = version.is_current == 1;
                                const badgeClass = isCurrent ? 'badge-success' : 'badge-secondary';
                                const badgeText = isCurrent ? 'Current' : 'v' + version.version_number;
                                
                                html += '<tr>';
                                html += '<td><span class="badge ' + badgeClass + '">' + badgeText + '</span></td>';
                                html += '<td>' + (version.file_name || 'N/A') + '</td>';
                                html += '<td>' + (version.change_notes || 'No notes') + '</td>';
                                html += '<td>' + (version.updated_by_name || 'Unknown') + '</td>';
                                html += '<td>' + new Date(version.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) + '</td>';
                                html += '<td>';
                                if (version.file_path) {
                                    if (version.file_exists) {
                                        // Ensure the path is correct and properly encoded
                                        let downloadUrl = encodeURI(version.file_path);
                                        html += '<a href="' + downloadUrl + '" class="btn btn-sm btn-primary" target="_blank" download="' + (version.file_name || 'policy_version') + '"><i class="fas fa-download mr-1"></i>Download</a>';
                                    } else {
                                        html += '<span class="badge badge-warning" title="File missing on server"><i class="fas fa-exclamation-triangle"></i> Missing</span>';
                                    }
                                } else {
                                    html += '<span class="text-muted">No file</span>';
                                }
                                html += '</td>';
                                html += '</tr>';
                            });
                            
                            html += '</tbody></table></div>';
                            content.innerHTML = html;
                        } else {
                            content.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-folder-open fa-3x mb-3"></i><p>No version history available</p></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('versionHistoryContent').innerHTML = 
                            '<div class="alert alert-danger">Error loading version history. Please try again.</div>';
                    });
            });
        });
    });
    </script>
<?php endif; ?>

<!-- Include Modals -->
<?php include __DIR__ . '/policy_modals.php'; ?>
