<div class="modal fade" id="editBenefitModal" tabindex="-1" aria-labelledby="editBenefitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form
                action="index.php?url=benefits-and-gov-contrib-update"
                method="POST"
                enctype="multipart/form-data">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="editBenefitModalLabel">
                        <i class="fas fa-edit me-2"></i>
                        Edit Benefit Record
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <input
                        type="hidden"
                        name="benefit_id"
                        id="editBenefitId">

                    <div class="row g-3">

                        <!-- Employee -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Employee
                            </label>

                            <input
                                type="text"
                                class="form-control bg-light"
                                id="editEmployee"
                                readonly>
                        </div>

                        <!-- Record Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Record Type
                            </label>

                            <select
                                class="form-select"
                                name="record_type"
                                id="editRecordType"
                                required>

                                <option value="SSS">SSS</option>
                                <option value="PhilHealth">PhilHealth</option>
                                <option value="Pag-IBIG">Pag-IBIG</option>
                                <option value="Withholding Tax">Withholding Tax</option>
                                <option value="BIR Form 2316">BIR Form 2316</option>

                            </select>
                        </div>

                        <!-- Period -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Period
                            </label>

                            <input readonly
                                type="text"
                                class="form-control"
                                name="period"
                                id="editPeriod"
                                placeholder="e.g. June 2026"
                                required>
                        </div>

                        <!-- Replace File -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Current Attachment
                            </label>

                            <div id="currentFilePreview" class="border rounded p-2 text-center mb-2">
                                <!-- Preview will be inserted here -->
                            </div>

                            <label class="form-label fw-semibold">
                                Replace Attachment
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="benefit_file"
                                accept=".pdf,.jpg,.jpeg,.png">

                            <small class="text-muted">
                                Leave this blank to keep the current file.
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                class="form-control"
                                rows="4"
                                name="description"
                                id="editDescription"
                                placeholder="Enter additional remarks..."></textarea>
                        </div>

                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>