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
    <?php
    $password = 'employee123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    ?>
<style>
    .forgot-password-btn {
        background: none;
        border: none;
        padding: 0;
        color: #6c757d;
        font-size: 14px;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .forgot-password-btn span {
        color: inherit;
    }

    .forgot-password-btn:hover {
        color: #343a40;
        text-decoration: underline;
    }

    .forgot-password-btn:focus,
    .forgot-password-btn:active {
        outline: none;
        box-shadow: none;
    }
</style>
</head>

<body>
    <div class="bigbox">
        <div class="box1">
            <h1 style="font-size: 60px;">
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
                <div class="text-center">
                    <button
                        type="button"
                        class="forgot-password-btn"
                        data-toggle="modal"
                        data-target="#resetPasswordModal">
                        Forgot password? <span>Click Here!</span>
                    </button>
                    <p class="para mt-3 d-flex justify-content-center">
                        Looking for Admin Login?
                        <span>
                            <a class="link ml-2" href="http://localhost/capstone_hr_management_system/login_form.php">
                                Click Here!
                            </a>
                        </span>
                    </p>
                </div>

            </form>
        </div>
    </div>

    <script src="/capstone_hr_management_system/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/capstone_hr_management_system/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/capstone_hr_management_system/assets/plugins/toastr/toastr.min.js"></script>
    <script src="/capstone_hr_management_system/assets/dist/js/adminlte.js"></script>
    <?php require __DIR__ . '/forgot-password-modal.php'; ?>
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