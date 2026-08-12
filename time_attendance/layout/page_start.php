<?php
$page_title = $page_title ?? 'Time & Attendance';
$body_class = $body_class ?? 'hold-transition sidebar-mini layout-fixed layout-navbar-fixed ta-module';
$page_head_extra = $page_head_extra ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
  <link rel="stylesheet" href="../assets/css/preloader.css" />

  <?= $page_head_extra ?>
</head>

<body class="<?= htmlspecialchars($body_class) ?>">
  <div class="wrapper">
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>
