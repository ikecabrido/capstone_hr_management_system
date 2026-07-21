<div class="modal fade" id="viewTrainingModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">

                <h4 class="modal-title fw-bold">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Training Details
                </h4>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <!-- Body -->
            <div class="modal-body p-4">

                <!-- Training Title -->
                <div class="text-center mb-4">

                    <i class="fas fa-book-open fa-3x text-primary mb-3"></i>

                    <h3 id="view_title" class="fw-bold mb-1"></h3>

                    <span id="view_status"></span>

                </div>

                <div class="row g-3">

                    <div class="col-md-6">

                        <div class="border rounded-3 p-3 h-100">

                            <small class="text-muted d-block">
                                <i class="fas fa-user-tie me-1"></i>
                                Trainer
                            </small>

                            <h6 id="view_trainer" class="fw-bold mb-0"></h6>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded-3 p-3 h-100">

                            <small class="text-muted d-block">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Schedule
                            </small>

                            <div>
                                <strong>Start:</strong>
                                <span id="view_start"></span>
                            </div>

                            <div>
                                <strong>End:</strong>
                                <span id="view_end"></span>
                            </div>

                        </div>

                    </div>

                    <div class="col-12">

                        <div class="border rounded-3 p-3">

                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-align-left me-1"></i>
                                Description
                            </small>

                            <p id="view_description" class="mb-0"></p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">

                <button
                    class="btn btn-secondary px-4"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Close
                </button>

            </div>

        </div>

    </div>

</div>