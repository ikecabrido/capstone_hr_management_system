<div class="modal fade" id="createLeaveRequestModal" tabindex="-1" aria-labelledby="createLeaveRequestLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-3xl" id="createLeaveRequestLabel">
                    <i class="fa-solid fa-plus me-2"></i>
                    Submit Leave Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="index.php?url=leave-requests-create" method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type of Leave</label>
                        <select name="type_of_leave" class="form-control" required>
                            <option value=""><-- Select Leave Type --></option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Vacation Leave">Vacation Leave</option>
                            <option value="Emergency Leave">Emergency Leave</option>
                            <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                            <option value="Bereavement Leave">Bereavement Leave</option>
                            <option value="Study Leave">Study Leave</option>
                            <option value="Medical Leave">Medical Leave</option>
                            <option value="Compensatory Leave">Compensatory Leave</option>
                            <option value="Unpaid Leave">Unpaid Leave</option>
                            <option value="Personal Leave">Personal Leave</option>
                            <option value="Public Holiday Leave">Public Holiday Leave</option>
                            <option value="Jury Duty Leave">Jury Duty Leave</option>
                            <option value="Sabbatical Leave">Sabbatical Leave</option>
                            <option value="Special Leave">Special Leave</option>
                        </select>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date"
                                id="start_date"
                                name="start_date"
                                class="form-control"
                                value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>"
                                min="<?php echo date('Y-m-d', strtotime('+1 month')); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date"
                                id="end_date"
                                name="end_date"
                                class="form-control"
                                value="<?php echo date('Y-m-d', strtotime('+1 month +1 day')); ?>"
                                min="<?php echo date('Y-m-d', strtotime('+1 month +1 day')); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Total Leave Days</label>
                            <input type="text"
                                id="leave_days"
                                class="form-control"
                                readonly
                                placeholder="Auto calculated">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Details / Reason</label>
                        <textarea name="details"
                            class="form-control"
                            rows="3"
                            placeholder="Provide a brief reason for leave..."
                            required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Supporting Document</label>
                        <input type="file"
                            name="supporting_document"
                            class="form-control"
                            accept=".pdf,.doc,.docx,.jpg,.png"
                            required>
                        <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit"
                        class="btn btn-primary rounded-pill px-4 fw-semibold">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>