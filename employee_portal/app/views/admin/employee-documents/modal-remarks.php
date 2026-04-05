<div class="modal fade" id="remarksModal<?= (int)$doc['approval_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="index.php?url=admin-documents-add-remarks">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="approval_id" value="<?= (int)$doc['approval_id'] ?>">

                    <textarea name="remarks"
                        class="form-control"
                        rows="4"
                        placeholder="Enter remarks..."
                        required><?= htmlspecialchars($doc['remarks'] ?? '', ENT_QUOTES) ?></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

            </div>
        </form>
    </div>
</div>