<div class="modal fade" id="updateStatusModal<?= $leave['id']; ?>" tabindex="-1" aria-labelledby="updateStatusLabel<?= $leave['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="updateStatusLabel<?= $leave['id']; ?>">
                    Update Leave Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=leave-requests-update-status" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="leave_id" value="<?= $leave['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select status-select" data-leave-id="<?= $leave['id']; ?>" required>
                            <option value="Pending" <?= ($leave['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= ($leave['status'] ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= ($leave['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="mb-3 reject-reason-div d-none" id="rejectReasonDiv<?= $leave['id']; ?>">
                        <label class="form-label fw-semibold">Rejected Reason</label>
                        <textarea name="reject_reason" class="form-control" rows="3" placeholder="Please describe the rejected reason"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>