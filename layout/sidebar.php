<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="../index.php" class="brand-link">
    <img src="../assets/pics/bcpLogo.png" alt="BCP Logo" class="brand-image elevation-3" style="opacity: 0.9" />
    <span class="brand-text font-weight-light">HR Performance</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
      <div class="image">
        <i class="fas fa-user-circle fa-2x text-white"></i>
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? $_SESSION['user']['name'] ?? 'User'); ?></a>
        <small class="text-muted"><?php echo htmlspecialchars($_SESSION['user']['role'] ?? 'Employee'); ?></small>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Performance Menu Items -->
        <li class="nav-item">
          <a href="../index.php" class="nav-link">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="360-degree.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '360-degree.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>360° Feedback</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="Goal&KPI.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'Goal&KPI.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-bullseye"></i>
            <p>Goals & KPIs</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="Appraisals&review.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'Appraisals&review.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-clipboard-check"></i>
            <p>Appraisals & Reviews</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="Performancereport.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'Performancereport.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Performance Reports</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="Training.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'Training.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-graduation-cap"></i>
            <p>Training</p>
          </a>
        </li>

        <li class="nav-header">ACCOUNT</li>

        <li class="nav-item">
          <a href="../user_profile/profile_form.php" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>Profile</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../logout.php" class="nav-link">
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

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="../index.php" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Dark Mode">
        <i class="fas fa-moon" id="themeIcon"></i>
      </a>
    </li>
  </ul>
</nav>