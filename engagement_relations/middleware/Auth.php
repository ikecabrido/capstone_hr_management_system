<?php

if (!class_exists('Auth')) {
    class Auth
    {
    public static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getBearerToken()
    {
        // Works with Apache + FastCGI
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim(str_ireplace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
        }
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim(str_ireplace('Bearer', '', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']));
        }
        return null;
    }

    public static function requireAuth()
    {
        self::startSession();

        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        $bearer = self::getBearerToken();
        if (!empty($bearer) && isset($_SESSION['token']) && hash_equals($_SESSION['token'], $bearer) && isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'message' => 'Please login to continue.']);
        exit;
    }

    public static function requirePermission($module, $action)
    {
        // Simple permission stub. Expand as needed.
        self::startSession();

        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized', 'message' => 'Please login to continue.']);
            exit;
        }

        // For now, allow all authenticated users. Implement role rules here.
        return true;
    }

    public static function check()
    {
        self::startSession();
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized', 'message' => 'Please login to continue.']);
            exit;
        }
        return true;
    }

    public static function isAdmin()
    {
        self::startSession();
        return (isset($_SESSION['user']['role']) && strtolower($_SESSION['user']['role']) === 'admin');
    }

    public static function isHRManager()
    {
        self::startSession();
        return (isset($_SESSION['user']['role']) && strtolower($_SESSION['user']['role']) === 'hr_manager');
    }

    public static function isEmployee()
    {
        self::startSession();
        return (isset($_SESSION['user']['role']) && strtolower($_SESSION['user']['role']) === 'employee');
    }
}
}
