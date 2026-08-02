<div class="modal fade" id="createDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form
                id="employee-documents-upload-form"
                method="POST"
                enctype="multipart/form-data"
                action="employee-documents-create">

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-upload me-2"></i>
                        Submit Employee Document
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Title
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="title"
                                list="documentTitles"
                                placeholder="Select or enter a document title"
                                required>

                            <datalist id="documentTitles">
                                <option value="Certificate of Employment">
                                <option value="Employment Contract">
                                <option value="Payslip">
                                <option value="Payroll Summary">
                                <option value="Leave Application">
                                <option value="Leave Approval">
                                <option value="Medical Certificate">
                                <option value="Performance Evaluation">
                                <option value="Training Certificate">
                                <option value="Training Attendance">
                                <option value="Memorandum">
                                <option value="Notice of Promotion">
                                <option value="Notice of Salary Adjustment">
                                <option value="Certificate of Compensation">
                                <option value="Certificate of Contribution">
                                <option value="Employment Verification Letter">
                                <option value="Clearance Form">
                                <option value="Employee ID Request">
                                <option value="Government Contribution Record">
                                <option value="Tax Certificate (BIR Form 2316)">
                            </datalist>

                            <small class="text-muted">
                                Select a common document title or type your own.
                            </small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                name="description"
                                rows="3"
                                placeholder="Enter description..."
                                required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Department
                            </label>

                            <select
                                class="form-select"
                                name="department"
                                required>

                                <option value="">Select Department</option>

                                <?php foreach ($departments as $dept): ?>

                                    <option value="<?= $dept['id']; ?>">
                                        <?= htmlspecialchars($dept['department_name']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Submitted By
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($employeeInfo['first_name']); ?> <?= htmlspecialchars($employeeInfo['last_name']); ?> "
                                readonly>

                            <input
                                type="hidden"
                                name="submit_by"
                                value="<?= $employeeInfo['employee_id']; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Attachment
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="attachment"
                                required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane me-1"></i>
                        Submit

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>