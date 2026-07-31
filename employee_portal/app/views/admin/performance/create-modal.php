<style>
    .form-check-input[type="radio"] {
        appearance: radio;
        -webkit-appearance: radio;
        border-radius: 50%;
        width: 1rem;
        height: 1rem;
    }
</style>
<div class="modal fade" id="feedbackModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            <form method="POST" action="performance-feedback-create">

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title">
                        <i class="fas fa-comments mr-2"></i>
                        Submit 360° Feedback
                    </h5>

                    <button type="button"
                        class="close text-white"
                        data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <?php require __DIR__ . '/../../partials/notif.php'; ?>
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Employee
                        </label>

                        <select
                            name="employee_id"
                            class="form-control"
                            required>

                            <option value="">
                                -- Select Employee --
                            </option>

                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['employee_id'] ?>">
                                    <?= htmlspecialchars(trim(
                                        $employee['first_name'] . ' ' .
                                            ($employee['middle_name'] ? $employee['middle_name'] . ' ' : '') .
                                            $employee['last_name'] .
                                            ($employee['suffix'] ? ' ' . $employee['suffix'] : '')
                                    )) ?>
                                    (<?= htmlspecialchars($employee['employee_code']) ?>)
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Evaluator</label>
                        <select name="evaluator_type" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            <option value="Communication">Communication</option>
                            <option value="Teamwork">Teamwork</option>
                            <option value="Leadership">Leadership</option>
                            <option value="Problem Solving">Problem Solving</option>
                            <option value="Work Quality">Work Quality</option>
                            <option value="Productivity">Productivity</option>
                            <option value="Professionalism">Professionalism</option>
                            <option value="Initiative">Initiative</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Rating</label>

                        <select class="form-control" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                            <option value="3">⭐⭐⭐ (3 - Good)</option>
                            <option value="2">⭐⭐ (2 - Fair)</option>
                            <option value="1">⭐ (1 - Poor)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Category</label>

                        <select class="form-control"
                            name="category"
                            required>

                            <option value="">-- Select Category --</option>
                            <option value="Communication">Communication</option>
                            <option value="Teamwork">Teamwork</option>
                            <option value="Leadership">Leadership</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Feedback Comments</label>

                        <textarea class="form-control"
                            name="comments"
                            rows="4"
                            placeholder="Write your feedback..."
                            required></textarea>
                    </div>

                    <div class="form-group">

                        <label class="d-block font-weight-bold">
                            Submit Anonymously?
                        </label>

                        <small class="text-muted d-block mb-2">
                            If "Yes" is selected, the employee will not see who submitted this feedback.
                        </small>

                        <div class="form-check form-check-inline">

                            <input class="form-check-input"
                                type="radio"
                                name="is_anonymous"
                                value="1"
                                checked>

                            <label class="form-check-label">
                                Yes
                            </label>

                        </div>

                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="is_anonymous"
                                value="0">

                            <label class="form-check-label">
                                No
                            </label>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-paper-plane"></i>
                        Submit Feedback

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>