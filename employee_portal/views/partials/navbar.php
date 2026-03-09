<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm px-3 z-40">
    <button class="btn text-white me-2" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <a class="navbar-brand fw-bold tracking-wide" href="index.php?url=dashboard">HRMSYS</a>

    <div class="ms-auto d-flex align-items-center gap-3">

        <div class="d-none d-md-flex align-items-center rounded p-1 transition-all bg-[#0D6EFD]">
            <div id="searchIcon" class="cursor-pointer text-muted">
                <i class="fas fa-search fa-xl"></i>
            </div>
            <input type="text" id="searchInput" style="transition: width 0.3s ease-in-out, opacity 0.3s ease-in-out;" class="form-control border-0 shadow-none w-0 opacity-0 transition-all duration-300 ease-in-out" placeholder="Search...">
        </div>

        <button class="btn text-white" id="darkToggle">
            <i class="fas fa-moon"></i>
            <span id="currentTime"></span>
        </button>

        <button class="btn text-white" id="fullscreenBtn">
            <i class="fas fa-expand"></i>
        </button>

        <div class="dropdown">
            <button class="btn text-white dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle fs-5"></i>
                <span class="d-none d-md-inline"><?= $_SESSION['user']['name'] ?? 'User'; ?></span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="index.php?url=profile">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>