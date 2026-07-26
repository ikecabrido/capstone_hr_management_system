<?php
// app/views/auth/index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($title ?? 'HR Employee Portal'); ?>
    </title>

    <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .auth-card {
            background: #fff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <?php if (!empty($_SESSION['error'])): ?>

            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']); ?>

                <button type="button"
                    class="close"
                    data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>


        <?php if (!empty($_SESSION['success'])): ?>

            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success']); ?>

                <button type="button"
                    class="close"
                    data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>


        <div class="auth-card">

            <?php
            if (isset($content) && file_exists($content)) {
                require $content;
            } else {
                echo '<div class="alert alert-danger">
                    Authentication page could not be loaded.
                  </div>';
            }
            ?>

        </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>