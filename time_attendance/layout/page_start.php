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
  <style>
    .preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      z-index: 99999;
    }

    .preloader.flex-column {
      flex-direction: column;
    }

    .preloader.justify-content-center {
      justify-content: center;
    }

    .preloader.align-items-center {
      align-items: center;
    }

    .preloader img {
      max-width: 100px;
      height: auto;
      display: block;
    }

    .animation__wobble {
      animation: wobble 2.5s infinite ease-in-out;
    }

    @keyframes wobble {
      0% {
        transform: translateX(0);
      }
      15% {
        transform: translateX(-5px) rotate(-5deg);
      }
      30% {
        transform: translateX(3px) rotate(3deg);
      }
      45% {
        transform: translateX(-3px) rotate(-3deg);
      }
      60% {
        transform: translateX(2px) rotate(2deg);
      }
      75% {
        transform: translateX(-1px) rotate(-1deg);
      }
      100% {
        transform: translateX(0);
      }
    }
  </style>

  <?= $page_head_extra ?>
</head>

<body class="<?= htmlspecialchars($body_class) ?>">
  <div class="wrapper">
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble" src="../../assets/pics/bcpLogo.png" alt="AdminLTELogo" height="60" width="60" />
    </div>
