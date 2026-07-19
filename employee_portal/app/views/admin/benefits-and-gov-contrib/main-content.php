<div class="w-full ml-2 mt-4">
    <div class="content-wrapper text-3xl">
        <div class="pt-10 pl-10">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold text-primary mb-1 display-5">
                        <i class="fas fa-hand-holding-heart me-2"></i>
                        Benefits & Government Contributions
                    </h1>

                    <p class="text-muted mb-0 fs-5">
                        Upload and manage employee benefits and government contribution records.
                    </p>

                </div>

                <button
                    class="btn btn-primary btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#createBenefitModal">

                    <i class="fas fa-upload me-2"></i>
                    Upload Record

                </button>

            </div>

            <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <div class="row">

                        <div class="col-md-4">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search records...">
                        </div>

                    </div>

                </div>

                <div class="card-body p-0">

                    <?php require __DIR__ . '/table.php'; ?>

                </div>
                <?php require __DIR__ . '/view-modal.php'; ?>
            </div>

            <?php require __DIR__ . '/create-modal.php'; ?>

        </div>
    </div>
</div>