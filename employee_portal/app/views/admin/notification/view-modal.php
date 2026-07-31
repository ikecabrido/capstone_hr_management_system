<div class="modal fade"
    id="viewNotificationModal<?= $row['notification_id'] ?>"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-width:800px;">

        <div class="modal-content">

            <div class="modal-header">

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

                <h4><?= htmlspecialchars($row['title']) ?></h4>

                <div class="mb-4">

                    <label class="fw-semibold text-muted mb-2">
                        Message
                    </label>

                    <div class="border rounded p-3 bg-light">
                        <?= nl2br(htmlspecialchars($row['message'])) ?>
                    </div>

                </div>

                <div class="row mb-4">

                    <div class="col-md-4">

                        <label class="fw-semibold text-muted d-block">
                            Type
                        </label>

                        <span class="badge bg-primary">
                            <?= ucfirst($row['type']) ?>
                        </span>

                    </div>

                    <div class="col-md-4">

                        <label class="fw-semibold text-muted d-block">
                            Priority
                        </label>


                        <span class="badge bg-danger">
                            <?= ucfirst($row['priority']) ?>
                        </span>

                    </div>

                    <div class="col-md-4">

                        <label class="fw-semibold text-muted d-block">
                            Created
                        </label>

                        <?= date('M d, Y', strtotime($row['created_at'])) ?>

                    </div>

                </div>

                <hr>

                <h6 class="fw-bold mb-3">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Recipients
                </h6>

                <div
                    class="table-responsive border rounded"
                    style="max-height:220px; overflow-y:auto;">

                    <table class="table table-hover table-sm mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Department</th>
                            </tr>

                        </thead>
                        <tbody>

                            <?php if (!empty($row['recipients'])): ?>

                                <?php foreach ($row['recipients'] as $employee): ?>

                                    <tr>

                                        <td><?= htmlspecialchars($employee['employee_code']) ?></td>

                                        <td>
                                            <?= htmlspecialchars($employee['first_name']) ?>
                                            <?= htmlspecialchars($employee['last_name']) ?>
                                        </td>

                                        <td><?= htmlspecialchars($employee['department']) ?></td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="3" class="text-center text-muted">

                                        No recipients found.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>