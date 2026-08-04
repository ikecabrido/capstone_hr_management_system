<div class="w-full mt-4">
    <div class="content-wrapper px-4 py-4">

        <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    Employee Management
                </h2>
                <p class="text-muted mb-0">
                    Manage employee records and HR profiles.
                </p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 xl:grid-cols-4">

            <!-- Total Employees -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total Employees
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800">
                            <?= $totalEmployees ?? 0 ?>
                        </h2>
                    </div>

                    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>

                </div>
            </div>

            <!-- Active Employees -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Active Employees
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-green-600">
                            <?= $activeEmployees ?? 0 ?>
                        </h2>
                    </div>

                    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-green-100">
                        <i class="fas fa-user-check text-2xl text-green-600"></i>
                    </div>

                </div>
            </div>

            <!-- Pending Profiles -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            HR Profiles Pending
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-amber-600">
                            <?= $pendingProfiles ?? 0 ?>
                        </h2>
                    </div>

                    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-100">
                        <i class="fas fa-user-clock text-2xl text-amber-600"></i>
                    </div>

                </div>
            </div>

            <!-- New Hires -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            New Hires
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-cyan-600">
                            <?= $newEmployees ?? 0 ?>
                        </h2>
                    </div>

                    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-100">
                        <i class="fas fa-user-plus text-2xl text-cyan-600"></i>
                    </div>

                </div>
            </div>

        </div>

        <!-- Employee Table -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Hire Date</th>
                        <th>HR Profile</th>
                        <th class="text-center">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($existingEmployees)): ?>

                        <?php foreach ($existingEmployees as $employee): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($employee['employee_id'] ?? '-') ?>
                                </td>
                                <td style="font-size: 12px; width: 100px;">
                                    <?= htmlspecialchars($employee['employee_code'] ?? '-') ?>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars(
                                            trim(
                                                $employee['first_name'] . ' ' .
                                                    ($employee['middle_name'] ? $employee['middle_name'] . ' ' : '') .
                                                    $employee['last_name']
                                            )
                                        ) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($employee['department'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($employee['position_title_enum'] ?? '-') ?>
                                </td>

                                <td>
                                    <span class="badge bg-success">
                                        <?= htmlspecialchars($employee['employment_status'] ?? '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($employee['employment_type'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= !empty($employee['hire_date'])
                                        ? date('M d, Y', strtotime($employee['hire_date']))
                                        : '-' ?>
                                </td>

                                <td>

                                    <?php
                                    $isComplete =
                                        !empty($employee['employee_code']) &&
                                        !empty($employee['department_id']) &&
                                        !empty($employee['position_id']) &&
                                        !empty($employee['position_title_enum']) &&
                                        !empty($employee['employment_status']) &&
                                        !empty($employee['employment_type']) &&
                                        !empty($employee['hire_date']);
                                    ?>

                                    <?php if ($isComplete): ?>

                                        <span class="badge bg-success">
                                            Complete
                                        </span>

                                    <?php else: ?>

                                        <button type="button" class="btn btn-sm py-0 px-2 rounded-md btn-warning"
                                            style="font-size: 0.75rem;" data-bs-toggle="modal"
                                            data-bs-target="#employmentModal<?= $employee['employee_id']; ?>">
                                            <?= $isComplete ? 'View Profile' : 'Complete Profile'; ?> </button>

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewEmployeeModal<?= $employee['employee_id']; ?>">

                                        <i class="fas fa-eye"></i>

                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEmployeeModal<?= $employee['employee_id']; ?>">

                                        <i class="fas fa-edit"></i>

                                    </button>

                                </td>

                            </tr>
                            <?php require __DIR__ . '/pending.php'; ?>
                            <?php require __DIR__ . '/edit.php'; ?>
                            <?php require __DIR__ . '/view.php'; ?>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="9" class="text-center py-5 text-muted">

                                <i class="fas fa-users fa-3x mb-3 d-block"></i>

                                No employee records found.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>