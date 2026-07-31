<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="index.php?url=training-status-update">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Update Status
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="id"
                        id="status_request_id">

                    <label class="fw-bold mb-2">
                        Status
                    </label>

                    <select
                        name="request_status"
                        id="status_request"
                        class="form-control">

                        <option value="New">New</option>
                        <option value="Received">Received</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Completed">Completed</option>

                    </select>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Save
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>