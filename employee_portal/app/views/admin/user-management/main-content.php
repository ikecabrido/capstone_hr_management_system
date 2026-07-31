<?php require __DIR__ . '/style.php'; ?>
<?php require __DIR__ . '/style.php'; ?>

<div class="w-full mt-4">
    <div class="content-wrapper px-4 py-4">

        <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

        <div class="card border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="card-header bg-white border-0 py-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h1 class="fw-bold text-4xl mb-1">
                            <i class="fas fa-users me-2"></i>
                            User Management
                        </h1>

                        <small class="text-muted">
                            Total Users:
                            <strong><?= count($users) ?></strong>
                        </small>
                    </div>

                    <button
                        class="btn btn-primary px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#createUserModal">

                        <i class="fas fa-user-plus me-2"></i>
                        Create User

                    </button>

                </div>

            </div>

            <!-- Table -->
            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="80">ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th width="220">Created</th>
                                <th width="120" class="text-center">Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($users)): ?>

                                <?php foreach ($users as $user): ?>

                                    <tr>

                                        <td>

                                            <span class="fw-bold text-primary">
                                                #<?= $user['id']; ?>
                                            </span>

                                        </td>

                                        <td>

                                            <div class="d-flex align-items-center">

                                                <div
                                                    class="rounded-circle bg-primary text-white fw-bold d-flex justify-content-center align-items-center me-3"
                                                    style="width:42px;height:42px;">

                                                    <?= strtoupper(substr($user['username'], 0, 1)); ?>

                                                </div>

                                                <div>

                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($user['username']); ?>
                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                        <td>

                                            <?= !empty($user['email'])
                                                ? htmlspecialchars($user['email'])
                                                : '<span class="text-muted">No email</span>'; ?>

                                        </td>
                                        <td>

                                            <?= date('M d, Y', strtotime($user['created_at'])) ?>

                                            <br>

                                            <small class="text-muted">

                                                <?= date('h:i A', strtotime($user['created_at'])) ?>

                                            </small>

                                        </td>

                                        <td class="text-center text-nowrap">

                                            <!-- Edit -->
                                            <button
                                                class="btn btn-sm btn-outline-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal<?= $user['id']; ?>"
                                                title="Edit User">

                                                <i class="fas fa-edit"></i>
                                                Edit

                                            </button>

                                            <!-- Set as Admin -->
                                            <?php $isAdmin = ((int)$user['is_admin'] === 1); ?>

                                            <form action="index.php?url=admin-toggle-user" method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $user['id']; ?>">

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm <?= $isAdmin ? 'btn-outline-secondary' : 'btn-outline-success'; ?>"
                                                    onclick="return confirm('<?= $isAdmin
                                                                                    ? 'Remove administrator privileges from this user?'
                                                                                    : 'Grant administrator privileges to this user?'; ?>')">

                                                    <i class="fas <?= $isAdmin ? 'fa-user-shield' : 'fa-crown'; ?>"></i>
                                                    <?= $isAdmin ? 'Remove Admin' : 'Set as Admin'; ?>
                                                </button>
                                            </form>
                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>

                                        <h5 class="text-muted">
                                            No users found
                                        </h5>

                                        <small class="text-muted">
                                            Click "Create User" to add your first account.
                                        </small>

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

<?php require __DIR__ . '/create.php'; ?>
<?php require __DIR__ . '/edit.php'; ?>
<script>
    function togglePassword(inputId, iconId) {

        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {

            input.type = "text";

            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");

        } else {

            input.type = "password";

            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");

        }
    }
</script>