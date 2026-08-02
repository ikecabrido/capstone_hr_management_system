<div class="modal fade" id="statusModal<?= $request['resignation_id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="index.php?url=admin-resignation-remarks" method="POST">
                <input type="hidden" name="resignation_id" value="<?= $request['resignation_id'] ?>">

                <div class="modal-header">
                    <h5 class="modal-title">HR Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>"
                            readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea
                            name="hr_remarks"
                            class="form-control"
                            rows="4"
                            placeholder="Enter HR remarks..."><?= htmlspecialchars($request['hr_remarks'] ?? '') ?></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>