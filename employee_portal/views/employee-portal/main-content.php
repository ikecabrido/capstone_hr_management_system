<?php require_once __DIR__ . '/sampleData.php'; ?>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 p-3 bg-white shadow-sm rounded">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold mb-1 text-5xl">Employee Portal</h2>
            <p class="text-muted mb-0">
                Welcome back,
                <span class="fw-semibold">
                    <?= htmlspecialchars($employee['name'] ?? 'Employee'); ?>
                </span>
            </p>
        </div>

        <div>
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=leave-requests-index"
                class="btn btn-outline-primary bg-slate-50">
                <i class="fa-solid fa-gear me-1"></i> Manage Leave Request
            </a>
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=request-index"
                class="btn btn-outline-primary bg-slate-50">
                <i class="fa-solid fa-gear me-1"></i> Manage Request
            </a>
            <a href="/capstone_hr_management_system/employee_portal/index.php?url=request-types"
                class="btn btn-outline-primary bg-slate-50">
                <i class="fa-solid fa-gear me-1"></i> Manage Request Types
            </a>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/notif.php'; ?>

    <div class="position-relative">
        <div
            style="
                        height:220px;
                        background:url('<?= !empty($employee['cover_image'])
                                            ? '/hrmsys/public/images/' . $employee['cover_image']
                                            : '/hrmsys/public/assets/images/default-cover.jpg' ?>')
                        center/cover;
                        border-radius:8px;
                    ">
        </div>

        <div class="position-absolute" style="bottom:-60px; left:30px;">
            <img
                src="<?= !empty($employee['profile_image'])
                            ? '/hrmsys/public/images/' . $employee['profile_image']
                            : '/hrmsys/public/assets/images/default-profile.jpg' ?>"
                class="rounded-circle border border-4 border-white shadow"
                style="width:140px;height:140px;object-fit:cover;"
                alt="Profile Image">
        </div>
    </div>

    <div class="row mt-5 pt-4 g-4">

        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="mb-1"><?= htmlspecialchars($employee['name']); ?></h5>
                    <p class="text-muted mb-2"><?= htmlspecialchars($employee['position']); ?></p>

                    <span class="badge bg-success mb-3">
                        <?= htmlspecialchars($employee['employment_status']); ?>
                    </span>

                    <hr>

                    <p class="mb-1 fw-bold">Employee ID:</p>
                    <p><?= htmlspecialchars($employee['employee_id']); ?></p>

                    <a href="index.php?url=profile" class="btn btn-primary btn-sm w-100">
                        Edit Personal Info
                    </a>
                </div>
            </div>
        </div>

        <!-- Employee Information Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Employee Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($employee['email']); ?>
                        </div>
                        <div class="col-6">
                            <strong>Type:</strong><br>
                            <?= htmlspecialchars($employee['employee_type']); ?>
                        </div>
                        <div class="col-6">
                            <strong>Department:</strong><br>
                            <?= htmlspecialchars($employee['department']); ?>
                        </div>
                        <div class="col-6">
                            <strong>Campus:</strong><br>
                            <?= htmlspecialchars($employee['campus']); ?>
                        </div>
                        <div class="col-6">
                            <strong>Date Hired:</strong><br>
                            <?= htmlspecialchars($employee['date_hired']); ?>
                        </div>
                        <div class="col-6">
                            <strong>Years in Service:</strong><br>
                            <?= $employee['date_hired']
                                ? date_diff(date_create($employee['date_hired']), date_create())->y
                                : 0 ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Request Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fa-solid fa-calendar-plus fa-2x text-success mb-2"></i>
                    <h5 class="fw-semibold mb-2">Submit Leave Request</h5>
                    <p class="text-muted small mb-3">
                        Quickly submit a new leave request for approval.
                    </p>
                    <a href="#"
                        class="btn btn-success w-100 mt-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#createLeaveRequestModal">
                        <i class="fa-solid fa-paper-plane me-1"></i> Create Leave Request
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-4 mt-3">
        <div class="mb-5">
            <p class="fw-bold mb-3 fs-5">Request Buttons:</p>

            <div class="row g-3">

                <?php foreach ($requestTypes as $type): ?>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 h-100 request-card">

                            <div class="card-body d-flex flex-column text-center">

                                <div class="mb-3">
                                    <i class="fa-solid <?= htmlspecialchars($type['icon']); ?> fa-2x text-primary"></i>
                                </div>

                                <h5 class="fw-semibold">
                                    <?= htmlspecialchars($type['name']); ?>
                                </h5>

                                <p class="text-muted small flex-grow-1">
                                    <?= htmlspecialchars($type['description'] ?? 'No description available.') ?>
                                </p>

                                <a href="#"
                                    class="btn btn-outline-primary btn-sm mt-auto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#requestModal<?= $type['id']; ?>">
                                    <i class="fa-solid fa-paper-plane me-1"></i>
                                    Request
                                </a>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

                <?php
                $types = $requestTypes;
                require __DIR__ . '/request-modal.php';
                ?>

            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Activity</h5>
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">✔ Profile information updated</li>
            <li class="list-group-item">✔ Last login recorded</li>
            <li class="list-group-item">✔ Password changed successfully</li>
        </ul>
    </div>

</div>

<?php __DIR__ . '/request-modal.php'; ?>
<?php require __DIR__ . '/../leave-request/create-leave-request-modal.php'; ?>