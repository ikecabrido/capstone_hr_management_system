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
        background-color: #f8f9fc;
    }

    textarea.form-control {
        min-height: 140px;
    }

    label {
        margin-bottom: .45rem;
    }

    .info-box {
        background: #f8f9fc;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
    }
</style>


<?php foreach ($trainingRequests as $request): ?>


    <div class="modal fade"
        id="viewTrainingModal<?= $request['ld_request_id']; ?>"
        tabindex="-1"
        aria-hidden="true">


        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">


            <div class="modal-content">


                <div class="modal-header bg-info text-white py-3">


                    <div>

                        <h4 class="modal-title mb-1">

                            <i class="fas fa-graduation-cap mr-2"></i>

                            Training Request Details

                        </h4>


                        <small class="text-white-50">

                            View your submitted training request information.

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


                        <i class="fas fa-info-circle text-info mr-2"></i>


                        This request is currently being reviewed by HR.


                    </div>




                    <div class="row">


                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-book text-info mr-1"></i>

                                    Training Program

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= htmlspecialchars($request['requested_program']); ?>">


                            </div>


                        </div>




                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-chalkboard-teacher text-info mr-1"></i>

                                    Course

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= htmlspecialchars($request['requested_course']); ?>">


                            </div>


                        </div>


                    </div>




                    <hr>




                    <div class="row">


                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-tasks text-success mr-1"></i>

                                    Request Status

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= htmlspecialchars($request['request_status']); ?>">


                            </div>


                        </div>




                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-calendar text-success mr-1"></i>

                                    Submitted Date

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= date('M d, Y', strtotime($request['created_at'])); ?>">


                            </div>


                        </div>


                    </div>





                    <div class="row">


                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-bullseye text-primary mr-1"></i>

                                    Goal ID

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= htmlspecialchars($request['goal_id'] ?? 'N/A'); ?>">


                            </div>


                        </div>




                        <div class="col-md-6">


                            <div class="form-group">


                                <label class="font-weight-bold">

                                    <i class="fas fa-chart-line text-primary mr-1"></i>

                                    KPI Name

                                </label>


                                <input
                                    class="form-control"
                                    readonly
                                    value="<?= htmlspecialchars($request['kpi_name'] ?? 'N/A'); ?>">


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
                            readonly><?= htmlspecialchars($request['request_reason']); ?></textarea>


                    </div>




                </div>





                <div class="modal-footer justify-content-between">


                    <small class="text-muted">

                        <i class="fas fa-clock mr-1"></i>

                        Submitted request details

                    </small>



                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                        Close

                    </button>


                </div>



            </div>


        </div>


    </div>


<?php endforeach; ?>