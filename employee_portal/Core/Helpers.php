<?php

class Helpers
{
    public static function randomPassword($length = 10)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$';
        return substr(str_shuffle($chars), 0, $length);
    }

    function requireAdmin()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: index.php?url=login");
            exit;
        }

        if ($_SESSION['user']['isAdmin'] != 1) {
            $_SESSION['error'] = "Unauthorized access.";
            header("Location: index.php?url=dashboard");
            exit;
        }
    }
}
