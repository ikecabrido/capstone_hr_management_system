<div class="modal fade"
    id="editUserModal<?= $user['id']; ?>"
    tabindex="-1"
    aria-labelledby="editUserModalLabel<?= $user['id']; ?>"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="index.php?url=admin-users-update">

                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id"
                        value="<?= $user['id']; ?>">

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($user['name']); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select" required>
                            <option value="1"
                                <?= $user['isAdmin'] == 1 ? 'selected' : '' ?>>
                                Admin
                            </option>

                            <option value="0"
                                <?= $user['isAdmin'] == 0 ? 'selected' : '' ?>>
                                User
                            </option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
