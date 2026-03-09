<?php
$employee = [
    'name' => 'Juan Dela Cruz',
    'position' => 'Software Developer',
    'employment_status' => 'Active',
    'employee_id' => 'EMP-001',
    'email' => 'juan.delacruz@example.com',
    'employee_type' => 'Full-Time',
    'department' => 'IT Department',
    'campus' => 'Main Campus',
    'date_hired' => '2021-06-15',
    'profile_image' => '',
    'cover_image' => ''
];

$requestTypes = [
    ['id' => 1, 'name' => 'Leave Request', 'icon' => 'fa-calendar'],
    ['id' => 2, 'name' => 'Overtime Request', 'icon' => 'fa-clock'],
    ['id' => 3, 'name' => 'Reimbursement', 'icon' => 'fa-money-bill'],
    ['id' => 4, 'name' => 'Certificate Request', 'icon' => 'fa-file']
];
?>
<?php echo htmlspecialchars($employee['name']); ?>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            <div class="flex justify-start align-items-center mb-5 p-3 bg-white shadow-sm rounded-4">
                <div class="mb-3 mb-md-0">
                    <h2 class="fw-bold mb-1 display-5">Employee Portal</h2>
                    <p class="text-muted mb-0">
                        Welcome back, <span class="fw-semibold"><?= htmlspecialchars($employee['name']) ?></span>
                    </p>
                </div>
            </div>

            <div class="mb-5">
                <p class="font-bold mb-2 text-2xl">Request Buttons:</p>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($requestTypes as $type): ?>
                        <a href="#"
                            class="btn btn-primary rounded-pill shadow-sm py-2 px-3 btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#requestModal<?= $type['id']; ?>">

                            <i class="fa-solid <?= htmlspecialchars($type['icon']); ?>"></i>
                            <?= htmlspecialchars($type['name']); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php // require_once __DIR__ . '../request/request-modal.php'; 
                    ?>
                </div>
            </div>

            <div class="position-relative">

                <div style="
        height:220px;
        background:url('<?= !empty($employee['cover_image'])
                            ? '/hrmsys/public/images/' . $employee['cover_image']
                            : '/hrmsys/public/assets/images/default-cover.jpg' ?>')
        center/cover;
        border-radius:8px;">
                </div>

                <div class="position-absolute" style="bottom:-60px; left:30px;">

                    <img
                        src="<?= !empty($employee['profile_image'])
                                    ? '/hrmsys/public/images/' . $employee['profile_image']
                                    : '/hrmsys/public/assets/images/default-profile.jpg' ?>"
                        class="rounded-circle border border-4 border-white shadow"
                        style="
                width:140px;
                height:140px;
                object-fit:cover;
            "
                        alt="Profile Image">

                </div>

            </div>

            <div class="row mt-[80px] g-4">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="mb-1"><?= htmlspecialchars($employee['name']) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars($employee['position']) ?></p>

                            <span class="badge bg-success mb-3">
                                <?= htmlspecialchars($employee['employment_status']) ?>
                            </span>

                            <hr>

                            <p class="mb-1"><strong>Employee ID:</strong></p>
                            <p><?= htmlspecialchars($employee['employee_id']) ?></p>

                            <a href="index.php?url=profile" class="btn btn-primary btn-sm w-100">
                                Edit Personal Info
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Employee Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>Email:</strong><br><?= htmlspecialchars($employee['email']) ?></div>
                                <div class="col-md-6 mb-2"><strong>Type:</strong><br><?= htmlspecialchars($employee['employee_type']) ?></div>
                                <div class="col-md-6 mb-2"><strong>Department:</strong><br><?= htmlspecialchars($employee['department']) ?></div>
                                <div class="col-md-6 mb-2"><strong>Campus:</strong><br><?= htmlspecialchars($employee['campus']) ?></div>
                                <div class="col-md-6 mb-2"><strong>Date Hired:</strong><br><?= htmlspecialchars($employee['date_hired']) ?></div>
                                <div class="col-md-6 mb-2"><strong>Years in Service:</strong><br>
                                    <?= $employee['date_hired']
                                        ? date_diff(date_create($employee['date_hired']), date_create())->y
                                        : 0 ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <h5>📝 Leave Requests</h5>
                            <p class="text-muted">Apply & track leave</p>
                            <a href="#" class="btn btn-outline-primary btn-sm" data-bs-target="#requestLeaveModal" data-bs-toggle="modal">Request Leave</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <h5>💰 Payslips</h5>
                            <p class="text-muted">Salary history</p>
                            <a href="#" class="btn btn-outline-success btn-sm" data-bs-target="#viewPayslipsModal" data-bs-toggle="modal">View Payslips</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <h5>📅 Attendance</h5>
                            <p class="text-muted">View attendance</p>
                            <a href="#" class="btn btn-outline-warning btn-sm">View Logs</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <h5>🔐 Security</h5>
                            <p class="text-muted">Account protection</p>
                            <a href="index.php?url=profile-save-password" class="btn btn-outline-danger btn-sm">
                                Change Password
                            </a>
                        </div>
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
    </section>
</div>