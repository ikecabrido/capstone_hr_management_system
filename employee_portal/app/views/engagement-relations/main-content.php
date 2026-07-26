<div class="w-full ml-32">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full">

            <?php require __DIR__ . '/../partials/notif.php' ?>

            <div class="card-header bg-gradient bg-primary text-white rounded-top-4 d-flex align-items-center">
                <i class="bi bi-exclamation-circle me-2 fs-4"></i>
                <h5 class="mb-0 text-2xl fw-bold">Submit Employee Grievance</h5>
            </div>

            <div class="card-body p-4">

                <form enctype="multipart/form-data" method="POST" action="employee-grievance-create">

                    <input type="hidden" value="<?= $employee_id ?>" name="employee_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject</label>
                        <input type="text" class="form-control rounded-3 shadow-sm"
                            name="subject"
                            placeholder="e.g. Salary discrepancy, workplace issue..."
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control rounded-3 shadow-sm"
                            name="description"
                            rows="4"
                            placeholder="Provide full details of your concern..."
                            required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select rounded-3 shadow-sm" name="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="Workplace Conflict">Workplace Conflict</option>
                                <option value="Harassment / Bullying">Harassment / Bullying</option>
                                <option value="Payroll Concern">Payroll Concern</option>
                                <option value="Work Environment">Work Environment</option>
                                <option value="Management Issue">Management Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold d-block">Submit Anonymously?</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="anonymous" value="1" checked>
                                <label class="form-check-label">Yes</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="anonymous" value="0">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Attachment (optional)</label>

                        <div class="border rounded-3 p-3 text-center bg-light shadow-sm">
                            <input type="file" class="form-control border-0 bg-transparent"
                                name="attachment_path">
                            <small class="text-muted">
                                Upload supporting files (images, PDF, etc.)
                            </small>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit"
                            class="btn btn-primary w-100 rounded-3 shadow-sm fw-semibold">
                            <i class="bi bi-send me-1"></i> Submit Grievance
                        </button>

                        <a href="index.php?url=dashboard"
                            class="btn btn-outline-secondary w-100 rounded-3 shadow-sm">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>