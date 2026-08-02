<div class="modal fade"
    id="createResignationModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form
                action="index.php?url=resignation-request-create"
                method="POST"
                enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Submit Resignation Request
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                Employee <span class="text-danger">*</span>
                            </label>

                            <select
                                name="employee_id"
                                class="form-select"
                                required>

                                <option value="">-- Select Employee --</option>

                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['employee_id']; ?>">
                                        <?= htmlspecialchars(
                                            $employee['employee_code'] . ' - ' .
                                                $employee['first_name'] . ' ' .
                                                $employee['last_name']
                                        ); ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Resignation Type
                            </label>

                            <select
                                name="resignation_type"
                                class="form-select"
                                required>

                                <option value="">Select Type</option>
                                <option value="With Notice">With Notice</option>
                                <option value="Immediate">Immediate</option>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Intended Last Working Day
                            </label>

                            <input
                                type="date"
                                name="intended_last_working_day"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Reason for Resignation
                            </label>

                            <textarea
                                name="resignation_reason"
                                rows="5"
                                class="form-control"
                                placeholder="State your reason for resignation..."
                                required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Employee Remarks (Optional)
                            </label>

                            <textarea
                                name="employee_remarks"
                                rows="2"
                                class="form-control"
                                placeholder="State your remarks as an employee..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Upload Resignation Letter
                            </label>

                            <input
                                type="file"
                                name="attachment"
                                class="form-control"
                                accept=".pdf,.doc,.docx" required>

                            <small class="text-muted">
                                Accepted formats: PDF, DOC, DOCX (Max 5MB)
                            </small>
                        </div>

                    </div>

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
                        class="btn btn-primary">
                        Submit Request
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>