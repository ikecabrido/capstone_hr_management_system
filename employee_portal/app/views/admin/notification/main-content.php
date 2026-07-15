<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h3 class="fw-bold text-primary mb-1">
                            <i class="fas fa-bell me-2"></i>Notification Management
                        </h3>

                        <p class="text-muted mb-0">
                            Create and manage employee notifications.
                        </p>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Notification
                    </button>

                </div>

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

                                                <td>N/A</td>

                                                <td>User #<?= $row['created_by_user_id']; ?></td>

                                                <td><?= date('M d, Y', strtotime($row['created_at'])); ?></td>

                                                <td>

                                                    <button class="btn btn-sm btn-info text-white">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-warning text-white">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

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

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>