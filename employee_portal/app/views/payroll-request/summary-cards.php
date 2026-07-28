<?php
$totalRequests = count($requests ?? []);

$pendingCount = 0;
$processingCount = 0;
$completedCount = 0;
$rejectedCount = 0;

foreach (($requests ?? []) as $request) {

    switch ($request['status'] ?? '') {

        case 'Pending':
            $pendingCount++;
            break;

        case 'Processing':
        case 'Approved':
            $processingCount++;
            break;

        case 'Completed':
            $completedCount++;
            break;

        case 'Rejected':
            $rejectedCount++;
            break;
    }
}
?>

<div class="row mb-4">

    <!-- Total -->
    <div class="col-xl-3 col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted d-block">
                            Total Requests
                        </small>

                        <h3 class="font-weight-bold mb-0">
                            <?= $totalRequests ?>
                        </h3>
                    </div>

                    <div class="payroll-icon bg-primary-light text-primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>

                </div>

            </div>
        </div>

    </div>


    <!-- Pending -->
    <div class="col-xl-3 col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted d-block">
                            Pending
                        </small>

                        <h3 class="font-weight-bold mb-0">
                            <?= $pendingCount ?>
                        </h3>
                    </div>

                    <div class="payroll-icon bg-warning-light text-warning">
                        <i class="fas fa-clock"></i>
                    </div>

                </div>

            </div>
        </div>

    </div>


    <!-- Processing -->
    <div class="col-xl-3 col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted d-block">
                            Processing
                        </small>

                        <h3 class="font-weight-bold mb-0">
                            <?= $processingCount ?>
                        </h3>
                    </div>

                    <div class="payroll-icon bg-info-light text-info">
                        <i class="fas fa-spinner"></i>
                    </div>

                </div>

            </div>
        </div>

    </div>


    <!-- Completed -->
    <div class="col-xl-3 col-md-6 mb-3">

        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted d-block">
                            Completed
                        </small>

                        <h3 class="font-weight-bold mb-0">
                            <?= $completedCount ?>
                        </h3>
                    </div>

                    <div class="payroll-icon bg-success-light text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>