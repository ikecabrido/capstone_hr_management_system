<div class="w-[500px]">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-circle mr-2"></i>
                Employee Information
            </h5>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Employee No.
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employee['employee_no'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Name
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employee['full_name'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Department
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employee['department'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Position
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employee['position_name'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Date Hired
                </div>
                <div class="col-7">
                    <?= !empty($employee['date_hired'])
                        ? date('F d, Y', strtotime($employee['date_hired']))
                        : '-' ?>
                </div>
            </div>

            <div class="row">
                <div class="col-5 text-muted font-weight-bold">
                    Employment
                </div>
                <div class="col-7">
                    <span class="badge badge-success px-3 py-2">
                        <?= ucfirst($employee['employment_status'] ?? '-') ?>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>