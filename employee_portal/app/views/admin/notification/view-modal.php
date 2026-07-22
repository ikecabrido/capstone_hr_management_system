<div class="modal fade" id="viewNotificationModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header py-2">

                <h5 class="modal-title">
                    <i class="fas fa-bell text-primary me-2"></i>
                    Notification Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <table class="table table-sm table-borderless mb-3">

                    <tr>
                        <th width="120" class="text-muted">Title</th>
                        <td id="viewTitle"></td>
                    </tr>

                    <tr>

                        <th class="text-muted align-top">
                            Message
                        </th>

                        <td>

                            <div
                                id="viewMessage"
                                class="border rounded p-2 bg-light small"
                                style="white-space:pre-wrap; max-height:120px; overflow:auto;">
                            </div>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Type
                        </th>

                        <td>

                            <span
                                id="viewType"
                                class="badge">
                            </span>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Priority
                        </th>

                        <td>

                            <span
                                id="viewPriority"
                                class="badge">
                            </span>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Created
                        </th>

                        <td id="viewCreated"></td>

                    </tr>

                </table>

                <label class="fw-semibold small mb-2">
                    Recipients
                </label>

                <div
                    class="table-responsive border rounded"
                    style="max-height:180px; overflow-y:auto;">

                    <table class="table table-sm table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>Employee No.</th>
                                <th>Name</th>
                                <th>Department</th>

                            </tr>

                        </thead>

                        <tbody id="viewRecipients">

                            <tr>

                                <td colspan="3" class="text-center text-muted">
                                    Loading...
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer py-2">

                <button
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>