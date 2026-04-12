<div class="w-full ml-12">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full p-4 bg-white">

            <?php require __DIR__ . '/../partials/notif.php' ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold text-6xl">Leave Dashboard</h1>
                <button
                    class="btn btn-success fw-bold d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#leaveRequestModal">
                    ➕ Request Leave
                </button>
            </div>

            <div class="leave-balance row g-3 mb-4">
                <?php foreach ($leaveBalances as $balance): ?>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm rounded-3 p-3">

                            <!-- Leave Type Name -->
                            <h5 class="fw-semibold text-secondary">
                                <?= $balance['leave_type_name'] ?>
                            </h5>

                            <!-- Total -->
                            <p class="mb-1">
                                Total: <span class="fw-bold"><?= $balance['total_days'] ?></span>
                            </p>

                            <!-- Used -->
                            <p class="mb-1 text-danger">
                                Used: <span class="fw-bold"><?= $balance['used_days'] ?></span>
                            </p>

                            <!-- Remaining -->
                            <p class="text-success">
                                Remaining: <span class="fw-bold"><?= $balance['remaining_days'] ?></span>
                            </p>

                        </div>
                    </div>
                <?php endforeach; ?>
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
                                <th>Details</th>
                                <th>Reject Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leaves)): ?>
                                <?php foreach ($leaves as $index => $leave): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($leaveTypeMap[$leave['leave_type_id']] ?? 'Unknown') ?></td>
                                        <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = match (strtolower($leave['status'])) {
                                                'pending' => 'badge bg-warning text-dark',
                                                'approved' => 'badge bg-success',
                                                'rejected' => 'badge bg-danger',
                                                default => 'badge bg-secondary'
                                            };
                                            ?>
                                            <span class="<?= $statusClass ?>"><?= ucfirst($leave['status']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($leave['details']) ?></td>
                                        <td>
                                            <?php
                                            if ($leave['status'] === 'Rejected' && !empty($leave['reject_reason'])) {
                                                echo htmlspecialchars($leave['reject_reason']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
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