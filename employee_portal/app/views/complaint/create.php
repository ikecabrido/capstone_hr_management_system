<div class="modal fade" id="createComplaintModal" tabindex="-1" aria-labelledby="createComplaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="index.php?url=employee-complaints-store" method="POST">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createComplaintModalLabel">
                        <i class="fas fa-file-alt me-2"></i>
                        Submit Employee Complaint
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Notice -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="fas fa-info-circle me-2 mt-1"></i>

                        <div>
                            Please provide the details of your complaint below.
                            Your submission will be forwarded to the Legal and Compliance
                            Office for appropriate review and action.
                        </div>
                    </div>


                    <!-- ============================== -->
                    <!-- Complaint Information -->
                    <!-- ============================== -->

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Complaint Information
                    </h6>

                    <div class="row g-3 mb-4">

                        <!-- Complaint Category -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Complaint Category
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="type"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select complaint category
                                </option>

                                <option value="harassment">
                                    Harassment
                                </option>

                                <option value="discrimination">
                                    Discrimination
                                </option>

                                <option value="workplace_safety">
                                    Workplace Safety
                                </option>

                                <option value="policy_violation">
                                    Policy Violation
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- Filing As -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                I am filing this complaint as
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="reporter_role"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select
                                </option>

                                <option value="victim">
                                    The person directly affected
                                </option>

                                <option value="witness">
                                    A witness to the concern
                                </option>

                                <option value="reporter">
                                    Reporting on behalf of someone
                                </option>

                            </select>

                        </div>


                        <!-- Complaint Title -->
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Complaint Title
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                maxlength="255"
                                placeholder="Provide a short title for your complaint"
                                required>

                            <small class="text-muted">
                                Example: Inappropriate Behavior from Supervisor
                            </small>

                        </div>


                        <!-- Nature of Complaint -->
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Nature of Complaint
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="incident_type"
                                class="form-control"
                                maxlength="100"
                                placeholder="Briefly describe the nature of your concern"
                                required>

                            <small class="text-muted">
                                Example: Verbal harassment, unfair treatment, workplace bullying
                            </small>

                        </div>

                    </div>


                    <hr class="my-4">


                    <!-- ============================== -->
                    <!-- Person Involved -->
                    <!-- ============================== -->

                    <h6 class="fw-bold text-primary mb-1">
                        <i class="fas fa-user me-2"></i>
                        Person Involved
                    </h6>

                    <p class="text-muted small mb-3">
                        If your complaint involves another employee, you may identify them below.
                    </p>


                    <div class="row g-3 mb-4">

                        <!-- Person Being Complained About -->
                        <div class="col-md-7">

                            <label class="form-label fw-semibold">
                                Person Being Complained About
                            </label>

                            <select
                                name="respondent_id"
                                class="form-select">

                                <option value="">
                                    Select employee (optional)
                                </option>

                                <!-- Example employee loop -->

                                <?php if (!empty($employees)): ?>

                                    <?php foreach ($employees as $emp): ?>

                                        <option value="<?= htmlspecialchars($emp['employee_id']) ?>">

                                            <?= htmlspecialchars(
                                                ($emp['first_name'] ?? '') . ' ' .
                                                    ($emp['last_name'] ?? '')
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </select>

                        </div>


                        <!-- Relationship -->
                        <div class="col-md-5">

                            <label class="form-label fw-semibold">
                                Relationship to You
                            </label>

                            <select
                                name="respondent_relationship"
                                class="form-select">

                                <option value="">
                                    Select relationship
                                </option>

                                <option value="co_worker">
                                    Co-worker
                                </option>

                                <option value="supervisor">
                                    Supervisor
                                </option>

                                <option value="subordinate">
                                    Subordinate
                                </option>

                                <option value="external">
                                    External Person
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>

                    </div>


                    <hr class="my-4">


                    <!-- ============================== -->
                    <!-- When and Where -->
                    <!-- ============================== -->

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>
                        When and Where Did It Happen?
                    </h6>


                    <div class="row g-3 mb-4">

                        <!-- Date -->
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">
                                Date of Occurrence
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="incident_date"
                                class="form-control"
                                max="<?= date('Y-m-d') ?>"
                                required>

                        </div>


                        <!-- Time -->
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">
                                Approximate Time
                            </label>

                            <input
                                type="time"
                                name="incident_time"
                                class="form-control">

                        </div>


                        <!-- Location -->
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">
                                Location
                            </label>

                            <input
                                type="text"
                                name="location"
                                class="form-control"
                                maxlength="255"
                                placeholder="Where did it happen?">

                        </div>

                    </div>


                    <hr class="my-4">


                    <!-- ============================== -->
                    <!-- Complaint Details -->
                    <!-- ============================== -->

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-align-left me-2"></i>
                        Complaint Details
                    </h6>


                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Describe Your Complaint
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="6"
                            placeholder="Please explain what happened. Include important details such as what occurred, who was involved, what was said or done, and any other information that may help in reviewing your complaint."
                            required></textarea>

                        <small class="text-muted">
                            Please provide clear and accurate information.
                        </small>

                    </div>


                    <!-- Confirmation -->
                    <div class="form-check mt-4">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="complaintConfirmation"
                            required>

                        <label
                            class="form-check-label small"
                            for="complaintConfirmation">

                            I confirm that the information provided in this complaint
                            is true and accurate to the best of my knowledge.

                        </label>

                    </div>

                </div>


                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        <i class="fas fa-times me-1"></i>
                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane me-1"></i>
                        Submit Complaint

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>