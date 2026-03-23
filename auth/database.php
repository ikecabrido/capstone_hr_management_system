<?php

class Database
{
    private static $instance = null;
    private PDO $conn;

    private string $host = "localhost";
    private string $db   = "hr_management";
    private string $user = "root";
    private string $pass = "";

    private function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            // Initialize required tables
            $this->initializeTables();
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    /**
     * Initialize required tables if they don't exist
     */
    public function initializeTables()
    {
        try {
            // Check if ld_archive table exists
            $stmt = $this->conn->query("SHOW TABLES LIKE 'ld_archive'");
            if ($stmt->rowCount() === 0) {
                // Create ld_archive table
                $this->conn->exec("
                    CREATE TABLE ld_archive (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        archive_type ENUM('course', 'program') NOT NULL,
                        original_id INT NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        content TEXT,
                        original_created_by INT,
                        archived_by INT NOT NULL,
                        archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        archive_reason VARCHAR(255),
                        archive_data JSON,
                        restored BOOLEAN DEFAULT FALSE,
                        restored_by INT NULL,
                        restored_at TIMESTAMP NULL,
                        FOREIGN KEY (archived_by) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (restored_by) REFERENCES users(id) ON DELETE SET NULL,
                        FOREIGN KEY (original_created_by) REFERENCES users(id) ON DELETE SET NULL,
                        INDEX (archive_type),
                        INDEX (archived_at),
                        INDEX (restored)
                    )
                ");
            }
        } catch (Exception $e) {
            // Table already exists or error occurred, continue anyway
        }
    }
}
