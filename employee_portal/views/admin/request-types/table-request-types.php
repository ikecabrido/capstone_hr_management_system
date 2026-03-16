<h2 class="text-center fw-bold display-4">
    Request Types Management
</h2>

<div class="d-flex flex-column flex-md-row justify-content-end align-items-center mb-4">
    <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm"
        data-bs-toggle="modal"
        data-bs-target="#createRequestTypeModal">
        ➕ Create Request Type
    </button>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Requires Attachment</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($requestTypes as $type): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($type['name']) ?></td>
                            <td><?= htmlspecialchars($type['description']) ?></td>
                            <td class="text-center">
                                <?php if (!empty($type['icon'])): ?>
                                    <i class="fa-solid <?= htmlspecialchars($type['icon']) ?> fa-lg"></i>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $type['requires_attachment'] ? 'Yes' : 'No' ?>
                            </td>
                            <td>
                                <?php if ($type['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editRequestTypeModal<?= $type['id']; ?>">
                                    Edit
                                </button>
                                <form method="POST"
                                    action="index.php?url=admin-request-types-delete"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this request type?');">
                                    <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <?php require __DIR__ . '/edit-request-type-modal.php'; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/create-request-type-modal.php'; ?>