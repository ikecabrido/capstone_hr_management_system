<div class="modal fade"
    id="viewTrainingModal<?= $request['ld_request_id']; ?>"
    tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-info text-white">

                <h5 class="modal-title">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Training Request Details
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            Employee
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['first_name']); ?> <?= htmlspecialchars($request['last_name']); ?>"
                            readonly>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            Status
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['request_status']); ?>"
                            readonly>

                    </div>

                </div>



                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            Training Program
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['requested_program']); ?>"
                            readonly>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            Course
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['requested_course']); ?>"
                            readonly>

                    </div>

                </div>



                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            Goal ID
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['goal_id'] ?? 'N/A'); ?>"
                            readonly>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold">
                            KPI Name
                        </label>

                        <input
                            class="form-control"
                            value="<?= htmlspecialchars($request['kpi_name'] ?? 'N/A'); ?>"
                            readonly>

                    </div>

                </div>



                <div class="mb-3">

                    <label class="font-weight-bold">
                        Justification
                    </label>

                    <textarea
                        class="form-control"
                        rows="5"
                        readonly><?= htmlspecialchars($request['request_reason']); ?></textarea>

                </div>


                <div class="mb-3">

                    <label class="font-weight-bold">
                        Submitted Date
                    </label>

                    <input
                        class="form-control"
                        value="<?= htmlspecialchars($request['created_at']); ?>"
                        readonly>

                </div>


            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>


        </div>

    </div>

</div>