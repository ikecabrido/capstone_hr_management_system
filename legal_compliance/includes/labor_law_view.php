<!-- Labor Law Compliance View Template -->
<!-- Alert Messages -->
<?php if (!empty($message)): ?>
    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible auto-dismiss" data-auto-dismiss="3000">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Dashboard Summary Cards - Matching Dashboard Style -->
<div class="row equal-height mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Total Items</div>
                    <div class="number text-primary"><?= number_format($stats['total']) ?></div>
                </div>
                <div class="icon text-primary"><i class="fas fa-list"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Compliant</div>
                    <div class="number text-success"><?= number_format($stats['compliant']) ?></div>
                </div>
                <div class="icon text-success"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Pending</div>
                    <div class="number text-warning"><?= number_format($stats['pending']) ?></div>
                </div>
                <div class="icon text-warning"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Overdue</div>
                    <div class="number text-danger"><?= number_format($stats['overdue']) ?></div>
                </div>
                <div class="icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Compliance Rate</div>
                    <div class="number <?= $complianceRate >= 80 ? 'text-success' : 'text-danger' ?>"><?= $complianceRate ?>%</div>
                </div>
                <div class="icon <?= $complianceRate >= 80 ? 'text-success' : 'text-danger' ?>"><i class="fas fa-percentage"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="summary-card">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <div class="label">Details</div>
                    <div class="number text-info" style="font-size: 1rem;"><?= $stats['compliant'] ?>/<?= $stats['total'] ?></div>
                </div>
                <div class="icon text-info"><i class="fas fa-info-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- View Navigation Tabs -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills" id="laborLawTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentView === 'checklist' ? 'active' : '' ?>" data-tab="checklist" href="?view=checklist">
                            <i class="fas fa-list-alt mr-1"></i> Compliance Checklist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentView === 'alerts' ? 'active' : '' ?>" data-tab="alerts" href="?view=alerts">
                            <i class="fas fa-bell mr-1"></i> Alerts & Deadlines
                            <?php if (count($overdueItems) > 0): ?>
                                <span class="badge badge-danger ml-1"><?= count($overdueItems) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Compliance Form (PHP dependent) -->
<?php if ($showAddForm): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i> Add New Compliance Item
                </h3>
                <div class="card-tools">
                    <a href="labor_law_compliance.php" class="btn btn-light btn-sm">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" class="labor-law-form" data-action="add" id="addComplianceForm">
                <input type="hidden" name="form_protection_token" value="<?php echo FormProtection::getToken('labor_law_compliance_add'); ?>">
                <div class="card-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compliance ID</label>
                                <input type="text" name="compliance_id" class="form-control" placeholder="e.g., SSS-001" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Requirement Name</label>
                                <input type="text" name="requirement_name" class="form-control" placeholder="e.g., SSS Registration" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="Government Contributions">Government Contributions</option>
                                    <option value="Employee Benefits">Employee Benefits</option>
                                    <option value="Legal Documentation">Legal Documentation</option>
                                    <option value="Mandatory Reports">Mandatory Reports</option>
                                    <option value="Workplace Safety">Workplace Safety</option>
                                    <option value="Tax Compliance">Tax Compliance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frequency</label>
                                <select name="frequency" class="form-control" required>
                                    <option value="">Select Frequency</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Semi-Annual">Semi-Annual</option>
                                    <option value="Annual">Annual</option>
                                    <option value="One-time">One-time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deadline</label>
                                <input type="date" name="deadline" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Responsible Person</label>
                                <select name="responsible_person" class="form-control" required>
                                    <option value="">Select Person</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?= $employee['id'] ?>"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detailed description of the compliance requirement"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Supporting Documents</label>
                        <input type="file" name="documents[]" class="form-control-file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Upload relevant documents (PDF, DOC, Images)</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Compliance Item
                    </button>
                    <a href="labor_law_compliance.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- VIEW: Checklist -->
