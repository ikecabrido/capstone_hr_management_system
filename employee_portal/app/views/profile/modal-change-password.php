<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form action="index.php?url=update-password" method="POST" onsubmit="return validatePassword()">

                <div class="modal-body">

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" 
                               id="newPassword"
                               name="password"
                               class="form-control"
                               placeholder="Enter new password"
                               required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" 
                               id="confirmPassword"
                               class="form-control"
                               placeholder="Confirm password"
                               required>
                    </div>

                    <!-- Notice -->
                    <div class="form-text text-muted">
                        Password must be at least 6 characters.
                    </div>

                    <!-- Error Message -->
                    <div id="passwordError" class="text-danger mt-2" style="display:none;"></div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dark">
                        Update Password
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>