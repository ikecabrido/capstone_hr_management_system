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
    <style>
        /* =========================================
   FORM FIELD
========================================= */

        .form-field {
            width: 100%;
            margin-bottom: 18px;
        }

        .form-field label {
            display: block;
            margin-bottom: 7px;

            font-size: 14px;
            font-weight: 600;
            color: #343a40;
        }


        /* =========================================
   INPUT GROUP
========================================= */

        .custom-input-group {
            position: relative;

            display: flex;
            align-items: center;

            width: 100%;
            height: 48px;

            background: #ffffff;
            border: 1px solid #d8dde3;
            border-radius: 9px;

            overflow: hidden;

            transition: border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        /* Focus entire input */
        .custom-input-group:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.10);
        }


        /* =========================================
   LEFT ICON
========================================= */

        .input-icon {
            width: 48px;
            min-width: 48px;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #6c757d;
            font-size: 15px;

            pointer-events: none;
        }

        .input-icon i {
            display: block;
            line-height: 1;
        }


        /* =========================================
   TEXT INPUT
========================================= */

        .custom-input-group input {
            flex: 1;

            min-width: 0;
            width: 100%;
            height: 100%;

            padding: 0 10px 0 0;

            border: none;
            outline: none;

            background: transparent;

            font-size: 14px;
            color: #343a40;
        }

        .custom-input-group input::placeholder {
            color: #adb5bd;
            font-size: 13px;
        }


        /* =========================================
   PASSWORD EYE BUTTON
========================================= */

        .password-toggle {
            flex: 0 0 48px;

            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0;
            margin: 0;

            border: none;
            outline: none;

            background: transparent;

            color: #6c757d;

            cursor: pointer;

            transition: color 0.2s ease,
                background-color 0.2s ease;
        }

        .password-toggle:hover {
            color: #007bff;
            background-color: #f8f9fa;
        }

        .password-toggle:focus {
            outline: none;
            box-shadow: none;
        }

        .password-toggle i {
            display: block;

            margin: 0;
            padding: 0;

            font-size: 15px;
            line-height: 1;
        }


        /* =========================================
   LOGIN BUTTON
========================================= */

        .login-btn {
            width: 100%;
            height: 48px;

            margin-top: 5px;

            border: none;
            border-radius: 9px;

            background: #007bff;
            color: #ffffff;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: all 0.2s ease;
        }

        .login-btn:hover {
            background: #0069d9;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.18);
        }

        .login-btn:active {
            transform: translateY(1px);
        }


        .custom-input-group .input-icon+input {
            margin-left: 0;
        }

        .custom-input-group button {
            margin-bottom: 0;
        }

        .forgot-password-btn {
            border: none;
            background: transparent;
            color: #64748b;
            /* Slate */
            font-size: 13px;
            cursor: pointer;
        }

        .forgot-password-btn span {
            color: #64748b;
            font-weight: 600;
        }

        .forgot-password-btn:hover {
            color: #475569;
        }

        .forgot-password-btn:hover span {
            color: #475569;
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

                <!-- Employee Number -->
                <div class="form-field flex">
                    <label for="employee_no">Employee No</label>

                    <div class="custom-input-group">
                        <div class="input-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>

                        <input
                            type="text"
                            name="employee_no"
                            id="employee_no"
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