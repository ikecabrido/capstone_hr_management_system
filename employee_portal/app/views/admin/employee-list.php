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
                                        type="button"
                                        class="btn btn-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#employeeModal<?= (int) $employee['id']; ?>">

                                        <i class="fas fa-eye"></i>

                                    </button>


                                </td>


                            </tr>

                        <?php require __DIR__ . '/view-modal.php'; ?>
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