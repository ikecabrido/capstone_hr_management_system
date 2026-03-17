<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="index.php?url=employee-update">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-user-pen me-2"></i>
                        Edit Employee Information
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Hidden ID -->
                    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee['employee_id']); ?>">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($employee['name']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control"
                                value="<?= htmlspecialchars($employee['contact_number'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($employee['email']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control"
                                value="<?= htmlspecialchars($employee['department']); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control"
                                value="<?= htmlspecialchars($employee['position']); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Employment Status</label>
                            <select name="employment_status" class="form-control">
                                <option value="Active" <?= $employee['employment_status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $employee['employment_status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($employee['address'] ?? ''); ?></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>