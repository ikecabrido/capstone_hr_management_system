<div class="modal fade" id="leaveStatusModal" tabindex="-1">

    <div class="modal-dialog modal-sm modal-dialog-centered">

        <div class="modal-content">

            <form method="POST" action="index.php?url=leave-update-status">

                <input type="hidden" name="leave_id" id="leave_id">

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

                <div class="modal-body text-center">

                    <p class="mb-4">
                        Choose the action for this leave request.
                    </p>

                    <div class="d-grid gap-2">

                        <button type="submit"
                            class="btn btn-success"
                            name="status"
                            value="Approved">
                            Approve
                        </button>

                        <button type="button"
                            class="btn btn-danger"
                            onclick="showRejectReason()">
                            Reject
                        </button>

                        <div id="rejectSection" style="display:none;" class="mt-3">
                            <textarea
                                class="form-control"
                                name="reject_reason"
                                placeholder="Reason for rejection..."></textarea>

                            <button
                                type="submit"
                                class="btn btn-danger mt-2"
                                name="status"
                                value="Rejected">
                                Confirm Rejection
                            </button>
                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>