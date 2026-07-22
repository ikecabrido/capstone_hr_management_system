<style>
    .badge {
        opacity: 1 !important;
    }

    .badge-warning {
        color: #212529 !important;
        background-color: #ffc107 !important;
    }

    .badge-success {
        color: #fff !important;
        background-color: #28a745 !important;
    }

    .badge-primary {
        color: #fff !important;
        background-color: #007bff !important;
    }

    .badge-danger {
        color: #fff !important;
        background-color: #dc3545 !important;
    }
</style>
<div class="w-full ml-32">

    <div class="content-wrapper px-4 py-3">


        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

            <div>

                <h1 class="text-3xl font-bold mb-1">
                    Training Request
                </h1>

                <p class="text-muted mb-0">
                    Request professional development programs and monitor your training application status.
                </p>

            </div>


            <button
                type="button"
                class="btn btn-primary shadow-sm mt-3 mt-md-0"
                data-bs-toggle="modal"
                data-bs-target="#trainingRequestModal">

                <i class="fas fa-plus mr-1"></i>
                New Request

            </button>


        </div>



        <?php require __DIR__ . '/../../views/partials/notif.php'; ?>



        <!-- Summary Cards -->
        <div class="row mb-4">


            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body d-flex align-items-center">

                        <div class="bg-primary text-white rounded-circle p-3 mr-3">

                            <i class="fas fa-paper-plane fa-lg"></i>

                        </div>


                        <div>

                            <h6 class="text-muted mb-1">
                                Total Requests
                            </h6>

                            <h3 class="mb-0">
                                <?= $totalRequests ?? 0; ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>



            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body d-flex align-items-center">

                        <div class="bg-warning text-white rounded-circle p-3 mr-3">

                            <i class="fas fa-clock fa-lg"></i>

                        </div>


                        <div>

                            <h6 class="text-muted mb-1">
                                Pending
                            </h6>

                            <h3 class="mb-0">
                                <?= $pendingRequests ?? 0; ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>



            <div class="col-md-4">

                <div class="card shadow-sm border-0">

                    <div class="card-body d-flex align-items-center">

                        <div class="bg-success text-white rounded-circle p-3 mr-3">

                            <i class="fas fa-check-circle fa-lg"></i>

                        </div>


                        <div>

                            <h6 class="text-muted mb-1">
                                Approved
                            </h6>

                            <h3 class="mb-0">
                                <?= $approvedRequests ?? 0; ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>


        </div>




        <!-- Request Table -->

        <div class="card shadow-sm border-0">


            <div class="card-header bg-white border-bottom">


                <h5 class="mb-0">

                    <i class="fas fa-graduation-cap text-primary mr-2"></i>

                    My Training Requests

                </h5>


            </div>



            <div class="card-body p-0">


                <div class="table-responsive">


                    <table class="table table-hover table-striped mb-0">


                        <thead class="thead-light">


                            <tr>

                                <th>#</th>

                                <th>Program</th>

                                <th>Course</th>

                                <th>Status</th>

                                <th>Requested Date</th>

                                <th width="80">
                                    Action
                                </th>

                            </tr>


                        </thead>



                        <tbody>


                            <?php if (!empty($trainingRequests)): ?>


                                <?php foreach ($trainingRequests as $request): ?>


                                    <?php
                                    switch ($request['request_status']) {

                                        case 'Approved':
                                            $badge = 'success';
                                            break;

                                        case 'Rejected':
                                            $badge = 'danger';
                                            break;

                                        case 'Received':
                                            $badge = 'primary';
                                            break;

                                        default:
                                            $badge = 'warning';
                                            break;
                                    }
                                    ?>



                                    <tr>


                                        <td>
                                            <?= $request['ld_request_id']; ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($request['requested_program']); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($request['requested_course']); ?>
                                        </td>



                                        <td>

                                            <span class="badge badge-pill badge-<?= $badge; ?>"
                                                style="font-size:14px; opacity:1 !important;">

                                                <?= htmlspecialchars($request['request_status']); ?>

                                            </span>

                                        </td>



                                        <td>

                                            <?= date(
                                                'M d, Y',
                                                strtotime($request['created_at'])
                                            ); ?>

                                        </td>



                                        <td>


                                            <button
                                                class="btn btn-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewTrainingModal<?= $request['ld_request_id']; ?>">


                                                <i class="fas fa-eye"></i>


                                            </button>


                                        </td>


                                    </tr>



                                    <?php require __DIR__ . '/view-modal.php'; ?>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <tr>

                                    <td colspan="6"
                                        class="text-center text-muted py-4">


                                        <i class="fas fa-folder-open fa-2x mb-2"></i>

                                        <br>

                                        No training requests submitted yet.


                                    </td>

                                </tr>


                            <?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </div>


    </div>

</div>


<?php require __DIR__ . '/create-modal.php'; ?>