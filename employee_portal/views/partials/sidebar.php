<aside id="sidebar" class="sidebar bg-primary text-white vh-100 position-fixed top-0 start-0 shadow-lg transition-all w-16 hover:w-64 z-50 group">
    <div class="d-flex flex-column align-items-center justify-content-center py-4 border-bottom border-light">
        <div class="w-14 h-14 bg-white text-center rounded-full flex items-center justify-center text-xl font-bold text-primary group-hover:hidden">
            <img src="/hrmsys/public/assets/images/bcp-logo.png" alt="BCP Logo" class="rounded-full shadow object-cover w-full h-full">
        </div>

        <div class="flex items-center justify-start group-hover:flex hidden">
            <img src="/hrmsys/public/assets/images/bcp-logo.png" alt="BCP Logo" class="rounded-full shadow object-cover w-16 h-16">
            <h5 class="fw-bold text-white ml-2">BCP Bulacan</h5>
        </div>


        <?php require_once __DIR__ . '/sidebarUsername.php'; ?>
    </div>

    <ul class="nav flex-column px-2 py-3 gap-1">
        <?php if (Auth::isAdmin()): ?>
            <li class="group">
                <a href="index.php?url=user-index" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-user-cog me-2"></i><span class="nav-text hidden group-hover:inline">Manage Users</span>
                </a>
            </li>

            <li class="group">
                <a href="index.php?url=employee-index" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-users me-2"></i><span class="nav-text hidden group-hover:inline">Manage Employees</span>
                </a>
            </li>

            <li class="group">
                <a href="index.php?url=employee-portal" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-clipboard-list me-2"></i><span class="nav-text hidden group-hover:inline">Employee Portal</span>
                </a>
            </li>

            <li class="group">
                <a href="index.php?url=admin-request" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-address-book me-2"></i><span class="nav-text hidden group-hover:inline">Manage Request</span>
                </a>
            </li>

            <li class="group">
                <a href="index.php?url=admin-request-types" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-table me-2"></i><span class="nav-text hidden group-hover:inline">Manage Request Type</span>
                </a>
            </li>

            <li class="group">
                <a href="#" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-cogs me-2"></i><span class="nav-text hidden group-hover:inline">Module 1</span>
                </a>
            </li>

            <li class="group">
                <a href="#" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-chart-line me-2"></i><span class="nav-text hidden group-hover:inline">Module 2</span>
                </a>
            </li>

            <li class="group">
                <a href="#" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-table me-2"></i><span class="nav-text hidden group-hover:inline">Module 3</span>
                </a>
            </li>

            <li class="group">
                <a href="#" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-users-cog me-2"></i><span class="nav-text hidden group-hover:inline">Module 4</span>
                </a>
            </li>

            <li class="group">
                <a href="#" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-folder-open me-2"></i><span class="nav-text hidden group-hover:inline">Module 5</span>
                </a>
            </li>

        <?php else: ?>
            <li class="group">
                <a href="index.php?url=employee-portal" class="nav-link text-white rounded hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-clipboard-list me-2"></i><span class="nav-text hidden group-hover:inline">Employee Portal</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="mt-3 border-top border-light pt-3">
            <a href="index.php?url=logout.php" class="nav-link text-danger hover:bg-white bg-opacity-10 rounded">
                <i class="fas fa-sign-out-alt me-2"></i><span class="nav-text hidden group-hover:inline">Logout</span>
            </a>
        </li>
    </ul>

</aside>