<div class="modal fade"
    id="deleteTrainingModal<?= $request['ld_request_id']; ?>"
    tabindex="-1">


    <div class="modal-dialog modal-dialog-centered">


        <div class="modal-content">


            <form action="index.php?url=admin-training-request-delete" method="POST">


                <div class="modal-header bg-danger text-white">


                    <h5 class="modal-title">

                        <i class="fas fa-trash mr-2"></i>

                        Delete Training Request

                    </h5>


                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">

                    </button>


                </div>



                <div class="modal-body text-center">


                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>


                    <h5>
                        Are you sure you want to delete this request?
                    </h5>


                    <p class="text-muted mb-0">

                        Employee:
                        <strong>
                            <?= htmlspecialchars($request['full_name']); ?>
                        </strong>

                    </p>


                    <p class="text-muted">

                        Program:
                        <strong>
                            <?= htmlspecialchars($request['requested_program']); ?>
                        </strong>

                    </p>



                    <input
                        type="hidden"
                        name="ld_request_id"
                        value="<?= $request['ld_request_id']; ?>">


                </div>



                <div class="modal-footer justify-content-center">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn btn-danger">

                        <i class="fas fa-trash mr-1"></i>

                        Delete

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>