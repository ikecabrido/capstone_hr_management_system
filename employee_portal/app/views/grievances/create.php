<!-- Create Grievance Modal -->
<div class="modal fade"
    id="createGrievanceModal"
    tabindex="-1"
    aria-labelledby="createGrievanceModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            <form
                enctype="multipart/form-data"
                method="POST"
                action="index.php?url=employee-grievance-store">

                <input
                    type="hidden"
                    name="employee_id"
                    value="<?= $employeeGrievance['employee_id'] ?>">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">

                    <div>

                        <h5 class="modal-title fw-bold" id="createGrievanceModalLabel">

                            <i class="fas fa-balance-scale me-2"></i>

                            Submit Employee Grievance

                        </h5>

                        <small class="opacity-75">

                            Your grievance will be reviewed by the Employee Engagement & Relations Office.

                        </small>

                    </div>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- Body -->
                <div class="modal-body">

                    <div class="alert alert-info border-0">

                        <i class="fas fa-shield-alt me-2"></i>

                        Please provide complete and truthful information. All grievances are handled professionally and confidentially.

                    </div>

                    <!-- Subject -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Subject <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="subject"
                            placeholder="Brief summary of your grievance"
                            required>

                    </div>

                    <!-- Description -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold">

                            Description <span class="text-danger">*</span>

                        </label>

                        <textarea
                            class="form-control"
                            name="description"
                            rows="5"
                            placeholder="Explain your grievance in detail..."
                            required></textarea>

                    </div>

                    <div class="row">

                        <!-- Category -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">

                                Category <span class="text-danger">*</span>

                            </label>

                            <select
                                class="form-select"
                                name="category"
                                required>

                                <option value="">Select Category</option>

                                <option value="Workplace Conflict">
                                    Workplace Conflict
                                </option>

                                <option value="Workplace Harassment">
                                    Workplace Harassment
                                </option>

                                <option value="Payroll Issues">
                                    Payroll Issues
                                </option>

                                <option value="Benefits & Compensation">
                                    Benefits & Compensation
                                </option>

                                <option value="Management Concern">
                                    Management Concern
                                </option>

                                <option value="Work Environment">
                                    Work Environment
                                </option>

                                <option value="Policy Violation">
                                    Policy Violation
                                </option>

                                <option value="Discrimination">
                                    Discrimination
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                        </div>

                        <!-- Priority -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">

                                Priority <span class="text-danger">*</span>

                            </label>

                            <select
                                class="form-select"
                                name="priority"
                                required>

                                <option value="medium" selected>
                                    Medium
                                </option>

                                <option value="low">
                                    Low
                                </option>

                                <option value="high">
                                    High
                                </option>

                                <option value="urgent">
                                    Urgent
                                </option>

                                <option value="critical">
                                    Critical
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="row">

                        <!-- Anonymous -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">

                                Submit Anonymously

                            </label>

                            <div class="border rounded-3 p-3">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="anonymous"
                                        value="1">

                                    <label class="form-check-label">

                                        Yes

                                    </label>

                                </div>

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="anonymous"
                                        value="0"
                                        checked>

                                    <label class="form-check-label">

                                        No

                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- Confidential -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">

                                Confidential Request

                            </label>

                            <div class="border rounded-3 p-3">

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="confidential"
                                        value="1"
                                        checked>

                                    <label class="form-check-label">

                                        Yes

                                    </label>

                                </div>

                                <div class="form-check">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="confidential"
                                        value="0">

                                    <label class="form-check-label">

                                        No

                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Attachment -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Supporting Attachment

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="attachment_path"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

                        <small class="text-muted">

                            Optional. Upload documents, screenshots, or images that support your grievance.

                        </small>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                        <i class="fas fa-times me-1"></i>

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane me-1"></i>

                        Submit Grievance

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>