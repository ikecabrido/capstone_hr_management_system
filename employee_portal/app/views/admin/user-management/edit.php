<?php foreach ($users as $user): ?>
    <div class="modal fade" id="editUserModal<?= $user['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="index.php?url=admin-update-user" method="POST" class="modal-content">

                <input type="hidden" name="id" value="<?= $user['id']; ?>">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit User
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Username
                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($user['username']); ?>"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']); ?>">
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save me-2"></i>
                        Save Changes

                    </button>

                </div>

            </form>
        </div>
    </div>
<?php endforeach; ?>