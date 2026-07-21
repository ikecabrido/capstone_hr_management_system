<?php

class Database
{
    private $host = "localhost";
    private $db_name = "hr-management";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct()
    {
        // Connection uses predefined localhost
    }

    public function getConnection(): PDO
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET SESSION time_zone = '+08:00'");

            return $this->conn;

        } catch (PDOException $exception) {
            throw new Exception("Database connection error: " . $exception->getMessage());
        }
    }
}
