<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Register New Employee</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="index.php?url=admin-employees-create" enctype="multipart/form-data">
                <div class="modal-body">

                    <h6 class="text-muted mb-3">Account Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" id="profileImageInput" class="form-control" accept="image/*">

                            <div class="mt-2">
                                <img id="profileImagePreview" src="<?= $profileImage ?? '/hrmsys/public/assets/images/default-profile.jpg' ?>"
                                    alt="Profile Preview" class="rounded-circle border border-2 border-gray-300 shadow-sm w-32 h-32 object-cover">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" id="coverImageInput" class="form-control" accept="image/*">

                            <div class="mt-2">
                                <img id="coverImagePreview" src="<?= $coverImage ?? '/hrmsys/public/assets/images/default-cover.jpg' ?>"
                                    alt="Cover Preview" class="rounded-xl shadow-sm w-full h-48 object-cover">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Employee Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" required placeholder="e.g. xxx01">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee Type</label>
                            <select name="employee_type" class="form-select" required>
                                <option value="">Select type</option>
                                <option value="Faculty">Faculty</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-select" required>
                                <option value="">Select status</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contractual">Contractual</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <option value="bsis">BSIS</option>
                                <option value="registrar">Registrar</option>
                                <option value="staff">Staff</option>
                                <option value="bscrim">BSCrim</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Campus</label>
                            <select name="campus" class="form-select" required>
                                <option value="">Select Campus</option>
                                <option value="bsis">San Jose Delmonte</option>
                                <option value="registrar">Novaliches</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Hired</label>
                            <input
                                type="date"
                                name="date_hired"
                                class="form-control"
                                value="<?= isset($employee['date_hired']) ? htmlspecialchars($employee['date_hired']) : date('Y-m-d') ?>"
                                required>
                        </div>
                    </div>
                    <div class="alert alert-info small mb-0">
                        🔐 A system-generated password will be sent to the employee's email.
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Register Employee
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>