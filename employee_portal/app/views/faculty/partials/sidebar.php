<aside class="sidebar">
    <div class="school-logo">
        <img src="<?php echo BASE_URL ?>/assets/images/bcp-logo.png" alt="School Logo">


        <div class="sidebar-icons d-flex align-items-center gap-3">

            <button type="button" id="notif-btn" class="btn position-relative p-0 border-0 bg-transparent">
                <i class="fa-regular fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    5
                </span>
            </button>

            <button type="button" class="btn p-0 border-0 bg-transparent">
                <i class="fa-regular fa-circle-user fs-5"></i>
            </button>

        </div>


    </div>
    <div class="sidebar-header">
        <div class="user-avatar"><?= $first_two ?></div>
        <h1><?= $name ?></h1>
        <p class="user-id">SAMPLEID123456789</p>
    </div>


    <div class="accordion" id="sidebarMenu">

        <!-- dashboard -->

        <h2>Dashboard</h2>
        <ul>
            <li><a href="<?php echo BASE_URL ?>/home" class="menu-link <?php echo CURRENT_URI  === "home" ? 'active' : '' ?> " id="dashboard">Dashboard</a></li>
        </ul>

        <!-- instructor management -->
        <hr>

        <h2>
            <a
                data-bs-toggle="collapse"
                href="#instructorMenu"
                class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Instructor Management
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>

        <div
            id="instructorMenu"
            class="collapse <?php echo CURRENT_URI === 'instructor' ? 'show' : ''; ?>"
            data-bs-parent="#sidebarMenu">
            <ul>
                <li>
                    <a
                        href="<?= BASE_URL ?>/instructor"
                        class="menu-link <?php echo CURRENT_URI === 'instructor' ? 'active' : ''; ?>"
                        id="instructor">
                        Instructors
                    </a>
                </li>
            </ul>
        </div>

        <!-- student management -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#studentMenu" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Student Management
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>

        <div id="studentMenu" class="collapse <?php echo in_array(CURRENT_URI, ['students', 'enrollees']) ? 'show' : '' ?>" data-bs-parent="#sidebarMenu">
            <ul>
                <li><a href="<?php echo BASE_URL ?>/students" class="menu-link <?php echo CURRENT_URI  === "students" ? 'active' : '' ?>" id="student">Students</a></li>
                <li><a href="<?php echo BASE_URL ?>/enrollees" class="menu-link <?php echo CURRENT_URI  === "Enrollees" ? 'active' : '' ?>" id="Enrollees">Enrollees</a></li>

            </ul>
        </div>

        <!-- school management -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#schoolMenu" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Academic Management
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
        <div id="schoolMenu" class="collapse <?php echo in_array(CURRENT_URI, ['course', 'school-year', 'semester', 'strand']) ? 'show' : '' ?>" data-bs-parent="#sidebarMenu">
            <ul>
                <li><a href="<?php echo BASE_URL ?>/course" class="menu-link <?php echo CURRENT_URI  === "course" ? 'active' : '' ?>" id="course">Courses</a></li>
                <li><a href="<?php echo BASE_URL ?>/school-year" class="menu-link <?php echo CURRENT_URI  === "school-year" ? 'active' : '' ?>" id="course">School Year</a></li>
                <li><a href="<?php echo BASE_URL ?>/semester" class="menu-link <?php echo CURRENT_URI  === "semester" ? 'active' : '' ?>" id="semester">Semesters</a></li>
                <li><a href="<?php echo BASE_URL ?>/strand" class="menu-link <?php echo CURRENT_URI  === "strand" ? 'active' : '' ?>" id="strand">Strands</a></li>
                <li><a href="<?php echo BASE_URL ?>/subject" class="menu-link <?php echo CURRENT_URI  === "subject" ? 'active' : '' ?>" id="course">Subjects</a></li>
                <li><a href="<?php echo BASE_URL ?>/room" class="menu-link <?php echo CURRENT_URI  === "room" ? 'active' : '' ?>" id="course">Rooms</a></li>

            </ul>
        </div>

        <!-- reports and request -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#schoolReports" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Reports and Requests
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
        <div id="schoolReports" class="collapse <?php echo in_array(CURRENT_URI, ['reports', 'file']) ? 'show' : '' ?>" data-bs-parent="#sidebarMenu">
            <ul>
                <li><a href="<?php echo BASE_URL ?>/reports" class="menu-link <?php echo CURRENT_URI  === "reports" ? 'active' : '' ?>" id="reports">Reports</a></li>
                <li><a href="<?php echo BASE_URL ?>/file" class="menu-link <?php echo CURRENT_URI  === "file" ? 'active' : '' ?>" id="file">File Requests</a></li>
            </ul>
        </div>

        <!-- tools  -->

        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#schoolTools" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Tools
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
        <div id="schoolTools" class="collapse <?php echo in_array(CURRENT_URI, ['recog', 'calendar']) ? 'show' : '' ?>" data-bs-parent="#sidebarMenu">
            <ul>
                <li><a href="<?php echo BASE_URL ?>/tools/recog" class="menu-link <?php echo CURRENT_URI  === "recog" ? 'active' : '' ?> " id="ocr">Image-to-Text</a></li>
                <li><a href="<?php echo BASE_URL ?>students" class="menu-link">Notes</a></li>
                <li><a href="<?php echo BASE_URL ?>/calendar" class="menu-link <?php echo CURRENT_URI  === "calendar" ? 'active' : '' ?>" id="calendar">Calendar</a></li>
            </ul>
        </div>

        <!-- settings  -->
        <hr>
        <h2>
            <a data-bs-toggle="collapse" href="#schoolSettings" class="menu-toggle d-flex justify-content-between align-items-center text-decoration-none text-white">
                Settings
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </h2>
        <div id="schoolSettings" class="collapse <?php echo in_array(CURRENT_URI, ['activity']) ? 'show' : '' ?>" data-bs-parent="#sidebarMenu">
            <ul>
                <li><a href="<?php echo BASE_URL ?>students" class="menu-link" id="profile">Profile</a></li>
                <li><a href="<?php echo BASE_URL ?>/settings/activity" class="menu-link <?php echo CURRENT_URI  === "activity" ? 'active' : '' ?>"" id=" profile">Activity Logs</a></li>
            </ul>
        </div>
        <hr>
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/logout"
                class="d-flex justify-content-between align-items-center text-decoration-none text-white text-uppercase"
                style="margin-left: 8px;">
                <span>Logout</span>
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </div>

</aside>