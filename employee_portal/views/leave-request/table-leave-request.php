<div class="card-header bg-white">
    <h5 class="mb-0">Leave Requests Table</h5>
</div>
<div class="card-body">
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Admin Name</th>
                <th>Type of Leave</th>
                <th>Start to End Date</th>
                <th>Date Submitted</th>
                <th>Updated At</th>
                <th>Status</th>
                <th>Details</th>
                <th>Document</th>
                <th>Reject Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-[12px]">
            <?php if (!empty($leaveRequests)): ?>
                <?php foreach ($leaveRequests as $leave): ?>
                    <tr>
                        <td><?= htmlspecialchars($leave['id']); ?></td>
                        <td><?= htmlspecialchars($leave['name'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($leave['type_of_leave']); ?></td>
                        <td><?= !empty($leave['start_date']) ? date('M d, Y', strtotime($leave['start_date'])) : ''; ?> to <?= !empty($leave['end_date']) ? date('M d, Y', strtotime($leave['end_date'])) : ''; ?></td>
                        <td><?= !empty($leave['date_submitted']) ? date('M d, Y', strtotime($leave['date_submitted'])) : ''; ?></td>
                        <td><?= !empty($leave['updated_at']) ? date('M d, Y', strtotime($leave['updated_at'])) : ''; ?></td>
                        <td>
                            <button class="text-[12px] btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?= $leave['id']; ?>">
                                <?= htmlspecialchars($leave['status']); ?>
                            </button>
                        </td>
                        <td><?= htmlspecialchars($leave['details']); ?></td>
                        <td>
                            <?php if (!empty($leave['supporting_document'])): ?>
                                <a class="text-blue-500" href="/capstone_hr_management_system/employee_portal/public/uploads/<?= htmlspecialchars($leave['supporting_document']); ?>" target="_blank">
                                    View
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($leave['reject_reason']); ?></td>
                        <td>
                            <form method="POST" action="index.php?url=leave-requests-delete" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this leave request?');">
                                <input type="hidden" name="leave_id" value="<?= $leave['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                    <?php include __DIR__ . '/update-status-modal.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No leave requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".status-select").forEach(function(select) {
            select.addEventListener("change", function() {
                let leaveId = this.dataset.leaveId;
                let rejectDiv = document.getElementById("rejectReasonDiv" + leaveId);

                if (this.value === "Rejected") {
                    rejectDiv.classList.remove("d-none");
                } else {
                    rejectDiv.classList.add("d-none");
                }
            });

            select.dispatchEvent(new Event('change'));
        });
    });
</script>