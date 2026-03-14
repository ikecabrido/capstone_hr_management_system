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
                    <?= !empty($type['icon']) ? "<i class='fa-solid {$type['icon']} fa-lg'></i>" : 'N/A' ?>
                </td>
                <td><?= $type['requires_attachment'] ? 'Yes' : 'No' ?></td>
                <td>
                    <span class="badge <?= $type['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $type['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="text-end">
                    <button
                        class="btn btn-sm btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#editRequestTypeModal<?= $type['id']; ?>">
                        Edit
                    </button>
                    <form method="POST"
                        action="index.php?url=request-types-delete"
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