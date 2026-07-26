<div class="w-[800px] card shadow-sm border-0 mb-4">

    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-file-alt mr-2"></i>
            My Leave Requests
        </h5>
    </div>

    <div class="card-body">

        <?php if (!empty($leave_requests)): ?>

            <?php foreach ($leave_requests as $req): ?>

                <?php

                switch ($req['status']) {

                    case 'Approved':
                    case 'Final-Approved':
                        $statusClass = 'status-approved';
                        $icon = 'fa-check-circle';
                        break;

                    case 'Pending':
                        $statusClass = 'status-pending';
                        $icon = 'fa-hourglass-half';
                        break;

                    case 'Rejected':
                        $statusClass = 'status-rejected';
                        $icon = 'fa-times-circle';
                        break;

                    default:
                        $statusClass = 'status-default';
                        $icon = 'fa-circle';
                }

                ?>

                <div class="border rounded p-3 mb-3">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="mb-1">
                                <?= htmlspecialchars($req['leave_type_name']) ?>
                            </h6>

                            <small class="text-muted">

                                <i class="far fa-calendar-alt"></i>

                                <?= date('M d, Y', strtotime($req['start_date'])) ?>

                                -

                                <?= date('M d, Y', strtotime($req['end_date'])) ?>

                            </small>

                        </div>

                        <span class="status-pill <?= $statusClass ?>">
                            <i class="fas <?= $icon ?> mr-2"></i>
                            <?= htmlspecialchars($req['status']) ?>
                        </span>

                    </div>

                    <hr>

                    <p class="mb-2">

                        <strong>Reason:</strong>

                        <?= htmlspecialchars($req['reason'] ?? $req['details'] ?? '-') ?>

                    </p>

                    <small class="text-muted">

                        Submitted:
                        <?= !empty($req['created_at'])
                            ? date('M d, Y h:i A', strtotime($req['created_at']))
                            : '-' ?>

                    </small>

                    <?php if (!empty($req['remarks'])): ?>

                        <div class="alert alert-light border mt-3 mb-0">

                            <strong>HR Remarks</strong><br>

                            <?= htmlspecialchars($req['remarks']) ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center py-4 text-muted">

                <i class="fas fa-inbox fa-2x mb-2"></i>

                <p class="mb-0">No leave requests found.</p>

            </div>

        <?php endif; ?>

    </div>

</div>