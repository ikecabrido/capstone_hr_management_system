<div class="px-3 pt-4">
    <div class="content-wrapper">
        <div class="mx-auto" style="max-width: 900px;">
            <div class="card shadow-lg border-0 rounded-4">
                <?php require __DIR__ . '/../partials/notif.php'; ?>
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <?php
                        $status = null;
                        if (!empty($resignationRequests)) {
                            $status = $resignationRequests[0]['status']; // Latest request (assuming ordered DESC)
                        }
                        ?>
                        <h4 class="mb-0 text-3xl fw-bold">
                            My Resignation Requests
                        </h4>
                        <?php if ($status === 'Pending'): ?>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-clock me-1"></i>
                                Wait for HR Review
                            </button>
                        <?php elseif ($status === 'Approved'): ?>
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-check-circle me-1"></i>
                                Your resignation has been approved
                            </button>
                        <?php elseif ($status === 'Rejected'): ?>
                            <button
                                type="button"
                                class="btn btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#createResignationModal">
                                <i class="fas fa-triangle-exclamation me-1"></i>
                                Resignation has been rejected - Submit Again
                            </button>
                        <?php else: ?>
                            <button
                                type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#createResignationModal">
                                <i class="fas fa-plus me-1"></i>
                                New Request
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($resignationRequests)): ?>
                        <?php foreach ($resignationRequests as $request): ?>
                            <?php
                            $badge = match ($request['status']) {
                                'Pending'   => 'bg-warning text-dark',
                                'Approved'  => 'bg-success',
                                'Rejected'  => 'bg-danger',
                                default     => 'bg-secondary'
                            };
                            ?>

                            <div class="card border mb-4 shadow-sm">

                                <div class="card-header bg-light d-flex justify-content-between align-items-center">

                                    <div>
                                        <strong><?= htmlspecialchars($request['resignation_type']) ?></strong>
                                    </div>

                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars($request['status']) ?>
                                    </span>
                                </div>

                                <div class="card-body">

                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="fw-semibold">Submitted</label>
                                            <p class="mb-0">
                                                <?= date('F d, Y h:i A', strtotime($request['date_submitted'])) ?>
                                            </p>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fw-semibold">Last Working Day</label>
                                            <p class="mb-0">
                                                <?= date('F d, Y', strtotime($request['intended_last_working_day'])) ?>
                                            </p>
                                        </div>

                                        <?php if (!empty($request['employee_remarks'])): ?>
                                            <div class="col-12">
                                                <label class="fw-semibold">Employee Remarks</label>
                                                <textarea
                                                    class="form-control"
                                                    rows="3"
                                                    readonly><?= htmlspecialchars($request['employee_remarks']) ?></textarea>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($request['hr_remarks'])): ?>
                                            <div class="col-12">
                                                <label class="fw-semibold">HR Remarks</label>
                                                <textarea
                                                    class="form-control"
                                                    rows="3"
                                                    readonly><?= htmlspecialchars($request['hr_remarks']) ?></textarea>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($request['attachment'])): ?>
                                            <div class="col-12 flex justify-content-between">
                                                <div>
                                                    <label class="fw-semibold">Attachment</label>
                                                    <br>

                                                    <a
                                                        href="/capstone_hr_management_system/employee_portal/<?= htmlspecialchars($request['attachment']) ?>"
                                                        target="_blank"
                                                        class="btn btn-outline-primary btn-sm mt-2">
                                                        <i class="fas fa-file-pdf me-1"></i>
                                                        View Attachment
                                                    </a>
                                                </div>
                                                <?php if ($request['status'] === 'Pending') { ?>
                                                    <form action="index.php?url=resignation-request-cancel" method="POST">
                                                        <input
                                                            type="hidden"
                                                            name="resignation_id"
                                                            value="<?= $request['resignation_id'] ?>">

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="Cancelled">

                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger btn-sm mt-3"
                                                            onclick="return confirm('Are you sure you want to cancel this resignation request?');">
                                                            Cancel Request
                                                        </button>
                                                    </form>
                                                <?php } else { ?>

                                                <?php } ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="text-center py-5">

                            <i class="fas fa-file-signature fa-3x text-secondary mb-3"></i>

                            <h5>No resignation requests found.</h5>

                            <p class="text-muted">
                                You haven't submitted any resignation requests yet.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/create.php' ?>