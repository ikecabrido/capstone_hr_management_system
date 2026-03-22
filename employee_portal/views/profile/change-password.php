<div class="min-vh-100 d-flex align-items-top justify-content-center bg-light px-3">

    <div class="col-md-5 col-lg-4">

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

            <div class="bg-primary bg-gradient text-white text-center py-4 px-3">
                <h4 class="fw-bold mb-1">Set Your Password</h4>
                <p class="mb-0 small opacity-75">
                    Create a secure password to access your account
                </p>
            </div>

            <div class="card-body p-4">

                <form method="POST"
                    action="index.php?url=profile-save-password"
                    onsubmit="return validateForm()">

                    <input type="hidden"
                        name="token"
                        value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            New Password
                        </label>

                        <input type="password"
                            id="password"
                            name="password"
                            class="form-control rounded-3 shadow-sm py-2"
                            required minlength="8">

                        <small class="text-muted">
                            Minimum 8 characters, include uppercase & numbers
                        </small>

                        <div class="progress mt-3 rounded-pill" style="height: 6px;">
                            <div id="strengthBar"
                                class="progress-bar rounded-pill"
                                style="width: 0%; transition: 0.4s;">
                            </div>
                        </div>

                        <small id="strengthText" class="fw-semibold d-block mt-1"></small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Confirm Password
                        </label>

                        <input type="password"
                            id="confirmPassword"
                            class="form-control rounded-3 shadow-sm py-2"
                            required>

                        <small id="matchError"
                            class="text-danger d-none">
                            Passwords do not match
                        </small>

                        <div class="progress mt-3 rounded-pill" style="height: 6px;">
                            <div id="strengthBar"
                                class="progress-bar rounded-pill"
                                style="width: 0%; transition: 0.4s;">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 py-2 fw-semibold rounded-pill shadow-sm">
                        Save Password
                    </button>

                </form>

            </div>
        </div>

        <p class="text-center text-muted small mt-4">
            © <?= date('Y') ?> HRMSYS.
        </p>

    </div>
</div>