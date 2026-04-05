<?php
/**
 * BaseModel Class
 * 
 * Abstract base class for all models in the clinic management system
 * Provides common database operations and functionality
 */

abstract class BaseModel {
    protected $db;
    protected $table_name;
    protected $primary_key = 'id';
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Read records from the table
     * @param array $conditions - WHERE conditions
     * @param string $order_by - ORDER BY clause
     * @param int $limit - LIMIT clause
     * @return array
     */
    public function read($conditions = [], $order_by = '', $limit = '') {
        $sql = "SELECT * FROM {$this->table_name}";
        $params = [];
        
        // Add WHERE conditions
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $placeholders = implode(', ', array_fill(0, count($value), '?'));
                    $where_clauses[] = "$field IN ($placeholders)";
                    $params = array_merge($params, $value);
                } else {
                    $where_clauses[] = "$field = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        // Add ORDER BY
        if (!empty($order_by)) {
            $sql .= " ORDER BY $order_by";
        }
        
        // Add LIMIT
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Read error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Read a single record by ID
     * @param string $id - Primary key value
     * @return array|null
     */
    public function readById($id) {
        $sql = "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Read by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new record
     * @param array $data - Associative array of field => value pairs
     * @return bool
     */
    public function create($data) {
        // Remove primary key only if it's auto-generated (empty or null)
        if (isset($data[$this->primary_key]) && (empty($data[$this->primary_key]) || $data[$this->primary_key] === '')) {
            unset($data[$this->primary_key]);
        }
        
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_values($data);
        
        $sql = "INSERT INTO {$this->table_name} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Create error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Values: " . print_r($values, true));
            return false;
        }
    }
    
    /**
     * Update a record
     * @param string $id - Primary key value
     * @param array $data - Associative array of field => value pairs
     * @return bool
     */
    public function update($id, $data) {
        // Remove primary key from update data
        unset($data[$this->primary_key]);
        
        if (empty($data)) {
            return false;
        }
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return "$field = ?";
        }, $fields);
        $values = array_values($data);
        $values[] = $id; // Add ID for WHERE clause
        
        $sql = "UPDATE {$this->table_name} 
                SET " . implode(', ', $placeholders) . 
                " WHERE {$this->primary_key} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     * @param string $id - Primary key value
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records
     * @param array $conditions - WHERE conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table_name}";
        $params = [];
        
        // Add WHERE conditions
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $field => $value) {
                $where_clauses[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Execute a custom query
     * @param string $sql - SQL query
     * @param array $params - Query parameters
     * @return array|bool
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Determine if it's a SELECT query
            $stmt_type = strtoupper(substr(trim($sql), 0, 6));
            if ($stmt_type === 'SELECT') {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the last inserted ID
     * @return string
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->db->rollback();
    }
    
    /**
     * Validate required fields
     * @param array $data - Data to validate
     * @param array $required_fields - Required field names
     * @return array - Array of errors (empty if valid)
     */
    public function validateRequired($data, $required_fields) {
        $errors = [];
        
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize input data
     * @param array $data - Data to sanitize
     * @return array - Sanitized data
     */
    public function sanitize($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Get table name
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }
    
    /**
     * Get primary key
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primary_key;
    }
}
?>
