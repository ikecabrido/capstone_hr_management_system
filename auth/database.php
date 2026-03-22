<?php

class Database
{
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db   = "hr_management";
    private $user = "root";
    private $pass = "";

    private function __construct()
    {
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
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db}",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
