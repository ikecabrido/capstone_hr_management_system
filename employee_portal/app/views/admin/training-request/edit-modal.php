<div class="modal fade"
    id="editTrainingModal<?= $request['ld_request_id']; ?>"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">


            <form action="index.php?url=admin-training-request-update" method="POST">


                <div class="modal-header bg-warning">

                    <h5 class="modal-title">

                        <i class="fas fa-edit mr-2"></i>

                        Edit Training Request

                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">

                    </button>

                </div>



                <div class="modal-body">


                    <input
                        type="hidden"
                        name="ld_request_id"
                        value="<?= $request['ld_request_id']; ?>">



                    <div class="form-group">

                        <label class="font-weight-bold">
                            Employee
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($request['full_name']); ?>"
                            readonly>

                    </div>



                    <div class="row">


                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    Requested Program
                                </label>

                                <input
                                    type="text"
                                    name="requested_program"
                                    class="form-control"
                                    value="<?= htmlspecialchars($request['requested_program']); ?>"
                                    required>

                            </div>

                        </div>



                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="font-weight-bold">
                                    Requested Course
                                </label>

                                <input
                                    type="text"
                                    name="requested_course"
                                    class="form-control"
                                    value="<?= htmlspecialchars($request['requested_course']); ?>"
                                    required>

                            </div>

                        </div>


                    </div>




                    <div class="form-group">

                        <label class="font-weight-bold">
                            Goal ID
                        </label>

                        <input
                            type="number"
                            name="goal_id"
                            class="form-control"
                            value="<?= htmlspecialchars($request['goal_id'] ?? ''); ?>">

                    </div>




                    <div class="form-group">

                        <label class="font-weight-bold">
                            KPI Name
                        </label>

                        <input
                            type="text"
                            name="kpi_name"
                            class="form-control"
                            value="<?= htmlspecialchars($request['kpi_name'] ?? ''); ?>">

                    </div>




                    <div class="form-group">

                        <label class="font-weight-bold">
                            Request Reason
                        </label>

                        <textarea
                            name="request_reason"
                            class="form-control"
                            rows="5"
                            required><?= htmlspecialchars($request['request_reason']); ?></textarea>

                    </div>



                    <div class="form-group">

                        <label class="font-weight-bold">
                            Status
                        </label>


                        <select
                            name="request_status"
                            class="form-control">


                            <option value="Pending"
                                <?= $request['request_status'] == 'Pending' ? 'selected' : ''; ?>>
                                Pending
                            </option>


                            <option value="Received"
                                <?= $request['request_status'] == 'Received' ? 'selected' : ''; ?>>
                                Received
                            </option>


                            <option value="Approved"
                                <?= $request['request_status'] == 'Approved' ? 'selected' : ''; ?>>
                                Approved
                            </option>


                            <option value="Rejected"
                                <?= $request['request_status'] == 'Rejected' ? 'selected' : ''; ?>>
                                Rejected
                            </option>


                        </select>

                    </div>


                </div>




                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>



                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save mr-1"></i>

                        Save Changes

                    </button>


                </div>


            </form>


        </div>

    </div>

</div>