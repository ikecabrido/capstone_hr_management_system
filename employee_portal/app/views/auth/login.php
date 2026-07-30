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
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="/capstone_hr_management_system/assets/css/login.css">
    <?php
    $password = 'employee123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    ?>
    <?php require __DIR__ . '/style.php';?>
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
                    <img
                        src="/capstone_hr_management_system/assets/pics/bcpLogo.png"
                        class="login-logo"
                        alt="BCP Logo"
                        loading="lazy"
                        width="40"
                        height="40" />

                    <h1 class="ml-2">Login</h1>
                    <div></div>
                </div>
                <!-- Username -->
                <div class="form-field flex">
                    <label for="username">Username</label>

                    <div class="custom-input-group">
                        <div class="input-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>

                        <input
                            type="text"
                            name="username"
                            id="username"
                            placeholder="Enter your employee number"
                            autocomplete="username"
                            required />
                    </div>
                </div>

                <!-- Password -->
                <div class="form-field flex">
                    <label for="password">Password</label>

                    <div class="custom-input-group">

                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required />

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password', 'passwordIcon')"
                            aria-label="Show password">
                            <i id="passwordIcon" class="fas fa-eye"></i>
                        </button>

                    </div>
                </div>

                <!-- Login -->
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt mr-1"></i>
                    Login
                </button>

                <div class="text-center mt-3">

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
                            <a
                                class="link ml-2"
                                href="http://localhost/capstone_hr_management_system/login_form.php">
                                Click Here!
                            </a>
                        </span>
                    </p>

                </div>

            </form>

            <script>
                function togglePassword(inputId, iconId) {

                    const input = document.getElementById(inputId);
                    const icon = document.getElementById(iconId);

                    if (input.type === "password") {

                        input.type = "text";

                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");

                    } else {

                        input.type = "password";

                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");

                    }
                }
            </script>
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