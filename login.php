<?php

require_once "auth/auth.php";

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($auth->login($username, $password)) {

        header("Location: router.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: login_form.php");
        exit;
    }
}
