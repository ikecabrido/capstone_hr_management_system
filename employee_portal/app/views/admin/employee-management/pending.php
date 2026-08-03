<!-- Employment Profile Modal -->
<div
    class="modal fade"
    id="employmentModal<?= $employee['employee_id']; ?>"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content rounded-4 border-0 shadow">

            <div class="modal-header bg-warning text-dark">

                <h5 class="modal-title fw-bold">
                    <i class="fas fa-briefcase me-2"></i>
                    Employment Profile
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <form
                action="index.php?url=employee-hr-store-pending"
                method="POST">

                <input
                    type="hidden"
                    name="employee_id"
                    value="<?= $employee['employee_id']; ?>">

                <div class="modal-body">

                    <div class="row">

                        <!-- Employee Code -->
                        <label class="form-label">
                            Employee Code
                        </label>
                        <?php if (!empty($employee['employee_code'])): ?>

                            <input
                                type="text"
                                class="form-control bg-light"
                                value="<?= htmlspecialchars($employee['employee_code']); ?>"
                                readonly>

                        <?php else: ?>

                            <div class="input-group">

                                <span class="input-group-text">
                                    EMP-
                                </span>

                                <input
                                    type="text"
                                    name="employee_code"
                                    class="form-control"
                                    placeholder="000001">

                            </div>

                        <?php endif; ?>

                        <!-- Department -->
                        <label class="form-label">
                            Department
                        </label>
                        <?php if (!empty($employee['department_id'])): ?>

                            <input
                                type="text"
                                class="form-control bg-light"
                                value="<?= htmlspecialchars($employee['department']); ?>"
                                readonly>

                        <?php else: ?>

                            <select
                                name="department_id"
                                class="form-select">

                                <option value="">Select Department</option>

                                <?php foreach ($departmentHRInfos as $department): ?>

                                    <option value="<?= $department['id']; ?>">
                                        <?= htmlspecialchars($department['department_name']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        <?php endif; ?>

                        <!-- Position -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Position
                            </label>

                            <?php if (!empty($employee['position_title_enum'])): ?>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employee['position_title_enum']); ?>"
                                    readonly>

                            <?php else: ?>

                                <select
                                    name="position_title_enum"
                                    class="form-select">

                                    <option value="">Select Position</option>

                                    <option>Instructor</option>
                                    <option>Professor</option>
                                    <option>HR Manager</option>
                                    <option>Registrar Staff</option>

                                </select>

                            <?php endif; ?>

                        </div>

                        <!-- Employment Status -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employment Status
                            </label>

                            <?php if (!empty($employee['employment_status'])): ?>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employee['employment_status']); ?>"
                                    readonly>

                            <?php else: ?>

                                <select
                                    name="employment_status"
                                    class="form-select">

                                    <option value="">Select Status</option>
                                    <option>Probationary</option>
                                    <option>Regular</option>
                                    <option>Contractual</option>

                                </select>

                            <?php endif; ?>

                        </div>

                        <!-- Employment Type -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employment Type
                            </label>

                            <?php if (!empty($employee['employment_type'])): ?>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($employee['employment_type']); ?>"
                                    readonly>

                            <?php else: ?>

                                <select
                                    name="employment_type"
                                    class="form-select">

                                    <option value="">Select Type</option>
                                    <option>Full-time</option>
                                    <option>Part-time</option>

                                </select>

                            <?php endif; ?>

                        </div>

                        <!-- Hire Date -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Hire Date
                            </label>

                            <?php if (!empty($employee['hire_date'])): ?>

                                <input
                                    type="date"
                                    class="form-control bg-light"
                                    value="<?= $employee['hire_date']; ?>"
                                    readonly>

                            <?php else: ?>

                                <input
                                    type="date"
                                    name="hire_date"
                                    class="form-control"
                                    value="<?= date('Y-m-d'); ?>">

                            <?php endif; ?>

                        </div>

                        <!-- Regular Date -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Regular Date
                            </label>

                            <input
                                type="date"
                                name="regular_date"
                                class="form-control"
                                value="<?= $employee['regular_date']; ?>">

                        </div>

                        <!-- Unit Load -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Unit Load
                            </label>

                            <input
                                type="number"
                                name="unit_load"
                                class="form-control"
                                value="<?= $employee['unit_load']; ?>">

                        </div>

                        <!-- Faculty Notes -->
                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Faculty Notes
                            </label>

                            <textarea
                                name="faculty_notes"
                                rows="3"
                                class="form-control"><?= htmlspecialchars($employee['faculty_notes']); ?></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save me-1"></i>
                        Save Employment Data

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>