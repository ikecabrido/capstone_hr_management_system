<div
    class="modal fade"
    id="viewPayrollRequestModal<?= (int) $request['id'] ?>"
    tabindex="-1"
    aria-labelledby="viewPayrollRequestModalLabel<?= (int) $request['id'] ?>"
    aria-hidden="true">

```
<div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">

        <div class="modal-header border-bottom px-4 py-3">
            <div>
                <h5 class="modal-title font-weight-bold mb-1"
                    id="viewPayrollRequestModalLabel<?= (int) $request['id'] ?>">
                    Payroll Request Details
                </h5>
                <small class="text-muted">
                    Review the employee's payroll document request.
                </small>
            </div>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close">
            </button>
        </div>

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

        <div class="modal-body px-4 py-4">

            <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-4">
                <div>
                    <small class="text-muted d-block mb-1">
                        Request Status
                    </small>

                    <span class="badge <?= $statusClass ?> px-3 py-2">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </div>

                <div class="text-right">
                    <small class="text-muted d-block">
                        Request ID
                    </small>
                    <strong>#<?= (int) $request['id'] ?></strong>
                </div>
            </div>

            <h6 class="font-weight-bold mb-3">
                <i class="fas fa-user text-primary mr-2"></i>
                Employee Information
            </h6>

            <div class="row mb-4">

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Employee Name</small>
                    <strong>
                        <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>
                    </strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Employee Code.</small>
                    <strong>
                        <?= htmlspecialchars($request['employee_code'] ?? 'N/A') ?>
                    </strong>
                </div>

            </div>

            <h6 class="font-weight-bold mb-3">
                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                Request Information
            </h6>

            <div class="row mb-4">

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Payroll Document</small>
                    <strong>
                        <?= htmlspecialchars($request['request_type'] ?? 'N/A') ?>
                    </strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Purpose</small>
                    <strong>
                        <?= htmlspecialchars($request['purpose'] ?? 'N/A') ?>
                    </strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Payroll Period</small>
                    <strong>
                        <?php if (!empty($request['payroll_period_start']) && !empty($request['payroll_period_end'])): ?>
                            <?= date('M d, Y', strtotime($request['payroll_period_start'])) ?>
                            -
                            <?= date('M d, Y', strtotime($request['payroll_period_end'])) ?>
                        <?php else: ?>
                            <span class="text-muted">Not specified</span>
                        <?php endif; ?>
                    </strong>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Requested At</small>
                    <strong>
                        <?= !empty($request['requested_at'])
                            ? date('M d, Y h:i A', strtotime($request['requested_at']))
                            : 'N/A' ?>
                    </strong>
                </div>

            </div>

            <div class="mb-4">
                <small class="text-muted d-block mb-2">
                    Additional Remarks
                </small>

                <div class="border rounded p-3 bg-light">
                    <?php if (!empty($request['remarks'])): ?>
                        <?= nl2br(htmlspecialchars($request['remarks'])) ?>
                    <?php else: ?>
                        <span class="text-muted">
                            No remarks provided.
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($request['rejection_reason'])): ?>
                <div class="alert alert-danger mb-4">
                    <div class="font-weight-bold mb-1">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Rejection Reason
                    </div>

                    <div>
                        <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($request['processed_at'])): ?>
                <div class="border-top pt-3">
                    <small class="text-muted">
                        Processed At
                    </small>

                    <div class="font-weight-bold">
                        <?= date('M d, Y h:i A', strtotime($request['processed_at'])) ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="modal-footer border-top px-4 py-3">
            <button
                type="button"
                class="btn btn-light border px-4"
                data-bs-dismiss="modal">
                Close
            </button>
        </div>

    </div>
</div>
```

</div>
