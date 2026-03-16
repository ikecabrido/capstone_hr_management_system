<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?url=profile-save-password" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" id="new-password" class="form-control" required>
                    <small class="form-text text-muted">At least 8 characters, mix letters & numbers</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="confirm-password" class="form-control" required>
                    <small id="passwordMatch" class="form-text"></small>
                </div>
                <div class="progress mt-3 rounded-pill" style="height: 6px;">
                    <div id="strengthBar"
                        class="progress-bar rounded-pill"
                        style="width: 0%; transition: 0.4s;">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning" id="savePasswordBtn">Save Password</button>
            </div>
        </form>
    </div>
</div>