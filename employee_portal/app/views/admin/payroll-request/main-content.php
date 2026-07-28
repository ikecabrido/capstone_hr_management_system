<div class="w-full ml-2 mt-4">
    <div class="content-wrapper">
        <div class="pt-10 pl-10 pr-10">

            <?php require __DIR__ . '/../../../views/partials/notif.php'; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="font-weight-bold mb-1 payroll-page-title">
                        Payroll Request Management
                    </h2>

                    <p class="text-muted mb-0 payroll-page-subtitle">
                        Review, process, and manage employee payroll document requests.
                    </p>
                </div>

                <div>
                    <button
                        type="button"
                        class="btn btn-primary shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#payrollRequestModal">
                        <i class="fas fa-plus mr-1"></i>
                        Create Request
                    </button>
                </div>
            </div>

            <?php require __DIR__ . '/card.php'; ?>

            <?php require __DIR__ . '/table.php'; ?>

            <?php require __DIR__ . '/payroll-request.php'; ?>

        </div>
    </div>
</div>

<link
    rel="stylesheet"
    href="/capstone_hr_management_system/employee_portal/public/assets/css/payroll-request.css">

<script
    src="/capstone_hr_management_system/employee_portal/public/assets/js/payrollRequest.js">
</script>
