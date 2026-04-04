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

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

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


    </div>

    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= $base ?>/assets/dist/js/adminlte.min.js"></script>
    <script src="<?= $base ?>/employee_portal/app/views/partials/custom.js"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/js/time.js"></script>
    <script>
        function printDiv(divId) {
            const divContents = document.getElementById(divId).innerHTML;

            const printWindow = window.open('', '', 'height=600,width=900');
            printWindow.document.write('<html><head><title>Print</title>');

            const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
                .map(el => el.outerHTML)
                .join('');
            printWindow.document.write(styles);

            printWindow.document.write('</head><body>');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>

</body>

</html>