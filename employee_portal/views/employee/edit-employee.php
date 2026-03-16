<div class="flex-grow-1 ml-16 transition-all">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>

    <main class="flex-grow-1 bg-light min-vh-100 py-4">
        <?php require_once __DIR__ . '/../partials/notif.php'; ?>

        <div class="container-fluid">
            <div class="flex flex-wrap justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-2">Edit Employee: <?= htmlspecialchars($employee['name']) ?></h2>
                <a href="index.php?url=employee-index" class="btn btn-outline-secondary btn-sm">
                    ← Back
                </a>
            </div>
            <h2 class="text-center fw-bold display-4">
                Manage Profile
            </h2>
            <form method="POST" class="text-sm w-[75%] ml-[10%]" action="index.php?url=admin-employees-update&id=<?= htmlspecialchars($employee['user_id'] ?? '') ?>" enctype="multipart/form-data">

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2 text-primary">Account Information</h6>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold small">Full Name</label>
                                <input id="name" type="text" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small">Email Address</label>
                                <input id="email" type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['email']) ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2 text-primary">Employee Information</h6>

                        <div class="mb-2">
                            <label for="employee_id" class="form-label fw-semibold small">Employee ID</label>
                            <input id="employee_id" type="text" name="employee_id" class="form-control form-control-sm" value="<?= str_pad(htmlspecialchars($employee['employee_id']), 4, '0', STR_PAD_LEFT) ?>" required>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="employee_type" class="form-label fw-semibold small">Employee Type</label>
                                <select name="employee_type" class="form-select form-select-sm" required>
                                    <option value="Faculty" <?= $employee['employee_type'] === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                                    <option value="Staff" <?= $employee['employee_type'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="employment_status" class="form-label fw-semibold small">Employment Status</label>
                                <select name="employment_status" class="form-select form-select-sm" required>
                                    <option value="Full-time" <?= $employee['employment_status'] === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                    <option value="Part-time" <?= $employee['employment_status'] === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                    <option value="Contractual" <?= $employee['employment_status'] === 'Contractual' ? 'selected' : '' ?>>Contractual</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="department" class="form-label fw-semibold small">Department</label>
                                <select id="department" name="department" class="form-select form-select-sm" required>
                                    <option value="bsis" <?= $employee['department'] === 'bsis' ? 'selected' : '' ?>>BSIS</option>
                                    <option value="registrar" <?= $employee['department'] === 'registrar' ? 'selected' : '' ?>>Registrar</option>
                                    <option value="staff" <?= $employee['department'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="bscrim" <?= $employee['department'] === 'bscrim' ? 'selected' : '' ?>>BSCrim</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label fw-semibold small">Position</label>
                                <input id="position" type="text" name="position" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['position']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="campus" class="form-label fw-semibold small">Campus</label>
                                <select id="campus" name="campus" class="form-select form-select-sm" required>
                                    <option value="bsis" <?= $employee['campus'] === 'bsis' ? 'selected' : '' ?>>San Jose Delmonte</option>
                                    <option value="registrar" <?= $employee['campus'] === 'registrar' ? 'selected' : '' ?>>Novaliches</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="date_hired" class="form-label fw-semibold small">Date Hired</label>
                                <input id="date_hired" type="date" name="date_hired" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['date_hired']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Profile Image</label>
                                <input type="file" name="profile_image" id="profileImageInput" class="form-control form-control-sm" accept="image/*">
                                <div class="mt-2">
                                    <img id="profileImagePreview" src="<?= !empty($employee['profile_image']) ? '/hrmsys/public/images/' . $employee['profile_image'] : '/hrmsys/public/assets/images/default-profile.jpg' ?>" class="rounded-circle border border-2 shadow-sm w-32 h-32 object-cover" alt="Profile Preview">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Cover Image</label>
                                <input type="file" name="cover_image" id="coverImageInput" class="form-control form-control-sm" accept="image/*">
                                <div class="mt-2">
                                    <img id="coverImagePreview" src="<?= !empty($employee['cover_image']) ? '/hrmsys/public/images/' . $employee['cover_image'] : '/hrmsys/public/assets/images/default-cover.jpg' ?>" class="rounded-xl shadow-sm w-full h-48 object-cover" alt="Cover Preview">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm">Save Changes</button>
                    <a href="index.php?url=employee-index" class="btn btn-outline-secondary btn-sm shadow-sm">Back</a>
                </div>
            </form>
        </div>
    </main>
</div>