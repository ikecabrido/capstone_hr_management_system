<!-- View Modal -->
<div class="modal fade"
    id="viewModal<?= $request['resignation_id']; ?>"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Resignation Request Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Employee Code
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['employee_code']); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Employee Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Resignation Type
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['resignation_type']); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['status']); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Submitted
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= date('F d, Y', strtotime($request['date_submitted'])); ?>"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Last Working Day
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= date('F d, Y', strtotime($request['intended_last_working_day'])); ?>"
                            readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Resignation Reason
                        </label>

                        <textarea
                            class="form-control"
                            rows="5"
                            readonly><?= htmlspecialchars($request['resignation_reason']); ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            HR Remarks
                        </label>

                        <textarea
                            class="form-control"
                            rows="3" readonly><?= htmlspecialchars($request['hr_remarks'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Employee Remarks
                        </label>

                        <?php if (!empty($request['employee_remarks'])): ?>
                            <textarea
                                class="form-control"
                                rows="3"
                                readonly><?= htmlspecialchars($request['employee_remarks']) ?></textarea>
                        <?php else: ?>
                            <p class="text-muted mb-0">No remarks.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Attachment
                        </label>

                        <?php if (!empty($request['attachment'])): ?>

                            <div class="border rounded p-3 bg-light d-flex justify-content-between align-items-center">

                                <span>
                                    <i class="fa-solid fa-file-lines me-2 text-primary"></i>
                                    <?= htmlspecialchars(basename($request['attachment'])); ?>
                                </span>

                                <a
                                    href="<?= htmlspecialchars($request['attachment']); ?>"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-eye me-1"></i>
                                    View Attachment
                                </a>

                            </div>

                        <?php else: ?>

                            <div class="alert alert-secondary mb-0">
                                No attachment uploaded.
                            </div>

                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>

        </div>
    </div>

</div>