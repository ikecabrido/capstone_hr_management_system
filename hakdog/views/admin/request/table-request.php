<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-3xl">All Requests</h5>

            <span class="badge bg-light text-dark border text-xl">
                <?= count($requests); ?> Total
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">

                <thead class="text-uppercase small text-muted border-bottom">
                    <tr>
                        <th>#</th>
                        <th>Sender</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th style="width:18%">Details</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Admin Remarks</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $index => $request): ?>

                            <?php
                            $badge = match ($request['status']) {
                                'Approved' => 'success',
                                'Rejected' => 'danger',
                                'Cancelled' => 'secondary',
                                'Completed' => 'info',
                                default => 'warning'
                            };
                            ?>

                            <tr>
                                <td class="text-muted"><?= $index + 1; ?></td>

                                <td>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($request['user_name']); ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($request['request_type']); ?>
                                    </span>
                                </td>

                                <td class="fw-medium">
                                    <?= htmlspecialchars($request['title']); ?>
                                </td>

                                <td class="text-muted small text-truncate" style="max-width: 200px;">
                                    <?= htmlspecialchars($request['details']); ?>
                                </td>

                                <td style="min-width:90px;">

                                    <?php require __DIR__ . '/statusUpdate.php'; ?>

                                </td>

                                <td class="text-center">

                                    <?php require __DIR__ . '/downloadFile.php'; ?>

                                </td>

                                <td class="small text-truncate" style="max-width: 180px;">
                                    <?php if (!empty($request['admin_remarks'])): ?>
                                        <?= htmlspecialchars($request['admin_remarks']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">——</span>
                                    <?php endif; ?>
                                </td>

                                <td class="small text-muted">
                                    <?= date('M d, Y', strtotime($request['created_at'])); ?>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#manageRequest<?= $request['id']; ?>">
                                        Manage
                                    </button>
                                </td>
                                <?php require __DIR__ . '/modal-request.php'; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>