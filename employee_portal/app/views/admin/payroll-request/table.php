<div class="card border-0 shadow-sm mb-1">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <h5 class="font-weight-bold text-4xl mb-1">
                    Payroll Requests
                </h5>

                <small class="text-muted">
                    Monitor and manage submitted employee requests.
                </small>
            </div>

            <div class="d-flex align-items-center mt-2 mt-md-0">

                <div class="input-group payroll-search mr-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>

                    <input
                        type="text"
                        id="payrollSearch"
                        class="form-control border-left-0"
                        placeholder="Search requests...">
                </div>

                <select
                    id="payrollStatusFilter"
                    class="form-control"
                    style="width: 160px;">

                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>

                </select>

            </div>

        </div>

    </div>
</div>
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
            </div>

            <span class="badge badge-dark px-3 py-2">
                <?= count($requests ?? []) ?> Requests
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0" id="payrollRequestTable">

            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Document</th>
                    <th>Purpose</th>
                    <th>Payroll Period</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($requests)): ?>

                    <?php foreach ($requests as $index => $request): ?>

                        <tr>

                            <td>
                                <?= $index + 1 ?>
                            </td>

                            <td>
                                <div class="font-weight-bold">
                                    <?= htmlspecialchars($request['full_name'] ?? 'Unknown Employee') ?>
                                </div>

                                <small class="text-muted">
                                    <?= htmlspecialchars($request['employee_no'] ?? '') ?>
                                </small>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">

                                    <div class="document-icon mr-2">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>

                                    <span>
                                        <?= htmlspecialchars($request['request_type']) ?>
                                    </span>

                                </div>
                            </td>

                            <td>
                                <?= htmlspecialchars($request['purpose'] ?? '—') ?>
                            </td>

                            <td>
                                <?php if (
                                    !empty($request['payroll_period_start']) &&
                                    !empty($request['payroll_period_end'])
                                ): ?>

                                    <?= date('M d, Y', strtotime($request['payroll_period_start'])) ?>
                                    -
                                    <?= date('M d, Y', strtotime($request['payroll_period_end'])) ?>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Not specified
                                    </span>

                                <?php endif; ?>
                            </td>

                            <td>
                                <?= !empty($request['requested_at'])
                                    ? date('M d, Y', strtotime($request['requested_at']))
                                    : '—'
                                ?>
                            </td>

                            <td>

                                <?php
                                $status = $request['status'] ?? 'Pending';

                                $statusClass = match ($status) {
                                    'Pending' => 'badge-warning',
                                    'Processing' => 'badge-info',
                                    'Approved' => 'badge-primary',
                                    'Rejected' => 'badge-danger',
                                    'Completed' => 'badge-success',
                                    'Cancelled' => 'badge-secondary',
                                    default => 'badge-light'
                                };
                                ?>

                                <span class="badge <?= $statusClass ?> payroll-status">
                                    <?= htmlspecialchars($status) ?>
                                </span>

                            </td>

                            <td class="text-center flex">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewPayrollRequestModal<?= (int) $request['id'] ?>"
                                    title="View Request">

                                    <i class="fas fa-eye"></i>

                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-warning ml-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#statusPayrollRequestModal<?= (int) $request['id'] ?>"
                                    title="Update Status">
                                    <i class="fas fa-sync-alt"></i>
                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger ml-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deletePayrollRequestModal<?= (int) $request['id'] ?>"
                                    title="Delete Request">
                                    <i class="fas fa-trash-alt"></i>
                                </button>


                            </td>


                        </tr>
                        <?php require __DIR__ . '/view.php'; ?>
                        <?php require __DIR__ . '/status.php'; ?>
                        <?php require __DIR__ . '/delete.php'; ?>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8" class="text-center py-5">

                            <div class="text-muted">

                                <i class="fas fa-file-invoice fa-3x mb-3"></i>

                                <h6 class="font-weight-bold">
                                    No Payroll Requests
                                </h6>

                                <p class="mb-0">
                                    There are currently no payroll requests to manage.
                                </p>

                            </div>

                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>
    </div>

</div>