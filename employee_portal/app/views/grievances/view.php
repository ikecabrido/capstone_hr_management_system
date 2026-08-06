<div class="modal fade"
    id="viewGrievance<?= $grievance['eer_grievance_id'] ?>"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white border-0 p-4">

                <div class="w-100 d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center">

                        <div class="bg-white rounded-3 d-flex align-items-center justify-content-center shadow-sm me-3"
                            style="width:58px;height:58px;">

                            <i class="fas fa-hand-paper text-primary fs-2"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">

                                Grievance Details

                            </h4>

                            <div class="text-white-50 small">

                                Employee Engagement & Relations

                            </div>

                            <div class="mt-2">

                                <span class="badge bg-light text-primary px-3 py-2">

                                    <i class="fas fa-hashtag me-1"></i>

                                    GRV-<?= str_pad($grievance['eer_grievance_id'], 5, '0', STR_PAD_LEFT) ?>

                                </span>

                            </div>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

            </div>

            <div class="modal-body">

                <?php

                // Status Badge
                switch (strtolower($grievance['status'])) {

                    case 'pending':
                        $statusBadge = 'bg-warning text-dark';
                        break;

                    case 'resolved':
                        $statusBadge = 'bg-success';
                        break;

                    case 'escalated':
                        $statusBadge = 'bg-danger';
                        break;

                    case 'closed':
                        $statusBadge = 'bg-secondary';
                        break;

                    default:
                        $statusBadge = 'bg-primary';
                }

                // Priority Badge
                switch (strtolower($grievance['priority'])) {

                    case 'low':
                        $priorityBadge = 'bg-success';
                        break;

                    case 'medium':
                        $priorityBadge = 'bg-info';
                        break;

                    case 'high':
                        $priorityBadge = 'bg-warning text-dark';
                        break;

                    case 'urgent':
                        $priorityBadge = 'bg-danger';
                        break;

                    case 'critical':
                        $priorityBadge = 'bg-dark';
                        break;

                    default:
                        $priorityBadge = 'bg-secondary';
                }

                ?>

                <!-- Basic Information -->
                <div class="card border-0 bg-light mb-4">

                    <div class="card-header bg-transparent border-0 pb-0">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-info-circle me-2"></i>

                            Grievance Information

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="row g-4">

                            <div class="col-md-6">

                                <small class="text-muted d-block">Subject</small>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars($grievance['subject']) ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">Category</small>

                                <div class="fw-semibold">

                                    <?= htmlspecialchars($grievance['category']) ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">Priority</small>

                                <span class="badge <?= $priorityBadge ?> px-3 py-2">

                                    <?= ucfirst($grievance['priority']) ?>

                                </span>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">Current Status</small>

                                <span class="badge <?= $statusBadge ?> px-3 py-2">

                                    <?= ucwords($grievance['status']) ?>

                                </span>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">

                                    Anonymous Submission

                                </small>

                                <div>

                                    <?= $grievance['anonymous'] ? 'Yes' : 'No' ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">

                                    Confidential

                                </small>

                                <div>

                                    <?= $grievance['confidential'] ? 'Yes' : 'No' ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">

                                    Submitted On

                                </small>

                                <div>

                                    <?= date('F d, Y h:i A', strtotime($grievance['created_at'])) ?>

                                </div>

                            </div>

                            <?php if (!empty($grievance['resolved_at'])): ?>

                                <div class="col-md-6">

                                    <small class="text-muted d-block">

                                        Resolved On

                                    </small>

                                    <div>

                                        <?= date('F d, Y h:i A', strtotime($grievance['resolved_at'])) ?>

                                    </div>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- Description -->

                <div class="card border-0 mb-4">

                    <div class="card-header bg-white">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-align-left me-2"></i>

                            Description

                        </h6>

                    </div>

                    <div class="card-body bg-light rounded-bottom">

                        <?= nl2br(htmlspecialchars($grievance['description'])) ?>

                    </div>

                </div>

                <!-- Resolution -->

                <?php if (!empty($grievance['resolution_of_complaint'])): ?>

                    <div class="card border-success mb-4">

                        <div class="card-header bg-success text-white">

                            <h6 class="fw-bold mb-0">

                                <i class="fas fa-check-circle me-2"></i>

                                Resolution

                            </h6>

                        </div>

                        <div class="card-body">

                            <?= nl2br(htmlspecialchars($grievance['resolution_of_complaint'])) ?>

                        </div>

                    </div>

                <?php endif; ?>

                <!-- Action Taken -->

                <?php if (!empty($grievance['action_taken'])): ?>

                    <div class="card border-info mb-4">

                        <div class="card-header bg-info text-white">

                            <h6 class="fw-bold mb-0">

                                <i class="fas fa-tasks me-2"></i>

                                Action Taken

                            </h6>

                        </div>

                        <div class="card-body">

                            <?= nl2br(htmlspecialchars($grievance['action_taken'])) ?>

                        </div>

                    </div>

                <?php endif; ?>

                <!-- Satisfaction -->

                <?php if (!empty($grievance['satisfaction_rating'])): ?>

                    <div class="card border-warning">

                        <div class="card-header bg-warning">

                            <h6 class="fw-bold mb-0">

                                <i class="fas fa-star me-2"></i>

                                Satisfaction Feedback

                            </h6>

                        </div>

                        <div class="card-body">

                            <p class="mb-2">

                                <strong>Rating:</strong>

                                <?= $grievance['satisfaction_rating'] ?>/5

                            </p>

                            <?php if (!empty($grievance['satisfaction_comment'])): ?>

                                <p class="mb-0">

                                    <?= nl2br(htmlspecialchars($grievance['satisfaction_comment'])) ?>

                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

            <div class="modal-footer justify-content-between">

                <small class="text-muted">
                    Submitted:
                    <?= date('F d, Y h:i A', strtotime($grievance['created_at'])) ?>
                </small>

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>