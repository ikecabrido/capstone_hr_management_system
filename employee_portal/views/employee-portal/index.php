<?php
$partials = __DIR__ . '/../partials/';
$content = $content ?? __DIR__ . '/main-content.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? 'Employee Portal'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../partials/custom.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        <?php require $partials . 'navbar.php'; ?>

        <?php require $partials . 'sidebar.php'; ?>

        <div class="content-wrapper">
            <?php
            if (file_exists($content)) {
                require $content;
            } else {
                echo "<div class='p-3'>Page content not found.</div>";
            }
            ?>
        </div>

        <aside class="control-sidebar control-sidebar-dark"></aside>

        <?php require $partials . 'footer.php'; ?>

    </div>

    <script src="../../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/dist/js/adminlte.min.js"></script>
    <script src="../partials/custom.js"></script>
</body>

</html>