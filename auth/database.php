<?php

require_once dirname(dirname(__FILE__)) . '/config.php';

class Database
{
    private static ?Database $instance = null;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    private function __construct()
    {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log detailed error for debugging
            $error_log = "Database Error Details:\n";
            $error_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
            $error_log .= "Host: " . $this->host . "\n";
            $error_log .= "Database: " . $this->dbname . "\n";
            $error_log .= "Username: " . $this->username . "\n";
            $error_log .= "Error Code: " . $e->getCode() . "\n";
            $error_log .= "Error Message: " . $e->getMessage() . "\n";
            $error_log .= "Client IP: " . $_SERVER['REMOTE_ADDR'] ?? 'Unknown' . "\n";
            error_log($error_log);
            
            die("Server returned database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
