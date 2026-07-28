<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            Total Requests
                        </small>
                        <h3 class="font-weight-bold mb-0">
                            <?= $totalRequests ?? 0 ?>
                        </h3>
                    </div>

                    <div class="payroll-admin-icon bg-primary-light text-primary">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            Pending
                        </small>
                        <h3 class="font-weight-bold mb-0">
                            <?= $pendingRequests ?? 0 ?>
                        </h3>
                    </div>

                    <div class="payroll-admin-icon bg-warning-light text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            Processing
                        </small>
                        <h3 class="font-weight-bold mb-0">
                            <?= $processingRequests ?? 0 ?>
                        </h3>
                    </div>

                    <div class="payroll-admin-icon bg-info-light text-info">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            Completed
                        </small>
                        <h3 class="font-weight-bold mb-0">
                            <?= $completedRequests ?? 0 ?>
                        </h3>
                    </div>

                    <div class="payroll-admin-icon bg-success-light text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>