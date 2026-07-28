<div
    class="modal fade"
    id="viewPayrollRequestModal<?= (int) $request['id'] ?>"
    tabindex="-1"
    aria-labelledby="viewPayrollRequestModalLabel<?= (int) $request['id'] ?>"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header">

                <div>
                    <h5
                        class="modal-title font-weight-bold"
                        id="viewPayrollRequestModalLabel<?= (int) $request['id'] ?>">

                        Payroll Request Details

                    </h5>

                    <small class="text-muted">
                        Information about your submitted payroll request.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <div class="modal-body p-4">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block mb-1">
                            Payroll Document
                        </small>

                        <div class="font-weight-bold">
                            <?= htmlspecialchars($request['request_type'] ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block mb-1">
                            Purpose
                        </small>

                        <div class="font-weight-bold">
                            <?= htmlspecialchars($request['purpose'] ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block mb-1">
                            Payroll Period
                        </small>

                        <div class="font-weight-bold">

                            <?php if (
                                !empty($request['payroll_period_start']) &&
                                !empty($request['payroll_period_end'])
                            ): ?>

                                <?= date('M d, Y', strtotime($request['payroll_period_start'])) ?>
                                –
                                <?= date('M d, Y', strtotime($request['payroll_period_end'])) ?>

                            <?php else: ?>

                                Not specified

                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block mb-1">
                            Status
                        </small>

                        <?php
                        $status = $request['status'] ?? 'Pending';

                        $statusClass = match ($status) {
                            'Pending' => 'badge-warning',
                            'Processing' => 'badge-info',
                            'Approved' => 'badge-primary',
                            'Rejected' => 'badge-danger',
                            'Completed' => 'badge-success',
                            'Cancelled' => 'badge-secondary',
                            default => 'badge-secondary'
                        };
                        ?>

                        <span class="badge <?= $statusClass ?> px-3 py-2">
                            <?= htmlspecialchars($status) ?>
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block mb-1">
                            Requested At
                        </small>

                        <div class="font-weight-bold">
                            <?= !empty($request['requested_at'])
                                ? date('M d, Y h:i A', strtotime($request['requested_at']))
                                : 'N/A' ?>
                        </div>
                    </div>

                    <?php if (!empty($request['processed_at'])): ?>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">
                                Processed At
                            </small>

                            <div class="font-weight-bold">
                                <?= date('M d, Y h:i A', strtotime($request['processed_at'])) ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <div class="col-12 mt-2">

                        <small class="text-muted d-block mb-2">
                            Additional Remarks
                        </small>

                        <div class="bg-light border rounded p-3">

                            <?php if (!empty($request['remarks'])): ?>

                                <?= nl2br(htmlspecialchars($request['remarks'])) ?>

                            <?php else: ?>

                                <span class="text-muted">
                                    No additional remarks provided.
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                    <?php if (!empty($request['rejection_reason'])): ?>

                        <div class="col-12 mt-3">

                            <small class="text-danger d-block mb-2">
                                Rejection Reason
                            </small>

                            <div class="alert alert-danger mb-0">
                                <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <div class="modal-footer bg-light">

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