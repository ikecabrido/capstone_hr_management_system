<?php

namespace HRManagement\Database;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * 
 * Handles PDO connection and provides singleton instance for database access
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private string $host = '127.0.0.1';
    private string $db = 'hr_management';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get singleton instance of Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish PDO connection
     */
    private function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get PDO instance
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared statement
     */
    public function execute(string $query, array $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException('Query execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch one record
     */
    public function fetchOne(string $query, array $params = []): ?array
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Fetch all records
     */
    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    /**
     * Commit transaction
     */
    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    /**
     * Rollback transaction
     */
    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
