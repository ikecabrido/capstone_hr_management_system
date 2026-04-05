<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="employee_portal.html" class="brand-link">
        <img src="<?= $base ?>/assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3"
            style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= $base ?>/assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="text-[14px] nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="index.php?url=admin-dashboard" class="nav-link active">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-leave-request" class="nav-link">
                        <i class="fas fa-calendar-alt nav-icon"></i>
                        <p>Leave Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-online-meeting" class="nav-link">
                        <i class="fas fa-video nav-icon"></i>
                        <p>Online Meeting</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/capstone_hr_management_system/employee_portal/index.php?url=admin-documents-index" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Employee Documents</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <p>Payslip Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chalkboard-teacher nav-icon"></i>
                        <p>Online Training Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i>
                        <p>View Schedule</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=auth-logout" class="nav-link">
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