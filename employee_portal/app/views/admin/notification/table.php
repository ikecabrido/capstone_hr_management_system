<table class="table table-hover align-middle mb-0">

    <thead class="table-light">

        <tr>

            <th>ID</th>

            <th>Title</th>

            <th>Type</th>

            <th>Priority</th>

            <th>Recipients</th>

            <th>Created By</th>

            <th>Date</th>

            <th width="150">Actions</th>

        </tr>

    </thead>

    <tbody>

        <?php if (!empty($notification)): ?>

            <?php foreach ($notification as $row): ?>

                <tr>

                    <td><?= $row['notification_id']; ?></td>

                    <td>
                        <strong><?= htmlspecialchars($row['title']); ?></strong>
                    </td>

                    <td>

                        <?php

                        $typeClass = [
                            'announcement' => 'primary',
                            'payroll' => 'success',
                            'leave' => 'warning text-dark',
                            'training' => 'info text-dark',
                            'performance' => 'secondary',
                            'document' => 'dark',
                            'meeting' => 'info',
                            'compliance' => 'danger',
                            'general' => 'secondary'
                        ];

                        ?>

                        <span class="badge bg-<?= $typeClass[$row['type']] ?? 'secondary'; ?>">
                            <?= ucfirst($row['type']); ?>
                        </span>

                    </td>

                    <td>

                        <?php

                        $priorityClass = [
                            'normal' => 'success',
                            'important' => 'warning text-dark',
                            'urgent' => 'danger'
                        ];

                        ?>

                        <span class="badge bg-<?= $priorityClass[$row['priority']] ?? 'secondary'; ?>">
                            <?= ucfirst($row['priority']); ?>
                        </span>

                    </td>

                    <td>
                        <?php if ($row['recipient_count'] > 0): ?>

                            <span class="badge bg-success">
                                <?= $row['recipient_count']; ?>
                                Employee<?= $row['recipient_count'] > 1 ? 's' : ''; ?>
                            </span>

                        <?php else: ?>

                            <span class="text-muted">
                                No recipients
                            </span>

                        <?php endif; ?>
                    </td>

                    <td class="flex">
                        Admin
                        <small class="ml-1 text-muted d-block">
                            ID: <?= $row['created_by_user_id']; ?>
                        </small>
                    </td>

                    <td><?= date('M d, Y', strtotime($row['created_at'])); ?></td>

                    <td class="flex">
                        <button
                            type="button"
                            class="btn btn-info btn-sm text-white mr-1 viewBtn"
                            data-id="<?= $row['notification_id']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#viewNotificationModal<?= $row['notification_id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>

                        <button
                            class="btn mr-1 btn-sm btn-warning text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#editNotificationModal<?= $row['notification_id']; ?>">

                            <i class="fas fa-edit"></i>
                        </button>

                        <form
                            action="index.php?url=notification-delete"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this notification? This action cannot be undone.');">

                            <input
                                type="hidden"
                                name="notification_id"
                                value="<?= $row['notification_id']; ?>">

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>

            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    No notifications found.
                </td>
            </tr>

        <?php endif; ?>

    </tbody>

</table>
<?php if (!empty($notification)): ?>

    <?php foreach ($notification as $row): ?>

        <?php require __DIR__ . '/edit-modal.php'; ?>
        <?php require __DIR__ . '/view-modal.php'; ?>

    <?php endforeach; ?>

<?php endif; ?>