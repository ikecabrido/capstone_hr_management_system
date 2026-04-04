<div class="modal fade" id="leaveRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-3xl">Request Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="leaveRequestForm" method="POST" action="index.php?url=leave-request-store">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type_id" class="form-select shadow-sm" required>
                            <option value="" disabled selected>Choose leave type...</option>
                            <?php foreach ($allLeaveTypes as $type): ?>
                                <option value="<?= $type['leave_type_id'] ?>"><?= htmlspecialchars($type['leave_type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Reason for Leave</label>
                        <textarea
                            name="reason"
                            class="form-control shadow-sm"
                            rows="4"
                            placeholder="Provide a brief explanation..."
                            required></textarea>
                        <small class="text-muted">Be clear and concise.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control shadow-sm" required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-4 py-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" id="submitLeaveBtn" class="btn btn-success px-4 shadow-sm">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>