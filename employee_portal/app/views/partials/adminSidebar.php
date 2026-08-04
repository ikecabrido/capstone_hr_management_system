<?php require __DIR__ . '/fetchName.php'; ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link ">
        <img src="<?= $base ?>/assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3"
            style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= $base ?>/employee_portal/public/assets/image/default_user_icon.webp"
                    class="img-circle elevation-2"
                    alt="User Image" />
            </div>

            <div class="info text-white">
                <span class="d-block fw-bold">
                    <?= htmlspecialchars(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? 'Admin')) ?>
                </span>

                <small class="text-muted text-white opacity-60">
                    @<?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="text-[14px] nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php?url=admin-dashboard" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=admin-employee-list" class="nav-link">
                        <i class="fas fa-id-card-alt nav-icon"></i>
                        <p>View Employee List</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=admin-view-attendance" class="nav-link">
                        <i class="fas fa-user-check nav-icon"></i>
                        <p>View Attendance</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-leave-request" class="nav-link">
                        <i class="fas fa-calendar-alt nav-icon"></i>
                        <p>Manage Leave Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-benefits-and-gov-contrib" class="nav-link d-flex align-items-center">
                        <i class="fas fa-hand-holding-heart nav-icon mr-3"></i>
                        <p class="m-0">Manage Benefits & <br> Government <br> Contributions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-online-meeting" class="nav-link">
                        <i class="fas fa-video nav-icon"></i>
                        <p>Manage Online Meeting</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-documents-index" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Manage Documents</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-performance-feedback" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Performance Evaluation</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-training-request" class="nav-link">
                        <i class="nav-icon fas fa-book-reader"></i>
                        <p>Training Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-notification" class="nav-link">
                        <i class="fa-solid fa-bell nav-icon"></i>
                        <p>Manage Notification</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-payroll-request" class="nav-link">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <p>Manage Payslip Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-training-request" class="nav-link d-flex align-items-center">
                        <i class="fas fa-chalkboard-teacher nav-icon mr-3"></i>
                        <p class="m-0">Manage Online <br> Training Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-manage-user" class="nav-link">
                        <i class="fas fa-user-cog nav-icon"></i>
                        <p>User Account Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=employee-hr-index" class="nav-link">
                        <i class="fas fa-user-tie nav-icon"></i>
                        <p>Employee Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-resignation-request" class="nav-link d-flex align-items-center">
                        <i class="fas fa-user-minus nav-icon mr-3"></i>
                        <p>Resignation Request <br> Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=auth-admin-logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>