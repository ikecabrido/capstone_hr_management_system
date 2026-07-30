<div class="modal fade" id="changeNameModal" tabindex="-1" aria-labelledby="changeNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 shadow">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="changeNameModalLabel">
                    Change Username
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <form action="index.php?url=update-name" method="POST">
                <div class="modal-body">

                    <!-- Current Name -->
                    <div class="mb-3">
                        <label class="form-label text-muted">Current userame</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($userInfos['username']); ?>" 
                               disabled>
                    </div>

                    <!-- New Name -->
                    <div class="mb-3">
                        <label class="form-label">New Username</label>
                        <input type="text" 
                               name="username"
                               class="form-control" 
                               placeholder="Enter new username" 
                               required>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>