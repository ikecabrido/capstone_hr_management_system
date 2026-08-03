<!-- View Employee Modal -->
<div
    class="modal fade"
    id="viewEmployeeModal<?= $employee['employee_id']; ?>"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user me-2"></i>
                    Employee HR Profile
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <!-- Employee Information -->
                <h5 class="fw-bold text-primary mb-3">
                    Employee Information
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee Name</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars(trim($employee['first_name'] . ' ' . $employee['last_name'])) ?>"
                            readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee Code</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['employee_code'] ?? '-') ?>"
                            readonly>
                    </div>

                </div>

                <hr>

                <!-- Position Information -->
                <h5 class="fw-bold text-primary mb-3">
                    Position Information
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Department</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['department'] ?? '-') ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Position</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['position'] ?? '-') ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Position Title</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['position_title_enum'] ?? '-') ?>"
                            readonly>
                    </div>

                </div>

                <hr>

                <!-- Employment Details -->
                <h5 class="fw-bold text-primary mb-3">
                    Employment Details
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Employment Status</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['employment_status'] ?? '-') ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Employment Type</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($employee['employment_type'] ?? '-') ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hire Date</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= !empty($employee['hire_date']) ? date('F d, Y', strtotime($employee['hire_date'])) : '-' ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Regularization Date</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= !empty($employee['regular_date']) ? date('F d, Y', strtotime($employee['regular_date'])) : '-' ?>"
                            readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Teaching Unit Load</label>
                        <input
                            type="text"
                            class="form-control"
                            value="<?= !empty($employee['unit_load']) ? $employee['unit_load'] . ' Units' : '-' ?>"
                            readonly>
                    </div>

                </div>

                <hr>

                <!-- HR Remarks -->
                <h5 class="fw-bold text-primary mb-3">
                    HR Remarks
                </h5>

                <div class="mb-3">

                    <textarea
                        class="form-control"
                        rows="4"
                        readonly><?= htmlspecialchars($employee['faculty_notes'] ?? 'No remarks available.') ?></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    <i class="fas fa-times me-1"></i>
                    Close

                </button>

            </div>

        </div>

    </div>

</div>