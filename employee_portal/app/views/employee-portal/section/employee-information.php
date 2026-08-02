<div class="w-[800px] card shadow-sm border-0 mb-4">
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
                    Employee Code.
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employeeInfo['employee_code'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Name
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employeeInfo['first_name'] ?? '-') ?>
                    <?= htmlspecialchars($employeeInfo['last_name'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Department
                </div>
                <div class="col-7">
                    <?= htmlspecialchars($employeeInfo['department'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Position
                </div>

                <div class="col-7">
                    <?= htmlspecialchars($employeeInfo['position'] ?? '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-5 text-muted font-weight-bold">
                    Date Hired
                </div>
                <div class="col-7">
                    <?= !empty($employeeInfo['hire_date'])
                        ? date('F d, Y', strtotime($employeeInfo['hire_date']))
                        : '-' ?>
                </div>
            </div>

            <div class="row">
                <div class="col-5 text-muted font-weight-bold">
                    Employment
                </div>
                <div class="col-7">
                    <span class="badge badge-success px-3 py-2">
                        <?= ucfirst($employeeInfo['employment_status'] ?? '-') ?>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>