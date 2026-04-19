<?php
$partials = __DIR__ . '/../../partials/';
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
    <style>
        .card-hover {
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 3px;
        }

        .pagination .active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .program-card {
            transition: all 0.25s ease;
        }

        .program-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .line-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/css/employeeDashboard.css">
    <link rel="stylesheet" href="/capstone_hr_management_system/employee_portal/public/assets/css/style.css">
    <script src="/capstone_hr_management_system/employee_portal/public/assets/js/mobile-responsive.js" defer></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        <?php require $partials . 'navbar.php'; ?>

        <?php require $partials . 'adminSidebar.php'; ?>


        <?php
        if (file_exists($content)) {

            require $content;
        } else {
            echo "<div class='alert alert-danger'>Page content not found.</div>";
        }
        ?>


    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.training-card').forEach(card => {

                card.addEventListener('click', function(e) {

                    if (e.target.closest('button') || e.target.closest('form')) return;

                    const title = this.dataset.title;
                    const desc = this.dataset.description;
                    const trainer = this.dataset.trainer;
                    const status = this.dataset.status;
                    const date = this.dataset.date;
                    const capacity = this.dataset.capacity;
                    const image = this.dataset.image || 'img/placeholder.gif';

                    document.getElementById('view-title').textContent = title;
                    document.getElementById('view-description').textContent = desc;
                    document.getElementById('view-trainer').textContent = trainer;
                    document.getElementById('view-image').src = image;

                    document.getElementById('meta-status').textContent = status;
                    document.getElementById('meta-date').textContent = date;
                    document.getElementById('meta-capacity').textContent = capacity;

                    const modal = new bootstrap.Modal(document.getElementById('viewProgramModal'));
                    modal.show();

                });

            });

        });
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.training-card').forEach(card => {

                card.addEventListener('click', function(e) {

                    if (e.target.closest('button') || e.target.closest('form')) return;

                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const desc = this.dataset.description;
                    const trainer = this.dataset.trainer;
                    const status = this.dataset.status;
                    const date = this.dataset.date;
                    const capacity = this.dataset.capacity;
                    const enrolled = this.dataset.enrolled === '1';

                    document.getElementById('view-title').textContent = title;
                    document.getElementById('view-description').textContent = desc;
                    document.getElementById('view-trainer').textContent = trainer;

                    document.getElementById('meta-status').textContent = status;
                    document.getElementById('meta-date').textContent = date;
                    document.getElementById('meta-capacity').textContent = capacity;

                    const actionsDiv = document.getElementById('view-actions');

                    let formAction = enrolled ? 'unenroll' : 'enroll';
                    let btnClass = enrolled ? 'btn-warning' : 'btn-primary';
                    let btnText = enrolled ? 'Unenroll' : 'Enroll';
                    let icon = enrolled ? 'fa-user-minus' : 'fa-user-plus';

                    actionsDiv.innerHTML = `
        <form method="post">
          <input type="hidden" name="id" value="${id}">
          <input type="hidden" name="action" value="${formAction}">
          <button class="btn ${btnClass}">
            <i class="fa-solid ${icon} me-1"></i> ${btnText}
          </button>
        </form>
      `;

                    const modal = new bootstrap.Modal(document.getElementById('viewProgramModal'));
                    modal.show();

                });

            });

        });
    </script>
    <script src="<?= $base ?>/employee_portal/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= $base ?>/assets/dist/js/adminlte.min.js"></script>
    <script src="<?= $base ?>/employee_portal/app/views/partials/custom.js"></script>
    <script src="<?= $base ?>/employee_portal/public/assets/js/time.js"></script>
</body>

</html>