<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? 'HR Management System'; ?></title>
    <link rel="stylesheet" href="/capstone_hr_management_system/assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="/capstone_hr_management_system/assets/plugins/toastr/toastr.min.css" />
    <link rel="stylesheet" href="/capstone_hr_management_system/assets/plugins/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/capstone_hr_management_system/login.css" />
</head>

<body>
    <div class="bigbox">
        <div class="box1">
            <h1>
                Employee <br />
                Portal <br />
                Login
            </h1>
        </div>

        <div class="box2">
            <form action="index.php?url=auth-login" method="POST">

                <?php require __DIR__ . '/../partials/notif.php' ?>
                <div class="header">

                    <img src="/capstone_hr_management_system/assets/pics/bcpLogo.png" class="brand-image" alt="AdminLTE Logo"
                        class="brand-image" />
                    <h1>Login</h1>
                    <div></div>
                </div>
                <div class="label">
                    <label for="">Employee No</label>
                    <input
                        type="text"
                        name="employee_no"
                        placeholder="Your User ID..."
                        required />
                </div>
                <div class="label">
                    <label for="">Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Your Password.."
                        required />
                </div>
                <button type="submit" name="login">Login</button>
                <p class="para mt-3 d-flex justify-content-center">
                    Looking for Admin Login?
                    <span>
                        <a class="link" href="http://localhost/capstone_hr_management_system/">
                            Click Here!
                        </a>
                    </span>
                </p>
            </form>
        </div>
    </div>

    <script src="/capstone_hr_management_system/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/capstone_hr_management_system/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/capstone_hr_management_system/assets/plugins/toastr/toastr.min.js"></script>
    <script src="/capstone_hr_management_system/assets/dist/js/adminlte.js"></script>

    <?php if (isset($error) && $error): ?>
        <script>
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Login Failed',
                body: <?= json_encode($error) ?>,
                autohide: true,
                delay: 3000
            });
        </script>
    <?php endif; ?>
</body>

</html>