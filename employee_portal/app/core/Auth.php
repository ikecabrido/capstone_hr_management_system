<?php

require_once __DIR__ . '/Session.php';

class Auth
{
    public static function isAuthenticated()
    {
        Session::start();

        if (!is_null(Session::get('user_id'))) {
            return true;
        }

        if (isset($_SESSION['user']) 
            && is_array($_SESSION['user']) 
            && isset($_SESSION['user']['id'])) 
        {
            $user = $_SESSION['user'];

            Session::set('user_id', $user['id']);
            Session::set('username', $user['username'] ?? null);
            Session::set('role', $user['role'] ?? null);
            Session::set('full_name', $user['name'] ?? null);

            return true;
        }

        return false;
    }

    public static function requireAuth($redirect = 'index.php?url=auth-index')
    {
        if (!self::isAuthenticated()) {
            header("Location: $redirect");
            exit;
        }
    }

    public static function logout()
    {
        Session::start();

        session_unset();
        session_destroy();

        header("Location: index.php?url=auth-index");
        exit;
    }

    public static function userId()
    {
        return Session::get('user_id');
    }

    public static function role()
    {
        return Session::get('role');
    }

    public static function hasRole($role)
    {
        return self::role() === $role;
    }
}