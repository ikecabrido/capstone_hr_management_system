<?php

class Database
{
    private $host = "localhost";
    private $db_name = "time_and_attendance";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set MySQL connection timezone to Philippines (UTC+8)
            $this->conn->exec("SET SESSION time_zone = '+08:00'");

        } catch (PDOException $exception) {
            die("Database connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
