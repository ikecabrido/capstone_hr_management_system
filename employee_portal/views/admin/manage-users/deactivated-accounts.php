<h4 class="mb-3 text-muted fw-semibold">Deactivated Accounts</h4>
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-4"></div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inactiveUsers)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No deactivated accounts
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php $i = 1; ?>
                        <?php foreach ($inactiveUsers as $user): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?= $user['isAdmin']
                                        ? '<span class="badge bg-primary">Admin</span>'
                                        : '<span class="badge bg-secondary">User</span>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Inactive</span>
                                </td>
                                <td class="text-end">
                                    <form method="POST"
                                        action="index.php?url=admin-users-activate"
                                        class="d-inline"
                                        onsubmit="return confirm('Activate this user?');">

                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                                        <button class="btn btn-sm btn-outline-success">
                                            Activate
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>