<?php if ($currentView === 'checklist'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-check mr-2"></i>Compliance Checklist
                        </h3>
                        <button type="button" class="btn btn-primary btn-sm" data-labor-law-modal-target="#addComplianceModal">
                            <i class="fas fa-plus mr-1"></i> Add New
                        </button>
                    </div>
                    
                    <!-- Filters -->
                    <div class="mt-3">
                        <form method="GET" class="form-inline">
                            <input type="hidden" name="view" value="checklist">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="Compliant" <?= ($_GET['status'] ?? '') === 'Compliant' ? 'selected' : '' ?>>Compliant</option>
                                <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Overdue" <?= ($_GET['status'] ?? '') === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                                <option value="Not Applicable" <?= ($_GET['status'] ?? '') === 'Not Applicable' ? 'selected' : '' ?>>Not Applicable</option>
                            </select>
                            <select name="category" class="form-control" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <option value="Government Contributions" <?= ($_GET['category'] ?? '') === 'Government Contributions' ? 'selected' : '' ?>>Government Contributions</option>
                                <option value="Employee Benefits" <?= ($_GET['category'] ?? '') === 'Employee Benefits' ? 'selected' : '' ?>>Employee Benefits</option>
                                <option value="Legal Documentation" <?= ($_GET['category'] ?? '') === 'Legal Documentation' ? 'selected' : '' ?>>Legal Documentation</option>
                                <option value="Mandatory Reports" <?= ($_GET['category'] ?? '') === 'Mandatory Reports' ? 'selected' : '' ?>>Mandatory Reports</option>
                                <option value="Workplace Safety" <?= ($_GET['category'] ?? '') === 'Workplace Safety' ? 'selected' : '' ?>>Workplace Safety</option>
                                <option value="Tax Compliance" <?= ($_GET['category'] ?? '') === 'Tax Compliance' ? 'selected' : '' ?>>Tax Compliance</option>
                            </select>
                        </form>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive data-grid-scroll-both">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-dark table-fixed-header">
                                <tr>
                                    <th>Code</th>
                                    <th>Requirement</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Frequency</th>
                                    <th>Assigned To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($complianceItems) > 0): ?>
                                    <?php foreach ($complianceItems as $item): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($item['compliance_id'] ?? 'N/A') ?></strong></td>
                                            <td>
                                                <strong><?= htmlspecialchars($item['requirement_name']) ?></strong>
                                                <?php if (!empty($item['legal_basis'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($item['legal_basis']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="category-icon <?= getCategoryClass($item['category']) ?>">
                                                    <i class="fas <?= getCategoryIcon($item['category']) ?>"></i>
                                                </span>
                                                <span class="ml-2"><?= htmlspecialchars($item['category']) ?></span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= getStatusClass($item['status']) ?>">
                                                    <?= htmlspecialchars($item['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($item['due_date'])): ?>
                                                    <?php
                                                    $isOverdueItem = isOverdue($item['due_date'], $item['status']);
                                                    $isUrgentItem = isUrgent($item['due_date'], $item['status']);
                                                    $daysUntil = getDaysUntilDue($item['due_date'], $item['status']);
                                                    ?>
                                                    <span class="<?= $isOverdueItem ? 'text-danger' : ($isUrgentItem ? 'text-warning' : '') ?>">
                                                        <?= date('M d, Y', strtotime($item['due_date'])) ?>
                                                    </span>
                                                    <?php if ($isOverdueItem): ?>
                                                        <br><small class="text-danger">Overdue by <?= $daysUntil ?> days</small>
                                                    <?php elseif ($isUrgentItem): ?>
                                                        <br><small class="text-warning">Due in <?= $daysUntil ?> days</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($item['frequency'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $assignedName = 'Unassigned';
                                                foreach ($employees as $emp) {
                                                    if ($emp['id'] == $item['assigned_to']) {
                                                        $assignedName = $emp['first_name'] . ' ' . $emp['last_name'];
                                                        break;
                                                    }
                                                }
                                                echo htmlspecialchars($assignedName);
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" data-labor-law-modal-target="#viewModal<?= $item['id'] ?>" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary" data-labor-law-modal-target="#statusModal<?= $item['id'] ?>" title="Update Status">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" data-labor-law-modal-target="#uploadModal<?= $item['id'] ?>" title="Upload Document">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-labor-law-modal-target="#deleteModal<?= $item['id'] ?>" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>No compliance items found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- VIEW: Alerts & Deadlines -->
<?php if ($currentView === 'alerts'): ?>
    <div class="row">
        <!-- Overdue Items -->
        <div class="col-lg-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Overdue Requirements
                    </h3>
                </div>
                <div class="card-body p-0">
                    <?php if (count($overdueItems) > 0): ?>
                        <div class="table-responsive data-grid-scroll-vertical">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Requirement</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overdueItems as $item): ?>
                                        <?php
                                        $daysOverdue = getDaysUntilDue($item['due_date'], $item['status']);
                                        ?>
                                        <tr class="deadline-urgent">
                                            <td>
                                                <strong><?= htmlspecialchars($item['requirement_name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($item['compliance_id']) ?></small>
                                            </td>
                                            <td class="text-danger"><?= date('M d, Y', strtotime($item['due_date'])) ?></td>
                                            <td class="text-danger font-weight-bold"><?= $daysOverdue ?> days</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-labor-law-modal-target="#statusModal<?= $item['id'] ?>">
                                                    Update Status
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>No overdue requirements!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Deadlines -->
        <div class="col-lg-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-2"></i>Upcoming Deadlines (30 Days)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <?php if (count($upcomingDeadlines) > 0): ?>
                        <div class="table-responsive data-grid-scroll-vertical">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Requirement</th>
                                        <th>Due Date</th>
                                        <th>Days Left</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingDeadlines as $item): ?>
                                        <?php
                                        $daysLeft = getDaysUntilDue($item['due_date'], $item['status']);
                                        $isUrgentItem = isUrgent($item['due_date'], $item['status']);
                                        ?>
                                        <tr class="<?= $isUrgentItem ? 'deadline-warning' : '' ?>">
                                            <td>
                                                <strong><?= htmlspecialchars($item['requirement_name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($item['compliance_id']) ?></small>
                                            </td>
                                            <td class="<?= $isUrgentItem ? 'text-warning font-weight-bold' : '' ?>">
                                                <?= date('M d, Y', strtotime($item['due_date'])) ?>
                                            </td>
                                            <td class="<?= $isUrgentItem ? 'text-warning font-weight-bold' : '' ?>">
                                                <?= $daysLeft ?> days
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-labor-law-modal-target="#statusModal<?= $item['id'] ?>">
                                                    Update Status
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                            <p>No upcoming deadlines!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
