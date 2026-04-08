<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="employee_portal.html" class="brand-link">
        <img src="<?= $base ?>/assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3"
            style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= $base ?>/assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="text-[14px] nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php?url=dashboard" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Profile
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=user-profile" class="nav-link">
                                <i class="nav-icon fas fa-user-edit text-warning"></i>
                                <p>Manage Profile</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>
                            Learning & Development
                            <i class="right fas fa-chevron-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=user-profile" class="nav-link">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Course Enrollment Training</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Engagement Relations
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview sub-menu">
                        <li class="nav-item">
                            <a href="index.php?url=employee-grievance" class="nav-link sub-link">
                                <i class="fas fa-comments nav-icon text-warning"></i>
                                <p>Grievance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?url=employee-announcements" class="nav-link sub-link">
                                <i class="fas fa-bullhorn nav-icon text-warning"></i>
                                <p>Announcements</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            Payroll
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=employee-payslip-items" class="nav-link">
                                <i class="far fa-file-alt nav-icon text-warning"></i>
                                <p>Payslip</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Performance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=performance-feedback" class="nav-link">
                                <i class="fas fa-comments nav-icon text-warning"></i>
                                <p>360 Degree Feedback</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clinic-medical"></i>
                        <p>
                            Clinic
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=employee-medical-records" class="nav-link">
                                <i class="fas fa-notes-medical nav-icon text-warning"></i>
                                <p>Medical Records</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>
                            Request
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=employee-leave-request" class="nav-link">
                                <i class="fas fa-calendar-alt nav-icon text-warning"></i>
                                <p>Leave Request</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?url=employee-documents-index" class="nav-link">
                                <i class="fas fa-file-alt nav-icon text-warning"></i>
                                <p>Employee Documents</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="online-training.php" class="nav-link">
                                <i class="fas fa-chalkboard-teacher nav-icon text-warning"></i>
                                <p>Online Training Request</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-clock nav-icon text-warning"></i>
                                <p>View Schedule</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?url=online-meeting" class="nav-link">
                                <i class="fas fa-video nav-icon text-warning"></i>
                                <p>Online Meeting</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=auth-logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <section class="flex flex-col items-center mt-44">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aiChatModal">
                AI Assistant
            </button>
        </section>
    </div>
</aside>

<?php require $partials . 'modal-ai.php'; ?>