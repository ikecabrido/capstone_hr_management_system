<?php

class Database
{
    private $host;
    private $db_name = "hr_management";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct()
    {
        // Determine the correct host based on where the request came from
        $this->host = $this->getServerHost();
    }

    private function getServerHost()
    {
        // If accessing via IP address, use that IP
        if (!empty($_SERVER['HTTP_HOST'])) {
            $host = explode(':', $_SERVER['HTTP_HOST'])[0]; // Remove port if present
            
            // Check if it's already an IP address
            if (filter_var($host, FILTER_VALIDATE_IP)) {
                return $host;
            }
            
            // Try to resolve hostname to IP
            $ip = @gethostbyname($host);
            if ($ip && $ip !== $host && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        
        // Try to get server's own IP address
        if (!empty($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }
        
        // Try to resolve server hostname to IP
        $hostname = @gethostname();
        if ($hostname) {
            $ip = @gethostbyname($hostname);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        
        // Fallback to localhost
        return 'localhost';
    }

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
            throw new Exception("Database connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
