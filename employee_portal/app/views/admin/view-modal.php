<?php foreach($employees as $employee): ?>

<div class="modal fade" id="employeeModal<?= $employee['id']; ?>" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="modal-header bg-primary text-white p-4">

                <div class="d-flex align-items-center">

                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:65px;height:65px;font-size:28px;">

                        <i class="fas fa-user"></i>

                    </div>

                    <div>
                        <h4 class="mb-1 font-weight-bold">
                            <?= htmlspecialchars($employee['full_name'] ?? 'Employee'); ?>
                        </h4>

                        <small class="text-white-50">
                            Employee Profile Information
                        </small>
                    </div>

                </div>

                <button type="button"
                    class="close text-white"
                    data-bs-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <!-- Body -->
            <div class="modal-body p-4">

                <div class="row mb-4">

                    <div class="col-md-4">

                        <div class="card border-0 bg-light rounded-4 p-3">

                            <small class="text-muted">
                                Employee No
                            </small>

                            <h5 class="font-weight-bold mb-0">
                                <?= htmlspecialchars($employee['employee_no'] ?? 'N/A'); ?>
                            </h5>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="card border-0 bg-light rounded-4 p-3">

                            <small class="text-muted">
                                Department
                            </small>

                            <h5 class="font-weight-bold mb-0">
                                <?= htmlspecialchars($employee['department'] ?? 'N/A'); ?>
                            </h5>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="card border-0 bg-light rounded-4 p-3">

                            <small class="text-muted">
                                Status
                            </small>

                            <?php
                            $status = $employee['employment_status'] ?? 'inactive';
                            $badge = $status == 'active' ? 'success' : 'secondary';
                            ?>

                            <h5 class="mb-0">
                                <span class="badge badge-<?= $badge; ?> px-3 py-2">
                                    <?= ucfirst($status); ?>
                                </span>
                            </h5>

                        </div>

                    </div>

                </div>


                <h6 class="font-weight-bold text-primary mb-3">
                    <i class="fas fa-id-card mr-2"></i>
                    Employee Information
                </h6>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            Full Name
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= htmlspecialchars($employee['full_name'] ?? 'N/A'); ?>">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            Date Hired
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= !empty($employee['date_hired']) 
                                ? date('F d, Y', strtotime($employee['date_hired'])) 
                                : 'N/A'; ?>">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            Position
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= htmlspecialchars($employee['position_id'] ?? 'N/A'); ?>">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            Employment Type
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= htmlspecialchars($employee['employment_type_id'] ?? 'N/A'); ?>">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            User Account ID
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= htmlspecialchars($employee['user_id'] ?? 'N/A'); ?>">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="font-weight-bold text-muted">
                            Category ID
                        </label>

                        <input class="form-control rounded-3"
                            readonly
                            value="<?= htmlspecialchars($employee['category_id'] ?? 'N/A'); ?>">

                    </div>

                </div>


                <div class="alert alert-primary border-0 rounded-4 mt-2 mb-0">

                    <i class="fas fa-info-circle mr-2"></i>

                    This employee profile contains basic HR information and employment details.

                </div>

            </div>


            <div class="modal-footer bg-light">

                <button class="btn btn-secondary px-4 rounded-3"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

<?php endforeach; ?>