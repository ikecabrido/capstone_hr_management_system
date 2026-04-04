<div class="modal fade" id="createMeetingModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="index.php?url=admin-online-meeting-store">
            <div class="modal-content">
                <input type="hidden" value="<?= $user_id ?? '' ?>" name="created_by">
                <input type="hidden" value="<?= $employee_id ?? '' ?>" name="employee_id">
                <div class="modal-header">
                    <h5 class="modal-title text-4xl">Create Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Meeting Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Schedule</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>

            </div>
        </form>
    </div>
</div>