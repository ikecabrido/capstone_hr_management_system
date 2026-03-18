<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR L&D Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
  </head>
  <body data-role="<?php echo htmlspecialchars(current_role() ?: 'guest'); ?>">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #1a1a2e;">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">L&amp;D Portal</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="index.php">
            <i class="fas fa-home"></i>
            <span class="nav-label">Home Page</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="training.php">
            <i class="fas fa-graduation-cap"></i>
            <span class="nav-label">Training</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="career.php">
            <i class="fas fa-briefcase"></i>
            <span class="nav-label">Career</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="leadership.php">
            <i class="fas fa-crown"></i>
            <span class="nav-label">Leadership</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="performance.php">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-label">Performance</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="lms.php">
            <i class="fas fa-book"></i>
            <span class="nav-label">LMS</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="compliance.php">
            <i class="fas fa-shield-alt"></i>
            <span class="nav-label">Compliance</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="orgdev.php">
            <i class="fas fa-sitemap"></i>
            <span class="nav-label">Org Dev</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="analytics.php">
            <i class="fas fa-chart-pie"></i>
            <span class="nav-label">Analytics</span>
          </a>
        </li>

        <?php if (in_array(current_role(), ['admin','manager','learning'])): ?>
        <li class="nav-item">
          <a class="nav-link icon-nav-link" href="admin.php">
            <i class="fas fa-cog"></i>
            <span class="nav-label">Admin</span>
          </a>
        </li>
        <?php endif; ?>

      </ul>

      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <div class="theme-switch-container" title="Toggle Theme">
            <div class="theme-switch" id="themeSwitch">
              <div class="switch-track">
                <span class="switch-icon left-icon"><i class="fas fa-moon"></i></span>
                <div class="switch-thumb"></div>
                <span class="switch-icon right-icon"><i class="fas fa-sun"></i></span>
              </div>
            </div>

          </div>
        </li>
        <?php if (isset($_SESSION['username'])): ?>
        <li class="nav-item align-self-center">
          <a class="nav-link icon-nav-link d-flex align-items-center" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-label">Logout</span>
          </a>
        </li>
        <?php else: ?>
        <li class="nav-item align-self-center">
          <a class="nav-link icon-nav-link d-flex align-items-center" href="login.php">
            <i class="fas fa-sign-in-alt"></i>
            <span class="nav-label">Login</span>
          </a>
        </li>
        <li class="nav-item align-self-center">
          <a class="nav-link icon-nav-link d-flex align-items-center" href="register.php">
            <i class="fas fa-user-plus"></i>
            <span class="nav-label">Register</span>
          </a>
        </li>
        <?php endif; ?>
      </ul>

    </div>
  </div>
</nav>

<div class="container content">