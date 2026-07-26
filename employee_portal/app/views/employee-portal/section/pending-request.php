<div class="col-lg-6">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-hourglass-half mr-2"></i>
                Pending Requests
            </h5>
        </div>
        <div class="card-body">

            <?php if (!empty($pendingRequests)): ?>

                <?php foreach ($pendingRequests as $request): ?>

                    <?php
                    $badge = 'warning';

                    if ($request['status'] === 'Approved') {
                        $badge = 'success';
                    } elseif ($request['status'] === 'Rejected') {
                        $badge = 'danger';
                    }
                    ?>

                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">

                        <div>
                            <strong><?= htmlspecialchars($request['leave_type_name']) ?></strong><br>

                            <small class="text-muted">
                                <?= date('M d, Y', strtotime($request['start_date'])) ?>
                                -
                                <?= date('M d, Y', strtotime($request['end_date'])) ?>
                            </small>

                            <br>

                            <small>
                                <?= htmlspecialchars($request['details']) ?>
                            </small>
                        </div>

                        <span class="badge badge-<?= $badge ?> px-3 py-2">
                            <?= htmlspecialchars($request['status']) ?>
                        </span>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="text-center text-muted py-3">
                    No pending requests.
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>