<h2 class="text-center fw-bold display-4">Request Types Management</h2>

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
                                <?= !empty($type['icon']) ? "<i class='fa-solid {$type['icon']} fa-lg'></i>" : 'N/A' ?>
                            </td>
                            <td><?= $type['requires_attachment'] ? 'Yes' : 'No' ?></td>
                            <td>
                                <span class="badge <?= $type['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $type['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>