<?php require_once __DIR__ . '/../partials/notif.php'; ?>

<div class="container py-5">

    <div class="card shadow-lg border-0 rounded-4 mx-auto overflow-hidden" style="max-width: 650px;">

        <div class="bg-primary bg-gradient text-white px-4 py-4">
            <h4 class="fw-bold mb-1">My Profile</h4>
            <p class="mb-0 small opacity-75">
                Manage your account information
            </p>
        </div>

        <div class="card-body px-4 py-4">

            <div class="mb-3">
                <label class="text-muted small">Full Name</label>
                <div class="fw-semibold fs-5">
                    <?= htmlspecialchars($user['name']) ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="text-muted small">Email Address</label>
                <div class="fw-semibold">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="text-muted small">Role</label>
                    <div>
                        <span class="badge rounded-pill px-3 py-2 
                            <?= $user['isAdmin'] ? 'bg-danger' : 'bg-secondary' ?>">
                            <?= $user['isAdmin'] ? 'Administrator' : 'User' ?>
                        </span>
                    </div>
                </div>

                <div class="col-6 mb-3">
                    <label class="text-muted small">Account Status</label>
                    <div>
                        <span class="badge rounded-pill px-3 py-2 
                            <?= $user['isActive'] ? 'bg-success' : 'bg-dark' ?>">
                            <?= $user['isActive'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-4">

                <a href="index.php?url=employee-profile-index"
                    class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                    Edit Profile
                </a>

                <button class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#changePasswordModal">
                    Change Password
                </button>

            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/change-password-modal.php'; ?>