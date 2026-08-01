<!-- Labor Law Compliance Modals -->
<?php
$laborLawStatusModalIds = [];
?>

<!-- Add Compliance Modal -->
<div class="modal fade" id="addComplianceModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Add New Compliance Item</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" class="labor-law-form" data-action="add" id="addComplianceForm">
                <input type="hidden" name="form_protection_token" value="<?php echo FormProtection::getToken('labor_law_compliance_add'); ?>">
                <div class="modal-body">
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
                                <select name="frequency" class="form-control">
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Semi-Annual">Semi-Annual</option>
                                    <option value="Yearly">Yearly</option>
                                    <option value="One-time">One-time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Compliant">Compliant</option>
                                    <option value="Overdue">Overdue</option>
                                    <option value="Not Applicable">Not Applicable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To</label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">Select Employee</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Legal Basis</label>
                                <input type="text" name="legal_basis" class="form-control" placeholder="e.g., RA 8282">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Compliance Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modals for Compliance Items -->
<?php foreach ($complianceItems as $item): ?>
    <?php $laborLawStatusModalIds[(int) $item['id']] = true; ?>
    <div class="modal fade" id="statusModal<?= $item['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" class="labor-law-form" data-action="update_status">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <div class="form-group">
                            <label>Requirement</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($item['requirement_name']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>New Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Compliant" <?= $item['status'] === 'Compliant' ? 'selected' : '' ?>>Compliant</option>
                                <option value="Pending" <?= $item['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Overdue" <?= $item['status'] === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                                <option value="Not Applicable" <?= $item['status'] === 'Not Applicable' ? 'selected' : '' ?>>Not Applicable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Upload Document Modal -->
    <div class="modal fade" id="uploadModal<?= $item['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" class="labor-law-form" data-action="upload_attachment">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="upload_attachment">
                        <input type="hidden" name="compliance_id" value="<?= $item['id'] ?>">
                        <div class="form-group">
                            <label>Select File</label>
                            <input type="file" name="attachment" class="form-control" required>
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?= $item['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h4 class="modal-title">Confirm Delete</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" class="labor-law-form" data-action="delete">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <p>Are you sure you want to delete this compliance item?</p>
                        <strong><?= htmlspecialchars($item['requirement_name']) ?></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal<?= $item['id'] ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h4 class="modal-title">Compliance Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Compliance ID:</strong> <?= htmlspecialchars($item['compliance_id'] ?? 'N/A') ?></p>
                            <p><strong>Requirement:</strong> <?= htmlspecialchars($item['requirement_name']) ?></p>
                            <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
                            <p><strong>Status:</strong> <span class="status-badge status-<?= getStatusClass($item['status']) ?>"><?= htmlspecialchars($item['status']) ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> <?= !empty($item['due_date']) ? date('M d, Y', strtotime($item['due_date'])) : 'N/A' ?></p>
                            <p><strong>Frequency:</strong> <?= htmlspecialchars($item['frequency'] ?? 'N/A') ?></p>
                            <p><strong>Legal Basis:</strong> <?= htmlspecialchars($item['legal_basis'] ?? 'N/A') ?></p>
                            <p><strong>Last Checked:</strong> <?= !empty($item['last_checked']) ? date('M d, Y H:i', strtotime($item['last_checked'])) : 'Never' ?></p>
                        </div>
                    </div>
                    <?php if (!empty($item['description'])): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <p><strong>Description:</strong></p>
                                <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($item['remarks'])): ?>
                        <div class="row mt-2">
                            <div class="col-12">
                                <p><strong>Remarks:</strong></p>
                                <p><?= nl2br(htmlspecialchars($item['remarks'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Attachments -->
                    <?php
                    $itemAttachments = [];
                    try {
                        $itemAttachments = getComplianceAttachments($db, $item['id']);
                    } catch (Throwable $e) {
                        error_log('labor_law_modals attachments: ' . $e->getMessage());
                    }
                    ?>
                    <?php if (count($itemAttachments) > 0): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Attachments</h5>
                                <ul class="list-group">
                                    <?php foreach ($itemAttachments as $att): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file mr-2"></i>
                                                <?= htmlspecialchars($att['file_name']) ?>
                                                <small class="text-muted">(<?= formatFileSize($att['file_size']) ?>)</small>
                                            </span>
                                            <a href="<?= htmlspecialchars($att['file_path']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Status Update Modals for Overdue Items -->
<?php foreach ($overdueItems as $item): ?>
    <?php if (!empty($laborLawStatusModalIds[(int) $item['id']])) {
        continue;
    } ?>
    <div class="modal fade" id="statusModal<?= $item['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" class="labor-law-form" data-action="update_status">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <div class="form-group">
                            <label>Requirement</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($item['requirement_name']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>New Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Compliant" <?= $item['status'] === 'Compliant' ? 'selected' : '' ?>>Compliant</option>
                                <option value="Pending" <?= $item['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Overdue" <?= $item['status'] === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                                <option value="Not Applicable" <?= $item['status'] === 'Not Applicable' ? 'selected' : '' ?>>Not Applicable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Status Update Modals for Upcoming Deadlines -->
<?php foreach ($upcomingDeadlines as $item): ?>
    <?php if (!empty($laborLawStatusModalIds[(int) $item['id']])) {
        continue;
    } ?>
    <div class="modal fade" id="statusModal<?= $item['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" class="labor-law-form" data-action="update_status">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <div class="form-group">
                            <label>Requirement</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($item['requirement_name']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>New Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Compliant" <?= $item['status'] === 'Compliant' ? 'selected' : '' ?>>Compliant</option>
                                <option value="Pending" <?= $item['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Overdue" <?= $item['status'] === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                                <option value="Not Applicable" <?= $item['status'] === 'Not Applicable' ? 'selected' : '' ?>>Not Applicable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
