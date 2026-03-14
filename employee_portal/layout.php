<?php
$partials = __DIR__ . '/views/partials/';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $title ?? 'Employee Portal'; ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/assets/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/views/partials/custom.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

  <div class="wrapper">

    <?php require $partials . 'navbar.php'; ?>

    <?php require $partials . 'sidebar.php'; ?>

    <div class="content-wrapper">

      <section class="content p-3">

        <?php
        if (isset($content) && file_exists($content)) {
          require $content;
        } else {
          echo "<div class='p-3'>Page content not found.</div>";
        }
        ?>

      </section>

    </div>

    <?php require $partials . 'footer.php'; ?>

  </div>

  <script src="/capstone_hr_management_system/employee_portal/assets/plugins/jquery/jquery.min.js"></script>
  <script src="/capstone_hr_management_system/employee_portal/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/capstone_hr_management_system/employee_portal/assets/dist/js/adminlte.min.js"></script>

  <script src="/capstone_hr_management_system/employee_portal/views/partials/custom.js"></script>

</body>

</html>