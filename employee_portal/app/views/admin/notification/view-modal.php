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

                        <th width="120" class="text-muted">
                            Title
                        </th>

                        <td>
                            <?= htmlspecialchars($viewNotification['title'] ?? '') ?>
                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted align-top">
                            Message
                        </th>

                        <td>

                            <div class="border rounded p-2 bg-light small"
                                style="white-space:pre-wrap; max-height:120px; overflow:auto;">

                                <?= htmlspecialchars($viewNotification['message'] ?? '') ?>

                            </div>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Type
                        </th>

                        <td>

                            <span class="badge bg-primary">

                                <?= ucfirst($viewNotification['type'] ?? '') ?>

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Priority
                        </th>

                        <td>

                            <?php

                            $priorityClass = [
                                'normal' => 'success',
                                'important' => 'warning text-dark',
                                'urgent' => 'danger'
                            ];

                            ?>

                            <span class="badge bg-<?= $priorityClass[$viewNotification['priority']] ?? 'secondary'; ?>">

                                <?= ucfirst($viewNotification['priority'] ?? '') ?>

                            </span>

                        </td>

                    </tr>

                    <tr>

                        <th class="text-muted">
                            Created
                        </th>

                        <td>

                            <?= date('M d, Y h:i A', strtotime($viewNotification['created_at'])) ?>

                        </td>

                    </tr>

                </table>

                <label class="fw-semibold small mb-2">
                    Recipients
                </label>

                <div class="table-responsive border rounded"
                    style="max-height:180px; overflow-y:auto;">

                    <table class="table table-sm table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>Employee No.</th>

                                <th>Name</th>

                                <th>Department</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($recipientList)): ?>

                                <?php foreach ($recipientList as $recipient): ?>

                                    <tr>

                                        <td><?= htmlspecialchars($recipient['employee_no']) ?></td>

                                        <td><?= htmlspecialchars($recipient['full_name']) ?></td>

                                        <td><?= htmlspecialchars($recipient['department']) ?></td>

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

            <div class="modal-footer py-2">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>