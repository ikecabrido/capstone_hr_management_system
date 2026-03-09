<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="index.php?url=admin-users-create">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                            name="name"
                            class="form-control"
                            placeholder="Enter full name"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email"
                            name="email"
                            class="form-control"
                            placeholder="user@example.com"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select role</option>
                            <option value="1">Admin</option>
                            <option value="0">User</option>
                        </select>
                    </div>

                    <div class="alert alert-info small mb-0">
                        🔐 A random password will be generated and sent to the user's email.
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>