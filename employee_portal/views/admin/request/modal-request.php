<div class="modal fade"
    id="manageRequest<?= $request['id']; ?>"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <form method="POST"
                action="index.php?url=admin-request-update-status">

                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <?= htmlspecialchars($request['title']); ?>
                        </h5>
                        <small class="text-muted">
                            Submitted on <?= date('F d, Y', strtotime($request['created_at'])); ?>
                        </small>
                    </div>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">

                    <input type="hidden"
                        name="id"
                        value="<?= $request['id']; ?>">

                    <div class="row g-4">

                        <div class="col-md-7">

                            <div class="p-4 rounded-4 border bg-white shadow-sm">

                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            Request Information
                                        </h6>
                                        <small class="text-muted">
                                            Submitted request details
                                        </small>
                                    </div>

                                    <?php
                                    $badge = match ($request['status']) {
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                        'Cancelled' => 'secondary',
                                        'Completed' => 'info',
                                        default => 'warning'
                                    };
                                    ?>
                                    <span class="badge rounded-pill bg-<?= $badge; ?>">
                                        <?= $request['status']; ?>
                                    </span>
                                </div>

                                <div class="border-top pt-3">

                                    <div class="mb-3">
                                        <div class="text-muted small">Sender</div>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($request['user_name']); ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="text-muted small">Request Type</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                            <?= htmlspecialchars($request['request_type']); ?>
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">Details</div>
                                        <div class="small text-dark lh-base">
                                            <?= nl2br(htmlspecialchars($request['details'])); ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($request['attachment'])): ?>
                                        <div class="pt-2 border-top mt-3">
                                            <div class="text-muted small mb-2">Attachment</div>
                                            <a href="index.php?url=admin-download-request&id=<?= $request['id']; ?>"
                                                class="d-inline-flex align-items-center gap-2 px-3 py-2 border rounded-3 text-decoration-none text-dark bg-light hover-shadow">
                                                📎
                                                <span class="small fw-medium">Download File</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-5">

                            <div class="p-3 rounded-3 border">

                                <h6 class="fw-semibold text-uppercase small text-muted mb-3">
                                    Admin Action
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label small text-muted">
                                        Update Status
                                    </label>

                                    <select name="status"
                                        class="form-select rounded-3">

                                        <?php
                                        $statuses = ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed'];
                                        foreach ($statuses as $status):
                                        ?>
                                            <option value="<?= $status; ?>"
                                                <?= $request['status'] === $status ? 'selected' : '' ?>>
                                                <?= $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted">
                                        Admin Remarks
                                    </label>

                                    <textarea name="admin_remarks"
                                        class="form-control rounded-3"
                                        rows="5"
                                        placeholder="Write remarks..."><?= htmlspecialchars($request['admin_remarks'] ?? ''); ?></textarea>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                        class="btn btn-light rounded-3 px-4"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary rounded-3 px-4 shadow-sm">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>