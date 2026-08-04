<!-- Edit Employee Modal -->
<div class="modal fade"
    id="editEmployeeModal<?= $employee['employee_id']; ?>"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <form
                action="index.php?url=employee-hr-update"
                method="POST">

                <input
                    type="hidden"
                    name="employee_id"
                    value="<?= $employee['employee_id']; ?>">

                <!-- Header -->
                <div class="modal-header bg-warning">

                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-edit me-2"></i>
                        Edit Employment Information
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- Employee Information -->
                    <h5 class="fw-bold text-success mb-3">
                        Employee Information
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employee Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>"
                                readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employee Code
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    EMP-
                                </span>
                                <input
                                    type="text"
                                    name="employee_code"
                                    class="form-control"
                                    placeholder="00001"
                                    maxlength="5"
                                    inputmode="numeric"
                                    pattern="\d{1,5}"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5);"
                                    value="<?= htmlspecialchars(str_replace('EMP-', '', $employee['employee_code'] ?? '')); ?>">

                            </div>

                        </div>

                    </div>

                    <hr>

                    <!-- Position -->
                    <h5 class="fw-bold text-success mb-3">
                        Position Information
                    </h5>

                    <div class="row">

                        <!-- Department -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select
                                name="department_id"
                                class="form-select">

                                <option value="">Select Department</option>

                                <?php foreach ($departmentHRInfos as $department): ?>

                                    <option
                                        value="<?= $department['id']; ?>"
                                        <?= $employee['department_id'] == $department['id'] ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($department['department_name']); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- Position -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Position
                            </label>

                            <select
                                name="position_id"
                                class="form-select">

                                <option value="">Select Position</option>

                                <?php foreach ($positionHRInfos as $position): ?>

                                    <option
                                        value="<?= $position['id']; ?>"
                                        <?= $employee['position_id'] == $position['id'] ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($position['title']); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <!-- Position Title -->
                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Position Title
                            </label>

                            <select
                                name="position_title_enum"
                                class="form-select">

                                <option value="">
                                    Select Position Title
                                </option>

                                <?php
                                $titles = [
                                    'College President',
                                    'Vice President for Academic Affairs',
                                    'Vice President for Administration',
                                    'Vice President for Finance',
                                    'Dean',
                                    'Associate Dean',
                                    'Assistant Dean',
                                    'Department Chairperson',
                                    'Program Head',
                                    'Professor',
                                    'Associate Professor',
                                    'Assistant Professor',
                                    'Instructor',
                                    'Lecturer',
                                    'Clinical Instructor',
                                    'University Registrar',
                                    'Assistant Registrar',
                                    'Registrar Staff',
                                    'Admissions Officer',
                                    'Enrollment Officer',
                                    'HR Director',
                                    'HR Manager',
                                    'HR Officer',
                                    'HR Assistant',
                                    'Administrative Officer',
                                    'Administrative Assistant',
                                    'Executive Assistant',
                                    'Secretary',
                                    'Finance Director',
                                    'Accountant',
                                    'Accounting Staff',
                                    'Cashier',
                                    'Payroll Officer',
                                    'Budget Officer',
                                    'IT Director',
                                    'IT Manager',
                                    'System Administrator',
                                    'Network Administrator',
                                    'IT Support Specialist',
                                    'Programmer',
                                    'Web Developer',
                                    'Guidance Counselor',
                                    'Director of Student Affairs',
                                    'Student Affairs Officer',
                                    'School Nurse',
                                    'Chief Librarian',
                                    'Librarian',
                                    'Library Staff',
                                    'Facilities Manager',
                                    'Maintenance Staff',
                                    'Driver',
                                    'Janitor',
                                    'Security Guard',
                                    'Utility Worker'
                                ];

                                foreach ($titles as $title):
                                ?>

                                    <option
                                        value="<?= $title; ?>"
                                        <?= $employee['position_title_enum'] == $title ? 'selected' : ''; ?>>

                                        <?= $title; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <hr>

                    <!-- Employment -->
                    <h5 class="fw-bold text-success mb-3">
                        Employment Details
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Employment Status
                            </label>

                            <select
                                name="employment_status"
                                class="form-select">

                                <?php foreach (['Probationary', 'Regular', 'Contractual'] as $status): ?>

                                    <option
                                        value="<?= $status; ?>"
                                        <?= $employee['employment_status'] == $status ? 'selected' : ''; ?>>

                                        <?= $status; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Employment Type
                            </label>

                            <select
                                name="employment_type"
                                class="form-select">

                                <?php foreach (['Full-time', 'Part-time'] as $type): ?>

                                    <option
                                        value="<?= $type; ?>"
                                        <?= $employee['employment_type'] == $type ? 'selected' : ''; ?>>

                                        <?= $type; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Hire Date
                            </label>

                            <input
                                type="date"
                                name="hire_date"
                                class="form-control"
                                value="<?= $employee['hire_date']; ?>">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Regularization Date
                            </label>

                            <input
                                type="date"
                                name="regular_date"
                                class="form-control"
                                value="<?= $employee['regular_date']; ?>">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Teaching Unit Load
                            </label>

                            <input
                                type="number"
                                name="unit_load"
                                class="form-control"
                                value="<?= $employee['unit_load']; ?>">

                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-bold text-success mb-3">
                        Additional Information
                    </h5>

                    <textarea
                        name="faculty_notes"
                        rows="4"
                        class="form-control"><?= htmlspecialchars($employee['faculty_notes']); ?></textarea>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save me-1"></i>
                        Update Employment Data

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>