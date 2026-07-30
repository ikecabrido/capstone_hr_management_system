<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form action="index.php?url=admin-create-user"
                method="POST">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createUserModalLabel">
                        <i class="fas fa-hand-holding-heart me-2"></i>
                        Create User Account
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="role" value="employee_portal">
                    <div class="form-field">
                        <label for="email">Email</label>

                        <div class="custom-input-group">

                            <div class="input-icon">
                                <i class="fas fa-envelope me-2"></i>
                            </div>

                            <input
                                type="email"
                                name="email"
                                placeholder="Enter your email"
                                required />
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="username">Username</label>

                        <div class="custom-input-group">

                            <div class="input-icon">
                                <i class="fas fa-user me-2"></i>
                            </div>

                            <input
                                type="username"
                                name="username"
                                placeholder="Enter your username"
                                required />
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="password">Password</label>

                        <div class="custom-input-group">

                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required />

                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword('password', 'passwordIcon')"
                                aria-label="Show password">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>