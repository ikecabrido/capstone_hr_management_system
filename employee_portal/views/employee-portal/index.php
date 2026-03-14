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

    <title><?= $title ?? 'Request Types'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/dist/css/adminlte.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/views/partials/custom.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        <?php require $partials . 'navbar.php'; ?>

        <?php require $partials . 'sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content p-3">

                <?php
                if (file_exists($content)) {
                    require $content;
                } else {
                    echo "<div class='alert alert-danger'>Page content not found.</div>";
                }
                ?>

            </section>
        </div>

        <?php require $partials . 'footer.php'; ?>

    </div>
    <script src="<?= $base ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/dist/js/adminlte.min.js"></script>
    <script src="<?= $base ?>/employee_portal/views/partials/custom.js"></script>

</body>

</html>