<div class="container-fluid py-4">

    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 p-3 bg-primary text-white shadow-sm rounded">
        <div class="mb-2 mb-md-0">
            <h2 class="fw-bold mb-1 text-4xl">Employee Portal</h2>
            <p class="mb-0">Welcome back, <span class="fw-semibold"><?= htmlspecialchars($employee['name'] ?? 'Employee'); ?></span></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=leave-requests-index" class="btn btn-light btn-sm">
                <i class="fa-solid fa-calendar-plus me-1"></i> Leave Requests
            </a>
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=request-index" class="btn btn-light btn-sm">
                <i class="fa-solid fa-envelope me-1"></i> Manage Requests
            </a>
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=request-types" class="btn btn-light btn-sm">
                <i class="fa-solid fa-gear me-1"></i> Request Types
            </a>
        </div>
    </div>

    <div class="row g-4 mt-2">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-light profile-card">
                <div class="card-body text-center">
                    <div class="profile-avatar">
                        <i class="fa-solid fa-user text-7xl"></i>
                    </div>
                    <h5 class="mb-1 fw-bold"><?= htmlspecialchars($employee['name']); ?></h5>
                    <p class="text-primary mb-2 fw-semibold"><?= htmlspecialchars($employee['position']); ?></p>

                    <span class="badge bg-success mb-3 px-3 py-2">
                        <?= htmlspecialchars($employee['employment_status']); ?>
                    </span>
                    <hr>
                    <p class="mb-1 text-muted small">Employee ID</p>
                    <p class="fw-semibold"><?= htmlspecialchars($employee['employee_id']); ?></p>
                    <a href="#"
                        class="btn btn-primary btn-sm w-100 mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#editEmployeeModal">
                        <i class="fa-solid fa-pen me-1"></i> Edit Info
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg border-0 h-100 info-card">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-id-card me-2 text-primary"></i>
                        Employee Information
                    </h5>
                </div>

                <div class="card-body">
                    <div class="info-item">
                        <i class="fa-solid fa-envelope text-primary"></i>
                        <span><?= htmlspecialchars($employee['email']); ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-building text-success"></i>
                        <span><?= htmlspecialchars($employee['department']); ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-briefcase text-warning"></i>
                        <span><?= htmlspecialchars($employee['position']); ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-calendar text-danger"></i>
                        <span><?= htmlspecialchars($employee['date_hired']); ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-clock text-info"></i>
                        <span>
                            <?= $employee['date_hired'] ? date_diff(date_create($employee['date_hired']), date_create())->y : 0 ?>
                            years in service
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-lg h-100 border-0 action-card">
                <div class="card-body d-flex flex-column justify-content-center text-center">

                    <div class="action-icon mb-3">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>

                    <h5 class="fw-bold mb-2">Submit Leave Request</h5>

                    <p class="text-muted small mb-4">
                        Quickly submit and track your leave requests with ease.
                    </p>

                    <a href="#"
                        class="btn btn-gradient w-100 mt-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#createLeaveRequestModal">
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        Create Request
                    </a>

                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start align-items-center flex-wrap mt-10 mb-4 p-3 bg-primary text-white shadow-sm rounded">
        <div class="mb-2 mb-md-0">
            <h2 class="fw-bold mb-1 text-4xl">Request Buttons</h2>
        </div>
    </div>
    <div class="row g-3 mt-4">
        <?php foreach ($requestTypes as $type): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 request-card hover-scale">
                    <div class="card-body d-flex flex-column text-center">
                        <div class="mb-3">
                            <i class="fa-solid <?= htmlspecialchars($type['icon']); ?> fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-semibold"><?= htmlspecialchars($type['name']); ?></h5>
                        <p class="text-muted small flex-grow-1"><?= htmlspecialchars($type['description'] ?? 'No description available.') ?></p>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-auto" data-bs-toggle="modal" data-bs-target="#requestModal<?= $type['id']; ?>">
                            <i class="fa-solid fa-paper-plane me-1"></i> Request
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php
        $types = $requestTypes;
        require __DIR__ . '/request-modal.php';
        ?>

        <?php require __DIR__ . '/request-modal.php'; ?>
        <?php require __DIR__ . '/edit-employee-modal.php'; ?>
        <?php require __DIR__ . '/create-leave-request-modal.php'; ?>
    </div>

    <div class="card shadow-sm mt-4 border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Recent Activity</h5>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Profile information updated</li>
            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Last login recorded</li>
            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Password changed successfully</li>
        </ul>
    </div>

</div>