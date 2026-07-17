<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold text-primary mb-1 display-5">
                        <i class="fas fa-bell me-2"></i>
                        Notification Management
                    </h1>

                    <p class="text-muted mb-0 fs-5">
                        Create and manage employee notifications.
                    </p>

                </div>

                <button
                    class="btn btn-primary btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#createNotificationModal">

                    <i class="fas fa-plus me-2"></i>
                    Create Notification

                </button>

            </div>
            <?php require __DIR__ . '/../../../views/partials/notif.php' ?>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <div class="row">

                        <div class="col-md-4">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search notifications...">
                        </div>

                    </div>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

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

                                                <a
                                                    href="index.php?url=admin-notification&view=<?= $row['notification_id']; ?>"
                                                    class="btn mr-1 btn-sm btn-info text-white">

                                                    <i class="fas fa-eye"></i>

                                                </a>

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
                                        <?php require __DIR__ . '/edit-modal.php'; ?>
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

                    </div>

                </div>

            </div>
            <?php require __DIR__ . '/create-modal.php' ?>
            <?php require __DIR__ . '/view-modal.php'; ?>

        </div>
    </div>
</div>

<?php if (!empty($viewNotification)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $('#viewNotificationModal').modal('show');

            // Remove the "view" parameter from the URL
            window.history.replaceState({},
                document.title,
                'index.php?url=admin-notification'
            );

        });
    </script>
<?php endif; ?>