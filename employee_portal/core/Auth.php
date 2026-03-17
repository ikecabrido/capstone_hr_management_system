<?php

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function logout()
    {
        session_destroy();
        header("Location: /semsys/public/");
        exit;
    }

    public static function isAdmin()
    {
        return self::check() && !empty($_SESSION['user']['isAdmin']);
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            $_SESSION['error'] = "Please login first.";
            header("Location: index.php?url=login");
            exit;
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            $_SESSION['error'] = "Unauthorized access.";
            header("Location: index.php?url=dashboard");
            exit;
        }
    }
}
