<div class="w-full ml-16">
    <div class="content-wrapper p-4 w-full">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">📊 Admin Dashboard</h4>
                <small class="text-muted">Manage employee documents and approvals</small>
            </div>
            <a href="index.php?url=employee-documents-create" class="btn btn-primary rounded-3">
                + New Document
            </a>
        </div>
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Total Documents</small>
                            <h4 class="fw-bold mb-0 mt-1">128</h4>
                        </div>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            📄
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Pending</small>
                            <h4 class="fw-bold text-warning mb-0 mt-1">32</h4>
                        </div>
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ⏳
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Approved</small>
                            <h4 class="fw-bold text-success mb-0 mt-1">76</h4>
                        </div>
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ✔
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Rejected</small>
                            <h4 class="fw-bold text-danger mb-0 mt-1">20</h4>
                        </div>
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ✖
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3">⚡ Quick Actions</h6>

            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?url=employee-documents-create" class="btn btn-outline-primary rounded-3">
                    Upload Document
                </a>

                <a href="#" class="btn btn-outline-success rounded-3">
                    Approve Requests
                </a>

                <a href="#" class="btn btn-outline-danger rounded-3">
                    Review Rejected
                </a>

                <a href="#" class="btn btn-outline-secondary rounded-3">
                    View Reports
                </a>
            </div>
        </div>

        <!-- Filters / Controls -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3">🔍 Filters</h6>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select class="form-select rounded-3">
                        <option>All Departments</option>
                        <option>HR</option>
                        <option>IT</option>
                        <option>Finance</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select rounded-3">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control rounded-3" placeholder="Search documents...">
                </div>

            </div>

        </div>
        <!-- Employee List -->

        <div class="card border-0 shadow-sm rounded-4 mt-4">

            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

                <h6 class="fw-bold text-4xl mb-0">
                    👥 Employee List
                </h6>

                <span class="badge bg-primary">
                    <?= count($employees ?? []); ?> Employees
                </span>

            </div>


            <div class="card-body">


                <!-- Search -->
                <div class="mb-3">

                    <input
                        type="text"
                        class="form-control rounded-3"
                        placeholder="Search employee...">

                </div>



                <div class="table-responsive">

                    <table class="table table-hover table-striped align-middle">


                        <thead class="table-light">

                            <tr>

                                <th>
                                    Employee No
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    Employment Type
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="100">
                                    Action
                                </th>

                            </tr>

                        </thead>



                        <tbody>


                            <?php if (!empty($employees)): ?>


                                <?php foreach ($employees as $employee): ?>


                                    <tr>


                                        <td>
                                            <?= htmlspecialchars($employee['employee_no']); ?>
                                        </td>


                                        <td>

                                            <strong>
                                                <?= htmlspecialchars($employee['full_name']); ?>
                                            </strong>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $employee['department'] ?? 'N/A'
                                            ); ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $employee['employment_type'] ?? 'N/A'
                                            ); ?>

                                        </td>



                                        <td>

                                            <?php if (
                                                strtolower($employee['employment_status']) == 'active'
                                            ): ?>

                                                <span class="badge bg-success">
                                                    Active
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-danger">
                                                    Inactive
                                                </span>

                                            <?php endif; ?>

                                        </td>



                                        <td>

                                            <button
                                                class="btn btn-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#employeeModal<?= $employee['id']; ?>">

                                                <i class="fas fa-eye"></i>

                                            </button>


                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <tr>

                                    <td colspan="6" class="text-center text-muted py-4">

                                        No employees found.

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