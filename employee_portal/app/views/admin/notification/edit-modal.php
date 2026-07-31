<div class="modal fade"
    id="editNotificationModal<?= $row['notification_id']; ?>"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form method="POST"
            action="index.php?url=notification-update">

            <div class="modal-content shadow-sm">

                <div class="modal-header">

                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Edit Notification
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
                        name="notification_id"
                        value="<?= $row['notification_id']; ?>">

                    <div class="mb-3">

                        <label class="form-label fw-semibold small">
                            Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control form-control-sm"
                            value="<?= htmlspecialchars($row['title']); ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold small">
                            Message
                        </label>

                        <textarea
                            name="message"
                            rows="4"
                            class="form-control form-control-sm"
                            required><?= htmlspecialchars($row['message']); ?></textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">
                                Type
                            </label>

                            <select
                                class="form-select form-select-sm"
                                name="type">

                                <?php
                                $types = [
                                    'announcement',
                                    'payroll',
                                    'leave',
                                    'training',
                                    'performance',
                                    'document',
                                    'meeting',
                                    'compliance',
                                    'general'
                                ];

                                foreach ($types as $type):
                                ?>

                                    <option
                                        value="<?= $type; ?>"
                                        <?= $row['type'] == $type ? 'selected' : ''; ?>>

                                        <?= ucfirst($type); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label fw-semibold small">
                                Priority
                            </label>

                            <select
                                class="form-select form-select-sm"
                                name="priority">

                                <?php
                                $priorities = [
                                    'normal',
                                    'important',
                                    'urgent'
                                ];

                                foreach ($priorities as $priority):
                                ?>

                                    <option
                                        value="<?= $priority; ?>"
                                        <?= $row['priority'] == $priority ? 'selected' : ''; ?>>

                                        <?= ucfirst($priority); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <label class="form-label fw-semibold small mb-0">
                            Recipients
                        </label>

                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            onclick="selectAllEmployees<?= $row['notification_id']; ?>()">

                            <i class="fas fa-check-double me-1"></i>
                            Select All

                        </button>

                    </div>

                    <select
                        id="editEmployees<?= $row['notification_id']; ?>"
                        class="form-select form-select-sm"
                        name="employee_ids[]"
                        multiple
                        size="8">

                        <?php
                        $selectedEmployees = $this->recipientModel
                            ->getEmployeeIdsByNotification($row['notification_id']);

                        foreach ($employeeList as $employee):
                        ?>

                            <option
                                value="<?= $employee['employee_id']; ?>"
                                <?= in_array($employee['employee_id'], $selectedEmployees) ? 'selected' : ''; ?>>

                                <?= htmlspecialchars($employee['first_name']); ?> <?= htmlspecialchars($employee['last_name']); ?>
                                (<?= htmlspecialchars($employee['employee_code']); ?>)

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <small class="text-muted">
                        Hold <strong>Ctrl</strong> (Windows) or
                        <strong>Cmd</strong> (Mac) to select multiple employees.
                    </small>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning btn-sm">

                        <i class="fas fa-save me-1"></i>
                        Update Notification

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>
    function selectAllEmployees<?= $row['notification_id']; ?>() {

        const select = document.getElementById(
            'editEmployees<?= $row['notification_id']; ?>'
        );

        for (let option of select.options) {
            option.selected = true;
        }
    }
</script>