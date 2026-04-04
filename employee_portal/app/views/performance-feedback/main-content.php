<div class="w-full ml-32">
    <div class="content-wrapper w-full">
        <div class="card shadow-lg border-0 rounded-4 w-full">


            <div class="card-header bg-gradient bg-primary text-white rounded-top-4 d-flex align-items-center">
                <i class="bi bi-exclamation-circle me-2 fs-4"></i>
                <h5 class="mb-0 text-2xl fw-bold">Submit Feedback</h5>
            </div>

            <div class="card-body p-4">
            <?php require __DIR__ . '/../partials/notif.php' ?>

                <form method="POST" action="performance-feedback-create">

                    <input type="hidden" value="<?= $employee_id ?>" name="employee_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Evaluator</label>
                        <select name="evaluator_type" class="form-select rounded-3 shadow-sm" required>
                            <option value="">-- Select Evaluator --</option>
                            <option value="Manager">Manager</option>
                            <option value="Peer">Peer</option>
                            <option value="Subordinate">Subordinate</option>
                            <option value="Self">Self</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rating</label>
                        <input type="number" class="form-control rounded-3 shadow-sm"
                            name="rating"
                            min="1"
                            max="5"
                            placeholder="Enter rating (1-5)"
                            value="5"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select class="form-select rounded-3 shadow-sm" name="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="Communication">Communication</option>
                            <option value="Teamwork">Teamwork</option>
                            <option value="Leadership">Leadership</option>
                        </select>
                    </div>
            </div>

            <div class="row">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Comments</label>
                    <textarea class="form-control rounded-3 shadow-sm"
                        name="comments"
                        rows="4"
                        placeholder="Provide full details of your concern..."
                        required></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold d-block">Submit Anonymously?</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_anonymous" value="1" checked>
                        <label class="form-check-label">Yes</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_anonymous" value="0">
                        <label class="form-check-label">No</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit"
                    class="btn btn-primary w-100 rounded-3 shadow-sm fw-semibold">
                    <i class="bi bi-send me-1"></i> Submit Feedback
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