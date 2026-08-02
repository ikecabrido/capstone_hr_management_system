<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h6 class="font-weight-bold mb-1">
                    Request History
                </h6>

                <small class="text-muted">
                    View the status and details of your payroll requests.
                </small>
            </div>

            <div class="input-group payroll-search">

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

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table
                class="table table-hover mb-0"
                id="payrollRequestTable">

                <thead class="thead-light">

                    <tr>

                        <th class="pl-4">
                            #
                        </th>

                        <th>
                            Document
                        </th>

                        <th>
                            Purpose
                        </th>

                        <th>
                            Payroll Period
                        </th>

                        <th>
                            Date Requested
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($requests)): ?>

                        <?php foreach ($requests as $index => $request): ?>

                            <?php

                            $status = $request['status'] ?? 'Pending';

                            $statusClass = 'secondary';
                            $statusIcon = 'fas fa-circle';

                            switch ($status) {

                                case 'Pending':
                                    $statusClass = 'warning';
                                    $statusIcon = 'fas fa-clock';
                                    break;

                                case 'Processing':
                                    $statusClass = 'info';
                                    $statusIcon = 'fas fa-spinner';
                                    break;

                                case 'Approved':
                                    $statusClass = 'primary';
                                    $statusIcon = 'fas fa-thumbs-up';
                                    break;

                                case 'Completed':
                                    $statusClass = 'success';
                                    $statusIcon = 'fas fa-check-circle';
                                    break;

                                case 'Rejected':
                                    $statusClass = 'danger';
                                    $statusIcon = 'fas fa-times-circle';
                                    break;

                                case 'Cancelled':
                                    $statusClass = 'secondary';
                                    $statusIcon = 'fas fa-ban';
                                    break;
                            }

                            $startDate =
                                !empty($request['payroll_period_start'])
                                ? date(
                                    'M d, Y',
                                    strtotime($request['payroll_period_start'])
                                )
                                : null;

                            $endDate =
                                !empty($request['payroll_period_end'])
                                ? date(
                                    'M d, Y',
                                    strtotime($request['payroll_period_end'])
                                )
                                : null;

                            if ($startDate && $endDate) {
                                $payrollPeriod =
                                    $startDate . ' - ' . $endDate;
                            } elseif ($startDate) {
                                $payrollPeriod = $startDate;
                            } else {
                                $payrollPeriod = 'N/A';
                            }

                            ?>

                            <tr>

                                <!-- ID -->
                                <td class="pl-4 align-middle">

                                    <span class="text-muted">
                                        <?= $index + 1 ?>
                                    </span>

                                </td>


                                <!-- Document -->
                                <td class="align-middle">

                                    <div class="d-flex align-items-center">

                                        <div class="document-icon mr-2">

                                            <i class="fas fa-file-alt"></i>

                                        </div>

                                        <div>

                                            <span class="font-weight-bold d-block">
                                                <?= htmlspecialchars(
                                                    $request['request_type']
                                                ) ?>
                                            </span>

                                            <small class="text-muted">
                                                Request #<?= (int) $request['id'] ?>
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                <!-- Purpose -->
                                <td class="align-middle">

                                    <span
                                        class="text-muted"
                                        title="<?= htmlspecialchars(
                                                    $request['purpose'] ?? ''
                                                ) ?>">

                                        <?= htmlspecialchars(
                                            $request['purpose'] ?? 'N/A'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- Payroll Period -->
                                <td class="align-middle">

                                    <small>
                                        <?= htmlspecialchars(
                                            $payrollPeriod
                                        ) ?>
                                    </small>

                                </td>


                                <!-- Requested Date -->
                                <td class="align-middle">

                                    <small class="text-muted">

                                        <?= !empty($request['requested_at'])
                                            ? date(
                                                'M d, Y h:i A',
                                                strtotime($request['requested_at'])
                                            )
                                            : 'N/A'
                                        ?>

                                    </small>

                                </td>


                                <!-- Status -->
                                <td class="align-middle">

                                    <span
                                        class="badge badge-<?= $statusClass ?> payroll-status">

                                        <i class="<?= $statusIcon ?> mr-1"></i>

                                        <?= htmlspecialchars($status) ?>

                                    </span>

                                </td>


                                <!-- Action -->
                                <td class="align-middle text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light border"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewPayrollRequestModal<?= (int) $request['id'] ?>"
                                        title="View Request">

                                        <i class="fas fa-eye text-primary"></i>

                                    </button>

                                </td>

                            </tr>
                            
                            <?php require __DIR__ . '/view.php'; ?>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr id="emptyPayrollRow">

                            <td
                                colspan="7"
                                class="text-center py-5">

                                <div class="mb-3">

                                    <i class="fas fa-file-invoice-dollar fa-3x text-muted"></i>

                                </div>

                                <h6 class="font-weight-bold">
                                    No Payroll Requests
                                </h6>

                                <p class="text-muted mb-3">
                                    You haven't submitted any payroll document requests yet.
                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>