<div class="w-full ml-32">
    <div class="content-wrapper w-full">
        <?php require __DIR__ . '/../../../views/partials/notif.php' ?>
        <div class="card shadow-lg border-0 rounded-4 w-full p-4 bg-white">

            <?php require __DIR__ . '/../partials/notif.php' ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold fs-3">Leave Dashboard</h1>
                <button
                    class="btn btn-success fw-bold d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#leaveRequestModal">
                    ➕ Request Leave
                </button>
            </div>

            <div class="leave-balance row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm rounded-3 p-3">
                        <h5 class="fw-semibold text-secondary">Total Leaves</h5>
                        <p class="fs-3 fw-bold"><?= $totalLeaves ?? 0 ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm rounded-3 p-3">
                        <h5 class="fw-semibold text-secondary">Used Leaves</h5>
                        <p class="fs-3 fw-bold text-danger"><?= $usedLeaves ?? 0 ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm rounded-3 p-3">
                        <h5 class="fw-semibold text-secondary">Remaining Leaves</h5>
                        <p class="fs-3 fw-bold text-success"><?= $remainingLeaves ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="leave-requests mt-4">
                <h2 class="fs-5 fw-bold mb-3">Leave Requests</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leaves)): ?>
                                <?php foreach ($leaves as $index => $leave): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                                        <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                                        <td>
                                            <?php
                                                $statusClass = match(strtolower($leave['status'])) {
                                                    'pending' => 'badge bg-warning text-dark',
                                                    'approved' => 'badge bg-success',
                                                    'rejected' => 'badge bg-danger',
                                                    default => 'badge bg-secondary'
                                                };
                                            ?>
                                            <span class="<?= $statusClass ?>"><?= ucfirst($leave['status']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($leave['reason']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No leave requests found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php require __DIR__ . '/modal-leave-request.php' ?>

        </div>
    </div>
</div>