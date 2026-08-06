<div class="modal fade"
    id="viewComplaint<?= $complaint['id'] ?>"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white py-3 px-4">

                <div class="d-flex align-items-center">

                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:55px; height:55px;">

                        <i class="fas fa-file-signature fs-4"></i>

                    </div>

                    <div>

                        <h5 class="modal-title fw-bold mb-1">

                            Employee Complaint Details

                        </h5>

                        <small class="text-white-50">

                            Reference No.
                            <strong class="text-white">
                                <?= htmlspecialchars($complaint['incident_id']) ?>
                            </strong>

                        </small>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white ms-3"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body bg-light">

                <!-- Complaint Summary -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-body">

                        <div class="row g-4">

                            <div class="col-md-3">

                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-hashtag text-primary me-1"></i>
                                    Reference No.
                                </small>

                                <h6 class="fw-bold mb-0">
                                    <?= htmlspecialchars($complaint['incident_id']) ?>
                                </h6>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-folder text-primary me-1"></i>
                                    Category
                                </small>

                                <h6 class="fw-bold mb-0">
                                    <?= ucwords(str_replace('_', ' ', $complaint['type'])) ?>
                                </h6>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-calendar text-primary me-1"></i>
                                    Submitted
                                </small>

                                <h6 class="fw-bold mb-0">
                                    <?= date('M d, Y', strtotime($complaint['created_at'])) ?>
                                </h6>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    Status
                                </small>

                                <?php

                                switch ($complaint['status']) {

                                    case 'submitted':
                                        $badge = 'bg-warning text-dark';
                                        break;

                                    case 'under_review':
                                        $badge = 'bg-info';
                                        break;

                                    case 'investigation':
                                        $badge = 'bg-primary';
                                        break;

                                    case 'resolved':
                                        $badge = 'bg-success';
                                        break;

                                    case 'rejected':
                                        $badge = 'bg-danger';
                                        break;

                                    default:
                                        $badge = 'bg-secondary';
                                }

                                ?>

                                <span class="badge <?= $badge ?> px-3 py-2">

                                    <?= ucwords(str_replace('_', ' ', $complaint['status'])) ?>

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Complaint Information -->
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-file-alt me-2"></i>

                            Complaint Information

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Nature of Complaint
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['incident_type']) ?>

                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Complaint Category
                                </label>

                                <div class="form-control bg-light">

                                    <?= ucwords(str_replace('_', ' ', $complaint['type'])) ?>

                                </div>

                            </div>

                            <div class="col-12 mb-3">

                                <label class="form-label text-muted">
                                    Complaint Title
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['title']) ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Respondent -->
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-user-slash me-2"></i>

                            Person Being Complained About

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Full Name
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['respondent_name'] ?: 'N/A') ?>

                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Employee Code
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['respondent_employee_id'] ?: 'N/A') ?>

                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Department
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['respondent_department'] ?: 'N/A') ?>

                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label text-muted">
                                    Position
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['respondent_position'] ?: 'N/A') ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label text-muted">
                                    Relationship
                                </label>

                                <div class="form-control bg-light">

                                    <?= ucwords(str_replace('_', ' ', $complaint['respondent_relationship'])) ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Incident Details -->
                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-header bg-white">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-map-marker-alt me-2"></i>

                            Incident Details

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="form-label text-muted">
                                    Date
                                </label>

                                <div class="form-control bg-light">

                                    <?= date('F d, Y', strtotime($complaint['incident_date'])) ?>

                                </div>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label text-muted">
                                    Time
                                </label>

                                <div class="form-control bg-light">

                                    <?= $complaint['incident_time']
                                        ? date('g:i A', strtotime($complaint['incident_time']))
                                        : 'N/A'; ?>

                                </div>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label text-muted">
                                    Location
                                </label>

                                <div class="form-control bg-light">

                                    <?= htmlspecialchars($complaint['location']) ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Description -->
                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white">

                        <h6 class="fw-bold text-primary mb-0">

                            <i class="fas fa-align-left me-2"></i>

                            Complaint Description

                        </h6>

                    </div>

                    <div class="card-body">

                        <div class="border rounded-3 p-4 bg-light"
                            style="min-height:180px; white-space:pre-line;">

                            <?= htmlspecialchars($complaint['description']) ?>

                        </div>

                    </div>

                </div>


                <!-- Timeline -->
                <div class="card border-0 bg-white shadow-sm mt-4">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <small class="text-muted">

                                    <i class="fas fa-clock me-1"></i>

                                    Submitted On

                                </small>

                                <div class="fw-semibold">

                                    <?= date('F d, Y h:i A', strtotime($complaint['created_at'])) ?>

                                </div>

                            </div>

                            <div class="col-md-6 text-md-end">

                                <small class="text-muted">

                                    <i class="fas fa-check-circle me-1"></i>

                                    Resolution Date

                                </small>

                                <div class="fw-semibold">

                                    <?= !empty($complaint['resolved_at'])
                                        ? date('F d, Y h:i A', strtotime($complaint['resolved_at']))
                                        : 'Not yet resolved'; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>