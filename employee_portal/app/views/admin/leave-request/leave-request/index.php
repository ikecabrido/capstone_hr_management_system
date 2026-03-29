<?php
$partials = __DIR__ . '/../partials/';
$base = "/capstone_hr_management_system";
$content = $content ?? __DIR__ . '/main-content.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? 'HR Management System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/dist/css/adminlte.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/app/views/partials/custom.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/public/assets/css/employee-portal.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/css/employeeDashboard.css">
    <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/css/style.css">
    <script src="/capstone_hr_management_system/employee_portal/public/assets/js/mobile-responsive.js" defer></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        <?php require $partials . 'navbar.php'; ?>

        <?php require $partials . 'sidebar.php'; ?>


        <?php
        if (file_exists($content)) {
            require $content;
        } else {
            echo "<div class='alert alert-danger'>Page content not found.</div>";
        }
        ?>

        <?php require $partials . 'footer.php'; ?>

    </div>

    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= $base ?>/assets/dist/js/adminlte.min.js"></script>
    <script src="<?= $base ?>/employee_portal/app/views/partials/custom.js"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/js/time.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const startInput = document.getElementById("start_date");
            const endInput = document.getElementById("end_date");

            const today = new Date();
            const tomorrow = new Date();

            tomorrow.setDate(today.getDate() + 1);

            const formatDate = (date) => date.toISOString().split('T')[0];

            startInput.value = formatDate(today);
            endInput.value = formatDate(tomorrow);

            startInput.min = formatDate(today);
            endInput.min = formatDate(today);
        });
        document.getElementById("start_date").addEventListener("change", function() {
            const start = new Date(this.value);
            const end = new Date(start);
            end.setDate(start.getDate() + 1);

            const formatDate = (date) => date.toISOString().split('T')[0];

            document.getElementById("end_date").value = formatDate(end);
            document.getElementById("end_date").min = formatDate(start);
        });
    </script>


</body>

</html>