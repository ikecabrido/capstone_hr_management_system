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
                <li class="nav-item">
                    <a href="index.php?url=user-profile" class="nav-link">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>Manage Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i>
                        <p>Attendance View</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=employee-leave-request" class="nav-link">
                        <i class="fas fa-calendar-alt nav-icon"></i>
                        <p>Leave Request</p>
                    </a>
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
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=employee-payroll-request" class="nav-link">
                                <i class="far fa-file-alt nav-icon text-warning"></i>
                                <p>Payroll Request</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=benefits-and-gov-contrib" class="nav-link">
                        <i class="fas fa-receipt nav-icon"></i>
                        <p class="small">Benefits & Gov Contrib View</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>
                            Announcements & Notifs
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=employee-announcements" class="nav-link">
                                <i class="fas fa-bullhorn nav-icon text-warning"></i>
                                <p>Announcements</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?url=employee-notifications" class="nav-link">
                                <i class="fas fa-bell nav-icon text-warning"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=employee-documents-index" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Employee Documents</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=performance-feedback" class="nav-link">
                        <i class="fas fa-comments nav-icon "></i>
                        <p>Performance View</p>
                    </a>
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
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon text-warning"></i>
                                <p>Course Enrollment Training</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?url=learning-and-development" class="nav-link">
                                <i class="fas fa-book-reader nav-icon text-warning"></i>
                                <p>Training Records</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=online-meeting" class="nav-link">
                        <i class="fas fa-video nav-icon"></i>
                        <p>Meeting Schedule</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=" class="nav-link">
                        <i class="fas fa-comment-alt nav-icon"></i>
                        <p>Employee Complaint</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=employee-medical-records" class="nav-link">
                        <i class="fas fa-notes-medical nav-icon"></i>
                        <p>Medical Records</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?url=employee-grievance" class="nav-link sub-link">
                        <i class="fas fa-comments nav-icon"></i>
                        <p>Grievance</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>
                            Others
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-clock nav-icon text-warning"></i>
                                <p>View Schedule</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?url=training-request" class="nav-link">
                                <i class="fas fa-chalkboard-teacher nav-icon text-warning"></i>
                                <p>Training Request</p>
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
    </div>
</aside>