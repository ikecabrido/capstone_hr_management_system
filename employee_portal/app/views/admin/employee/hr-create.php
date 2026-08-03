<div class="w-full mt-4">
    <div class="content-wrapper px-4 py-4 w-100">

        <?php require __DIR__ . '/../../partials/notif.php'; ?>

        <div class="card shadow-sm border-0 rounded-4 w-100">

            <div class="card-header bg-success text-white rounded-top-4 py-3">

                <h3 class="mb-1 fw-bold">
                    <i class="fas fa-briefcase me-2"></i>
                    Employment Information
                </h3>
                <small>
                    HR Management Section - Complete employee employment details.
                </small>
            </div>
            <div class="card-body">
                <form action="index.php?url=employee-hr-store" method="POST">

                    <input
                        type="hidden"
                        name="employee_id"
                        value="<?= $employeeHRInfo['employee_id']; ?>">


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
                                value="<?= htmlspecialchars(
                                            $employeeHRInfo['first_name'] . ' ' .
                                                $employeeHRInfo['last_name']
                                        ); ?>"
                                readonly>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Employee Code
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    EMP-
                                </span>

                                <input
                                    type="text"
                                    name="employee_code"
                                    class="form-control"
                                    placeholder="000001"
                                    required>

                            </div>

                        </div>

                    </div>


                    <hr>


                    <!-- Position Information -->

                    <h5 class="fw-bold text-success mb-3">
                        Position Information
                    </h5>


                    <div class="row">


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select
                                name="department_id"
                                class="form-select">

                                <option value="">
                                    Select Department
                                </option>
                                <?php foreach ($departmentHRInfos as $department): ?>
                                    <option value="<?= $department['id']; ?>">
                                        <?= $department['department_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                                <!-- Populate from departments table -->

                            </select>

                        </div>



                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Position
                            </label>

                            <select
                                name="department_id"
                                class="form-select">

                                <option value="">
                                    Select Department
                                </option>
                                <?php foreach ($positionHRInfos as $position): ?>
                                    <option value="<?= $position['id']; ?>">
                                        <?= $position['title']; ?>
                                    </option>
                                <?php endforeach; ?>
                                <!-- Populate from departments table -->

                            </select>

                        </div>

                    </div>

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

                                <option value="">Select Position Title</option>

                                <!-- Executive -->
                                <option value="College President">College President</option>
                                <option value="Vice President for Academic Affairs">Vice President for Academic Affairs</option>
                                <option value="Vice President for Administration">Vice President for Administration</option>
                                <option value="Vice President for Finance">Vice President for Finance</option>
                                <option value="Dean">Dean</option>
                                <option value="Associate Dean">Associate Dean</option>
                                <option value="Assistant Dean">Assistant Dean</option>
                                <option value="Department Chairperson">Department Chairperson</option>
                                <option value="Program Head">Program Head</option>

                                <!-- Faculty -->
                                <option value="Professor">Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Instructor">Instructor</option>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Clinical Instructor">Clinical Instructor</option>

                                <!-- Registrar & Admissions -->
                                <option value="University Registrar">University Registrar</option>
                                <option value="Assistant Registrar">Assistant Registrar</option>
                                <option value="Registrar Staff">Registrar Staff</option>
                                <option value="Admissions Officer">Admissions Officer</option>
                                <option value="Enrollment Officer">Enrollment Officer</option>

                                <!-- Human Resources -->
                                <option value="HR Director">HR Director</option>
                                <option value="HR Manager">HR Manager</option>
                                <option value="HR Officer">HR Officer</option>
                                <option value="HR Assistant">HR Assistant</option>

                                <!-- Administration -->
                                <option value="Administrative Officer">Administrative Officer</option>
                                <option value="Administrative Assistant">Administrative Assistant</option>
                                <option value="Executive Assistant">Executive Assistant</option>
                                <option value="Secretary">Secretary</option>

                                <!-- Finance -->
                                <option value="Finance Director">Finance Director</option>
                                <option value="Accountant">Accountant</option>
                                <option value="Accounting Staff">Accounting Staff</option>
                                <option value="Cashier">Cashier</option>
                                <option value="Payroll Officer">Payroll Officer</option>
                                <option value="Budget Officer">Budget Officer</option>

                                <!-- IT -->
                                <option value="IT Director">IT Director</option>
                                <option value="IT Manager">IT Manager</option>
                                <option value="System Administrator">System Administrator</option>
                                <option value="Network Administrator">Network Administrator</option>
                                <option value="IT Support Specialist">IT Support Specialist</option>
                                <option value="Programmer">Programmer</option>
                                <option value="Web Developer">Web Developer</option>

                                <!-- Student Services -->
                                <option value="Guidance Counselor">Guidance Counselor</option>
                                <option value="Director of Student Affairs">Director of Student Affairs</option>
                                <option value="Student Affairs Officer">Student Affairs Officer</option>
                                <option value="School Nurse">School Nurse</option>

                                <!-- Library -->
                                <option value="Chief Librarian">Chief Librarian</option>
                                <option value="Librarian">Librarian</option>
                                <option value="Library Staff">Library Staff</option>

                                <!-- Facilities -->
                                <option value="Facilities Manager">Facilities Manager</option>
                                <option value="Maintenance Staff">Maintenance Staff</option>
                                <option value="Driver">Driver</option>
                                <option value="Janitor">Janitor</option>
                                <option value="Security Guard">Security Guard</option>
                                <option value="Utility Worker">Utility Worker</option>

                            </select>

                        </div>
                    </div>

                    <hr>

                    <!-- Employment Details -->
                    <h5 class="fw-bold text-success mb-3">
                        Employment Details
                    </h5>

                    <div class="row">

                        <!-- Employment Status -->
                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Employment Status
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="employment_status"
                                class="form-select"
                                required>

                                <option value="">Select Status</option>
                                <option value="Probationary">Probationary</option>
                                <option value="Regular">Regular</option>
                                <option value="Contractual">Contractual</option>

                            </select>

                        </div>

                        <!-- Employment Type -->
                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Employment Type
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="employment_type"
                                class="form-select"
                                required>

                                <option value="">Select Type</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>

                            </select>

                        </div>

                        <!-- Hire Date -->
                        <div class="col-md-4 mb-4">

                            <label class="form-label fw-semibold mb-2">
                                Hire Date
                                <span class="text-danger">*</span>
                            </label>

                            <div class="position-relative">

                                <i class="fas fa-calendar-check position-absolute top-50 start-0 translate-middle-y ms-3 text-success"></i>

                                <input
                                    type="date"
                                    name="hire_date"
                                    class="form-control ps-5 rounded-3 shadow-sm border-2"
                                    value="<?= date('Y-m-d'); ?>"
                                    max="<?= date('Y-m-d'); ?>"
                                    required>

                            </div>
                            <small class="text-muted">
                                Employee's official first day of work.
                            </small>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label for="regular_date" class="form-label fw-semibold">
                                Regularization Date
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>

                                <input
                                    type="date"
                                    id="regular_date"
                                    name="regular_date"
                                    class="form-control">

                            </div>

                            <small class="text-muted">
                                Leave blank if the employee has not yet been regularized.
                            </small>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label for="unit_load" class="form-label">
                                Teaching Unit Load
                            </label>

                            <input
                                type="number"
                                id="unit_load"
                                name="unit_load"
                                class="form-control"
                                min="0"
                                max="30"
                                step="0.5"
                                placeholder="e.g., 18">

                            <div class="form-text">
                                Applicable to teaching personnel only (e.g., Instructor, Lecturer, Professor).
                            </div>

                        </div>


                    </div>


                    <hr>


                    <!-- Notes -->

                    <h5 class="fw-bold text-success mb-3">
                        Additional Information
                    </h5>


                    <div class="mb-4">

                        <label class="form-label">
                            Faculty Notes / HR Remarks
                        </label>

                        <textarea
                            name="faculty_notes"
                            rows="4"
                            class="form-control"
                            placeholder="Additional HR notes..."></textarea>

                    </div>



                    <div class="d-flex justify-content-end">

                        <a
                            href="index.php?url=admin-manage-user"
                            class="btn btn-secondary me-2">

                            <i class="fas fa-arrow-left me-1"></i>
                            Back

                        </a>


                        <button
                            type="submit"
                            class="btn btn-success">

                            <i class="fas fa-save me-1"></i>
                            Save Employment Data

                        </button>

                    </div>


                </form>
            </div>
        </div>
    </div>
</div>