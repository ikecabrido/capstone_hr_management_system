<?php
$partials = __DIR__ . '/views/partials/';
$base = "/capstone_hr_management_system";
$content = $content ?? __DIR__ . '/error-content.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? 'Error'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/dist/css/adminlte.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/views/partials/custom.css">
    <!-- Time Attendance Portal Styles -->
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/time_attendance_portal/assets/style.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/time_attendance_portal/assets/employeeDashboard.css">
    <link rel="stylesheet" href="<?= $base ?>/employee_portal/time_attendance_portal/assets/mobile-sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Debug mobile detection
        console.log('Window width:', window.innerWidth);
        console.log('Window height:', window.innerHeight);
        if (window.innerWidth <= 768) {
            console.log('Mobile breakpoint detected (<= 768px)');
            document.documentElement.setAttribute('data-mobile', 'true');
        }
    </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        <?php require $partials . 'navbar.php'; ?>
        
        <script>
            // Hide preloader immediately when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                const preloader = document.querySelector('.preloader');
                if (preloader) {
                    preloader.style.display = 'none';
                }
            });
        </script>

        <?php require $partials . 'sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content p-3">

                <?php
                if (file_exists($content)) {
                    require $content;
                } else {
                    echo "<div class='alert alert-danger'>";
                    echo "<strong>Page content not found.</strong><br>";
                    echo "Looking for: " . htmlspecialchars($content) . "<br>";
                    echo "File exists: " . (file_exists($content) ? 'Yes' : 'No');
                    echo "</div>";
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
    
    <script>
        // Hide preloader when page loads
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        });
        
        // Also hide preloader after a timeout (in case load event doesn't fire)
        setTimeout(function() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }, 3000);
    </script>

</body>

</html>