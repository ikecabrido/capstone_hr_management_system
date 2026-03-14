<!doctype html>
<html lang="en">

<head>
    <!-- =========================================
         BASIC META SETTINGS
    ========================================== -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Employee Portal</title>

    <!-- =========================================
         FONTS
    ========================================== -->
    <!-- Poppins Font  -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- AdminLTE default font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />

    <!-- =========================================
         CSS FRAMEWORKS
    ========================================== -->

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet"
        href="/capstone_hr_management_system/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">

    <!-- =========================================
         ICON LIBRARIES
    ========================================== -->

    <!-- FontAwesome CDN -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- AdminLTE FontAwesome -->
    <link rel="stylesheet"
        href="../../assets/plugins/fontawesome-free/css/all.min.css" />

    <!-- =========================================
         ADMINLTE PLUGINS
    ========================================== -->

    <!-- overlayScrollbars -->
    <link rel="stylesheet"
        href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />

    <!-- AdminLTE Main Theme -->
    <link rel="stylesheet"
        href="../../assets/dist/css/adminlte.min.css" />

    <!-- =========================================
         CUSTOM STYLES
    ========================================== -->
    <link rel="stylesheet" href="../partials/custom.css">

</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <!-- =========================================
         MAIN WRAPPER
    ========================================== -->
    <div class="wrapper">

        <?php
        $partials = __DIR__ . '../partials/';
        ?>

        <!-- =========================================
             NAVBAR
        ========================================== -->
        <?php require $partials . 'navbar.php'; ?>

        <!-- =========================================
             SIDEBAR
        ========================================== -->
        <?php require $partials . 'sidebar.php'; ?>

        <!-- =========================================
             MAIN PAGE CONTENT
        ========================================== -->
        <section class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-gradient-primary text-white text-center p-4">
            <h1 class="display-1 fw-bold mb-3" style="font-size: 8rem;">404</h1>
            <h2 class="fw-semibold mb-4">Oops! Page Not Found</h2>
            <p class="lead mb-4" style="max-width: 600px;">
                Don’t worry, it happens to the best of us! The page you’re looking for doesn’t exist.
                But there’s always a bright side—let’s get you back on track.
            </p>

            <a href="index.php?url=home" class="btn btn-light btn-lg px-5 fw-bold shadow-sm">
                Go Home
            </a>

            <div class="mt-5">
                <img src="/semsys/public/assets/images/positive-404.png" alt="Positive 404" style="max-width: 300px;">
            </div>
        </section>

        <!-- =========================================
             CONTROL SIDEBAR 
        ========================================== -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Optional right sidebar content -->
        </aside>

        <!-- =========================================
             FOOTER
        ========================================== -->
        <?php require $partials . 'footer.php'; ?>

    </div>
    <!-- END WRAPPER -->



    <!-- =========================================
         REQUIRED JAVASCRIPT LIBRARIES
    ========================================== -->

    <!-- jQuery -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Bundle -->
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- overlayScrollbars -->
    <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

    <!-- AdminLTE Core -->
    <script src="../../assets/dist/js/adminlte.js"></script>



    <!-- =========================================
         OPTIONAL PAGE PLUGINS
    ========================================== -->

    <!-- jQuery Mapael -->
    <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="../../assets/plugins/raphael/raphael.min.js"></script>
    <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>

    <!-- Chart.js -->
    <script src="../../assets/plugins/chart.js/Chart.min.js"></script>



    <!-- =========================================
         CUSTOM JAVASCRIPT
    ========================================== -->
    <script src="../partials/custom.js"></script>

</body>

</html>