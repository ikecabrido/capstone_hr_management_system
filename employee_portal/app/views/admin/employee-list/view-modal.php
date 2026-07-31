<?php foreach ($employees as $employee): ?>

    <?php
    $employeeId = (int) $employee['employee_id'];
    $status = strtolower($employee['employment_status'] ?? 'inactive');
    $statusBadge = $status === 'active' ? 'success' : 'secondary';
    $fullName = trim(
        $employee['first_name'] . ' ' .
            (!empty($employee['middle_name']) ? $employee['middle_name'] . ' ' : '') .
            $employee['last_name'] .
            (!empty($employee['suffix']) ? ' ' . $employee['suffix'] : '')
    );
    ?>
    <div
        class="modal fade"
        id="employeeModal<?= $employeeId; ?>"
        tabindex="-1"
        aria-labelledby="employeeModalLabel<?= $employeeId; ?>"
        aria-hidden="true">

        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content border-0 shadow-lg overflow-hidden">

                <div class="modal-header bg-primary text-white px-4 py-4">

                    <div class="d-flex align-items-center">

                        <div
                            class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:58px;height:58px;font-size:24px;">

                            <i class="fas fa-user"></i>

                        </div>

                        <div>

                            <h5
                                class="modal-title font-weight-bold mb-1"
                                id="employeeModalLabel<?= $employeeId; ?>">

                                <?= htmlspecialchars($fullName ?? 'Employee'); ?>

                            </h5>

                            <div class="small text-white-50">
                                <i class="fas fa-id-badge mr-1"></i>
                                <?= htmlspecialchars($employee['employee_code'] ?? 'N/A'); ?>
                            </div>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                <div class="modal-body p-4">

                    <div class="row mb-4">

                        <div class="col-md-4 mb-3 mb-md-0">

                            <div class="border rounded-lg p-3 h-100">

                                <div class="d-flex align-items-center">

                                    <div class="bg-primary-light text-primary rounded p-2 mr-3">
                                        <i class="fas fa-id-card"></i>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block">
                                            Employee Number
                                        </small>

                                        <strong>
                                            <?= htmlspecialchars($employee['employee_code'] ?? 'N/A'); ?>
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3 mb-md-0">

                            <div class="border rounded-lg p-3 h-100">

                                <div class="d-flex align-items-center">

                                    <div class="bg-info-light text-info rounded p-2 mr-3">
                                        <i class="fas fa-building"></i>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block">
                                            Department
                                        </small>

                                        <strong>
                                            <?= htmlspecialchars($employee['department'] ?? 'N/A'); ?>
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded-lg p-3 h-100">

                                <div class="d-flex align-items-center">

                                    <div class="bg-success-light text-success rounded p-2 mr-3">
                                        <i class="fas fa-user-check"></i>
                                    </div>

                                    <div>
                                        <small class="text-muted d-block">
                                            Employment Status
                                        </small>

                                        <span class="badge badge-<?= $statusBadge; ?> px-2 py-1">
                                            <?= ucfirst(htmlspecialchars($status)); ?>
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="border-top pt-4">

                        <div class="d-flex align-items-center mb-3">

                            <div class="bg-primary-light text-primary rounded p-2 mr-2">
                                <i class="fas fa-user-tie"></i>
                            </div>

                            <div>
                                <h6 class="font-weight-bold mb-0">
                                    Employee Information
                                </h6>

                                <small class="text-muted">
                                    Basic employment details
                                </small>
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="small font-weight-bold text-muted mb-1">
                                    Full Name
                                </label>
                                <div class="form-control bg-light">
                                    <?= htmlspecialchars($fullName ?? 'N/A'); ?>
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="small font-weight-bold text-muted mb-1">
                                    Date Hired
                                </label>

                                <div class="form-control bg-light">

                                    <?= !empty($employee['hire_date'])
                                        ? date('F d, Y', strtotime($employee['hire_date']))
                                        : 'N/A'; ?>

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="small font-weight-bold text-muted mb-1">
                                    Position
                                </label>

                                <div class="form-control bg-light">
                                    <?= htmlspecialchars($employee['position_id'] ?? 'N/A'); ?>
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="small font-weight-bold text-muted mb-1">
                                    Employment Type
                                </label>

                                <div class="form-control bg-light">
                                    <?= htmlspecialchars($employee['employment_type'] ?? 'N/A'); ?>
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="alert alert-light border mt-3 mb-0">

                        <div class="d-flex">

                            <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>

                            <div>
                                <strong class="d-block mb-1">
                                    Employee Profile
                                </strong>

                                <small class="text-muted">
                                    This profile contains basic employee and employment
                                    information maintained by the HR Management System.
                                </small>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer bg-light px-4">

                    <button
                        type="button"
                        class="btn btn-secondary px-4"
                        data-bs-dismiss="modal">

                        <i class="fas fa-times mr-1"></i>
                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>

<?php endforeach; ?>