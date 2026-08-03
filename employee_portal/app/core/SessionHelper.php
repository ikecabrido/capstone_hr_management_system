<?php

class SessionHelper
{
    const TIMEOUT = 900; // 15 minutes

    public static function checkTimeout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            isset($_SESSION['last_activity']) &&
            (time() - $_SESSION['last_activity']) >= self::TIMEOUT
        ) {
            $isAdmin = !empty($_SESSION['user']['is_admin']);

            session_unset();
            session_destroy();

            Helper::redirect(
                $isAdmin
                    ? 'index.php?url=auth-admin-logout'
                    : 'index.php?url=auth-logout'
            );

            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}
