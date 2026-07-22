<style>
    .modal-content {
        border: none;
        border-radius: 18px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: 1px solid #edf2f7;
    }

    .form-control {
        border-radius: 10px;
        min-height: 46px;
    }

    textarea.form-control {
        min-height: 140px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        border-color: #0d6efd;
    }

    label {
        margin-bottom: .45rem;
    }

    .alert-light {
        background: #f8f9fc;
        border: 1px solid #e9ecef;
    }
</style>
<div class="modal fade" id="trainingRequestModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            <form action="index.php?url=admin-create-training-request" method="POST">

                <div class="modal-header bg-primary text-white py-3">
                    <div>
                        <h4 class="modal-title mb-1">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Training Request
                        </h4>
                        <small class="text-white-50">
                            Submit a request for a professional development program.
                        </small>
                    </div>

                    <button
                        type="button"
                        class="close text-white"
                        data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-light border mb-4">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Complete the information below. HR will review your request and notify you once it has been processed.
                    </div>

                        <div class="form-group">

                            <label>
                                Request By (Employee)
                                <span class="text-danger">*</span>
                            </label>

                            <select name="employee_id" class="form-control" required>

                                <option value="">-- Select Employee --</option>

                                <?php foreach ($employees as $employee): ?>

                                    <option value="<?= $employee['id']; ?>">
                                        <?= htmlspecialchars($employee['employee_no']); ?>
                                        -
                                        <?= htmlspecialchars($employee['full_name']); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    <i class="fas fa-book text-primary mr-1"></i>
                                    Training Program
                                </label>

                                <input
                                    type="text"
                                    name="requested_program"
                                    class="form-control"
                                    placeholder="Leadership Development"
                                    required>

                                <small class="text-muted">
                                    Name of the program you wish to attend.
                                </small>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    <i class="fas fa-chalkboard-teacher text-primary mr-1"></i>
                                    Course
                                </label>

                                <input
                                    type="text"
                                    name="requested_course"
                                    class="form-control"
                                    placeholder="Strategic Leadership"
                                    required>

                                <small class="text-muted">
                                    Specific course or topic.
                                </small>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    <i class="fas fa-bullseye text-success mr-1"></i>
                                    Goal ID
                                </label>

                                <input
                                    type="number"
                                    name="goal_id"
                                    class="form-control"
                                    placeholder="Optional">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    <i class="fas fa-chart-line text-success mr-1"></i>
                                    KPI Name
                                </label>

                                <input
                                    type="text"
                                    name="kpi_name"
                                    class="form-control"
                                    placeholder="Optional">

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label class="font-weight-bold">
                            <i class="fas fa-comment-dots text-warning mr-1"></i>
                            Justification
                        </label>

                        <textarea
                            class="form-control"
                            rows="6"
                            name="request_reason"
                            placeholder="Explain why you need this training, how it aligns with your work responsibilities, and how it will contribute to your professional development."
                            required></textarea>

                        <small class="text-muted">
                            Provide a clear justification to help HR evaluate your request.
                        </small>

                    </div>

                </div>

                <div class="modal-footer justify-content-between">

                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>
                        Processing may take 2–5 working days.
                    </small>

                    <div>

                        <button
                            type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary px-4">

                            <i class="fas fa-paper-plane mr-2"></i>

                            Submit Request

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>