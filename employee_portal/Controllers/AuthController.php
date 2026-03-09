<?php

require_once "../app/Models/User.php";

require_once "../app/Core/Controller.php";
require_once "../app/Core/Validator.php";
require_once "../app/Core/Helpers.php";
require_once "../app/Core/Mailer.php";
require_once "../app/Core/EmailView.php";


class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $_SESSION['old'] = $_POST;

            $validator = new Validator();

            $validator->validate($_POST, [
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if ($validator->fails()) {
                $_SESSION['error'] = reset($validator->errors());
                header("Location: index.php?url=login");
                exit;
            }

            $userModel = new User();
            $user = $userModel->login($_POST['email'], $_POST['password']);

            if ($user === 'inactive') {
                $_SESSION['error'] = "Your account has been deactivated. Please contact the administrator.";
                header("Location: index.php?url=login");
                exit;
            }

            if ($user === false) {
                $_SESSION['error'] = "Invalid email or password.";
                header("Location: index.php?url=login");
                exit;
            }

            unset($_SESSION['old']);
            $_SESSION['user'] = $user;
            $_SESSION['success'] = "Login successful!";
            header("Location: index.php?url=dashboard");
            exit;
        }

        $this->view('auth/login', [
            'title' => 'Login | SEMSYS'
        ]);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user']);
        $_SESSION['success'] = "You have successfully logged out.";
        session_regenerate_id(true);
        header("Location: index.php?url=home");
        exit;
    }

    public function sessionExpired()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        session_start();
        $_SESSION['error'] = "Your session has expired. Please login again.";

        header("Location: index.php?url=login");
        exit;
    }
}
