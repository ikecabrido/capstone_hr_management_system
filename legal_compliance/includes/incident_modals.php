<?php
/**
 * Incident Modals
 * Contains all modals for incident management
 */
?>

<!-- Create Incident Modal -->
<div class="modal fade" id="modal-create-incident" tabindex="-1" role="dialog" aria-labelledby="modal-create-incident-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modal-create-incident-label">
                    <i class="fas fa-plus"></i> Report New Incident
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-create-incident" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="incident-title">Incident Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="incident-title" name="title" required placeholder="Brief description of the incident">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incident-type">Incident Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="incident-type" name="incident_type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($incidentTypes as $typeKey => $typeLabel): ?>
                                        <?php
                                        $optValue = is_array($typeLabel) ? ($typeLabel['type_name'] ?? $typeKey) : $typeKey;
                                        $optText = is_array($typeLabel) ? ($typeLabel['type_name'] ?? $optValue) : $typeLabel;
                                        ?>
                                        <option value="<?= htmlspecialchars((string) $optValue) ?>"><?= htmlspecialchars((string) $optText) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incident-category">Category <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="incident-category" name="type" required>
                                    <option value="">Select Category</option>
                                    <option value="workplace_safety">Workplace Safety</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="policy_violation">Policy Violation</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incident-severity">Severity <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="incident-severity" name="severity" required>
                                    <option value="">Select Severity</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incident-location">Location</label>
                                <input type="text" class="form-control" id="incident-location" name="location" placeholder="Where did it happen?">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-date">Incident Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="incident-date" name="incident_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-time">Incident Time</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select id="incident-time_hour" name="incident_time_hour" class="form-control" style="flex: 1;">
                                        <option value="">Hour</option>
                                        <?php for ($h = 0; $h < 24; $h++): ?>
                                            <option value="<?php echo sprintf('%02d', $h); ?>"><?php echo sprintf('%02d', $h); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span>:</span>
                                    <select id="incident-time_minute" name="incident_time_minute" class="form-control" style="flex: 1;">
                                        <option value="">Minute</option>
                                        <?php for ($m = 0; $m < 60; $m++): ?>
                                            <option value="<?php echo sprintf('%02d', $m); ?>"><?php echo sprintf('%02d', $m); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="incident-description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="incident-description" name="description" rows="4" required placeholder="Provide detailed description of the incident..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-respondent">Respondent (Person being reported)</label>
                                <select class="form-control select2" id="incident-respondent" name="respondent_id" style="width: 100%;">
                                    <option value="">Select Respondent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-respondent-relationship">Relationship to Reporter</label>
                                <select class="form-control select2" id="incident-respondent-relationship" name="respondent_relationship">
                                    <option value="co_worker">Co-worker</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="subordinate">Subordinate</option>
                                    <option value="external">External</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-reporter-role">Your Role in Incident</label>
                                <select class="form-control select2" id="incident-reporter-role" name="reporter_role">
                                    <option value="reporter">Reporter</option>
                                    <option value="witness">Witness</option>
                                    <option value="victim">Victim</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident-assigned">Assign To (HR Officer)</label>
                                <select class="form-control select2" id="incident-assigned" name="assigned_to" style="width: 100%;">
                                    <option value="">Auto-assign</option>
                                    <?php foreach ($hrEmployees as $hr): ?>
                                        <option value="<?= $hr['id'] ?>">
                                            <?= htmlspecialchars($hr['first_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="incident-evidence">Evidence Files</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="incident-evidence" name="evidence[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                            <label class="custom-file-label" for="incident-evidence">Choose files</label>
                        </div>
                        <small class="form-text text-muted">You can upload multiple files (images, documents, etc.). Max 10MB per file.</small>
                        <div id="evidence-preview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-incident">
                        <i class="fas fa-paper-plane"></i> Submit Incident
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Incident Modal -->
<div class="modal fade" id="modal-view-incident" tabindex="-1" role="dialog" aria-labelledby="modal-view-incident-label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="modal-view-incident-label">
                    <i class="fas fa-eye"></i> Incident Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="incident-details-content">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" id="btn-update-status-modal">
                    <i class="fas fa-edit"></i> Update Status
                </button>
                <button type="button" class="btn btn-primary" id="edit-incident-btn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-danger" id="btn-add-disciplinary-modal">
                    <i class="fas fa-gavel"></i> Add Disciplinary Action
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="modal-update-status" tabindex="-1" role="dialog" aria-labelledby="modal-update-status-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modal-update-status-label">
                    <i class="fas fa-edit"></i> Update Incident Status
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-update-status">
                <div class="modal-body">
                    <input type="hidden" id="status-incident-id" name="id">
                    
                    <div class="form-group">
                        <label for="status-current">Current Status</label>
                        <input type="text" class="form-control" id="status-current" readonly>
                    </div>

                    <div class="form-group">
                        <label for="status-new">New Status <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="status-new" name="status" required>
                            <option value="">Select Status</option>
                            <option value="submitted">Submitted</option>
                            <option value="under_review">Under Review</option>
                            <option value="investigation">Investigation</option>
                            <option value="escalated">Escalated</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status-notes">Notes</label>
                        <textarea class="form-control" id="status-notes" name="notes" rows="3" placeholder="Add notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Incident Modal -->
<div class="modal fade" id="modal-edit-incident" tabindex="-1" role="dialog" aria-labelledby="modal-edit-incident-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modal-edit-incident-label">
                    <i class="fas fa-edit"></i> Edit Incident
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-incident">
                <div class="modal-body">
                    <input type="hidden" id="edit-incident-id" name="id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit-incident-title">Incident Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-incident-title" name="title" required placeholder="Brief description of the incident">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-incident-type">Incident Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit-incident-type" name="incident_type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($incidentTypes as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-incident-category">Category <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit-incident-category" name="type" required>
                                    <option value="">Select Category</option>
                                    <option value="workplace_safety">Workplace Safety</option>
                                    <option value="harassment">Harassment</option>
                                    <option value="policy_violation">Policy Violation</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-incident-severity">Severity <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="edit-incident-severity" name="severity" required>
                                    <option value="">Select Severity</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit-incident-location">Location</label>
                                <input type="text" class="form-control" id="edit-incident-location" name="location" placeholder="Location of incident">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-incident-date">Incident Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit-incident-date" name="incident_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-incident-time">Incident Time</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select id="edit-incident-time_hour" name="incident_time_hour" class="form-control" style="flex: 1;">
                                        <option value="">Hour</option>
                                        <?php for ($h = 0; $h < 24; $h++): ?>
                                            <option value="<?php echo sprintf('%02d', $h); ?>"><?php echo sprintf('%02d', $h); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span>:</span>
                                    <select id="edit-incident-time_minute" name="incident_time_minute" class="form-control" style="flex: 1;">
                                        <option value="">Minute</option>
                                        <?php for ($m = 0; $m < 60; $m++): ?>
                                            <option value="<?php echo sprintf('%02d', $m); ?>"><?php echo sprintf('%02d', $m); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit-incident-description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit-incident-description" name="description" rows="4" required placeholder="Describe the incident in detail..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Comment Modal -->
<div class="modal fade" id="modal-add-comment" tabindex="-1" role="dialog" aria-labelledby="modal-add-comment-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="modal-add-comment-label">
                    <i class="fas fa-comment"></i> Add Comment
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-add-comment">
                <div class="modal-body">
                    <input type="hidden" id="comment-incident-id" name="incident_id">
                    
                    <div class="form-group">
                        <label for="comment-text">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="comment-text" name="comment" rows="4" required placeholder="Enter your comment..."></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="comment-internal" name="is_internal" value="1">
                            <label class="custom-control-label" for="comment-internal">Internal note (not visible to reporter)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-paper-plane"></i> Add Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
