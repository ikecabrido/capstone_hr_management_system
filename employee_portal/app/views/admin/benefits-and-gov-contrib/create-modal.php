<div class="modal fade" id="createBenefitModal" tabindex="-1" aria-labelledby="createBenefitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="index.php?url=benefits-and-gov-contrib-store"
                method="POST"
                enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createBenefitModalLabel">
                        <i class="fas fa-hand-holding-heart me-2"></i>
                        Upload Benefits & Government Contribution Record
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Employee
                            </label>
                            <!-- Employee -->
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>

                                <?php foreach ($employeeList as $employee): ?>
                                    <option value="<?= $employee['id']; ?>">
                                        <?= htmlspecialchars($employee['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                        </div>
                        <!-- Record Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Record Type
                            </label>

                            <select
                                name="record_type"
                                class="form-select"
                                required>

                                <option value="">Select Record Type</option>

                                <option value="SSS">SSS</option>
                                <option value="PhilHealth">PhilHealth</option>
                                <option value="Pag-IBIG">Pag-IBIG</option>
                                <option value="Withholding Tax">Withholding Tax</option>
                                <option value="BIR Form 2316">BIR Form 2316</option>

                            </select>
                        </div>

                        <!-- Period -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Period
                            </label>

                            <input
                                type="month"
                                name="period"
                                class="form-control"
                                placeholder="e.g. June 2026"
                                required>
                        </div>

                        <!-- File -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Upload File
                            </label>

                            <input
                                type="file"
                                name="benefit_file"
                                class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required>

                            <small class="text-muted">
                                Accepted formats: PDF, JPG, JPEG, PNG
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="4"
                                class="form-control"
                                placeholder="Enter additional remarks (optional)"></textarea>
                        </div>

                    </div>

                </div>

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

                        <i class="fas fa-upload me-1"></i>
                        Upload Record

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>