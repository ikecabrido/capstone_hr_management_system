<?php
/**
 * Disciplinary Action Modals
 * Contains all modals for disciplinary action management
 */
?>

<!-- Create Disciplinary Action Modal -->
<div class="modal fade" id="modal-create-disciplinary" tabindex="-1" role="dialog" aria-labelledby="modal-create-disciplinary-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modal-create-disciplinary-label">
                    <i class="fas fa-gavel"></i> Create Disciplinary Action
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-create-disciplinary">
                <div class="modal-body">
                    <input type="hidden" id="disciplinary-incident-id" name="incident_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Incident:</strong> <span id="disciplinary-incident-reference"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplinary-employee">Employee <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="disciplinary-employee" name="employee_id" required style="width: 100%;">
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplinary-action-type">Action Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="disciplinary-action-type" name="action_type" required>
                                    <option value="">Select Action Type</option>
                                    <option value="verbal_warning">Verbal Warning</option>
                                    <option value="written_warning">Written Warning</option>
                                    <option value="suspension">Suspension</option>
                                    <option value="termination">Termination</option>
                                    <option value="final_warning">Final Warning</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="disciplinary-reason">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="disciplinary-reason" name="reason" rows="3" required placeholder="Provide the reason for this disciplinary action..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="disciplinary-violation">Violation Description</label>
                        <textarea class="form-control" id="disciplinary-violation" name="violation_description" rows="2" placeholder="Detailed description of the violation..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplinary-start-date">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="disciplinary-start-date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplinary-end-date">End Date (for suspension)</label>
                                <input type="date" class="form-control" id="disciplinary-end-date" name="end_date">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="disciplinary-duration">Duration (days)</label>
                        <input type="number" class="form-control" id="disciplinary-duration" name="duration_days" min="1" placeholder="Auto-calculated for suspension">
                    </div>

                    <div class="form-group">
                        <label for="disciplinary-document">Supporting Document</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="disciplinary-document" name="document" accept=".pdf,.doc,.docx">
                            <label class="custom-file-label" for="disciplinary-document">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Upload supporting document (PDF, DOC, DOCX)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger" id="btn-submit-disciplinary">
                        <i class="fas fa-paper-plane"></i> Create Disciplinary Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Disciplinary Actions Modal -->
<div class="modal fade" id="modal-view-disciplinary" tabindex="-1" role="dialog" aria-labelledby="modal-view-disciplinary-label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modal-view-disciplinary-label">
                    <i class="fas fa-gavel"></i> Disciplinary Actions
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Incident: <span id="disciplinary-list-incident-reference"></span></h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-danger" id="btn-add-disciplinary-from-list">
                            <i class="fas fa-plus"></i> Add Disciplinary Action
                        </button>
                    </div>
                </div>
                
                <div id="disciplinary-actions-list">
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
            </div>
        </div>
    </div>
</div>

<!-- View Single Disciplinary Action Modal -->
<div class="modal fade" id="modal-view-disciplinary-detail" tabindex="-1" role="dialog" aria-labelledby="modal-view-disciplinary-detail-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modal-view-disciplinary-detail-label">
                    <i class="fas fa-gavel"></i> Disciplinary Action Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="disciplinary-detail-content">
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
                <button type="button" class="btn btn-warning" id="edit-disciplinary-btn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-success" id="btn-approve-disciplinary">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-info" id="btn-mark-implemented">
                    <i class="fas fa-check-double"></i> Mark Implemented
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Disciplinary Status Modal -->
<div class="modal fade" id="modal-update-disciplinary-status" tabindex="-1" role="dialog" aria-labelledby="modal-update-disciplinary-status-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modal-update-disciplinary-status-label">
                    <i class="fas fa-edit"></i> Update Disciplinary Action Status
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-update-disciplinary-status">
                <div class="modal-body">
                    <input type="hidden" id="disciplinary-status-id" name="id">
                    
                    <div class="form-group">
                        <label for="disciplinary-status-current">Current Status</label>
                        <input type="text" class="form-control" id="disciplinary-status-current" readonly>
                    </div>

                    <div class="form-group">
                        <label for="disciplinary-status-new">New Status <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="disciplinary-status-new" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="issued">Issued</option>
                            <option value="appealed">Appealed</option>
                            <option value="upheld">Upheld</option>
                            <option value="dismissed">Dismissed</option>
                        </select>
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

<!-- Employee Disciplinary History Modal -->
<div class="modal fade" id="modal-employee-history" tabindex="-1" role="dialog" aria-labelledby="modal-employee-history-label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="modal-employee-history-label">
                    <i class="fas fa-history"></i> Employee Disciplinary History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Employee: <span id="history-employee-name"></span></h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-danger" id="history-total-actions">0 Total Actions</span>
                    </div>
                </div>
                
                <div id="employee-history-content">
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
            </div>
        </div>
    </div>
</div>
