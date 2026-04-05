<?php
/**
 * MedicineInventory Model
 * 
 * Manages medicine inventory data and operations
 */

require_once __DIR__ . "/../core/BaseModel.php";

class MedicineInventory extends BaseModel {
    protected $table_name = 'cm_medicine_inventory';
    protected $primary_key = 'medicine_id';
    
    /**
     * Get inventory statistics
     * @return array
     */
    public function getInventoryStats() {
        $stats = [];
        
        try {
            // Total medicines
            $stats['total_medicines'] = $this->count();
            
            // Available medicines
            $stats['available_medicines'] = $this->count(['status' => 'Available']);
            
            // Low stock medicines
            $stats['low_stock_medicines'] = $this->count(['status' => 'Low Stock']);
            
            // Out of stock medicines
            $stats['out_of_stock_medicines'] = $this->count(['status' => 'Out of Stock']);
            
            // Expired (Unavailable) medicines
            $stats['expired_medicines'] = $this->count(['status' => 'Unavailable']);
            
            // Expiring soon (within 30 days)
            $sql = "SELECT COUNT(*) as count FROM {$this->table_name} 
                    WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
            $result = $this->query($sql);
            $stats['expiring_soon'] = $result[0]['count'] ?? 0;
            
            // Total stock value
            $sql = "SELECT SUM(current_stock * unit_cost) as total_value 
                    FROM {$this->table_name} 
                    WHERE current_stock > 0";
            $result = $this->query($sql);
            $stats['total_stock_value'] = $result[0]['total_value'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error getting inventory stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Add stock to medicine
     * @param string $medicine_id - Medicine ID
     * @param int $quantity - Quantity to add
     * @param string $added_by - Who added the stock
     * @return bool
     */
    public function addStock($medicine_id, $quantity, $added_by) {
        $this->beginTransaction();
        
        try {
            // Get current stock
            $medicine = $this->readById($medicine_id);
            if (!$medicine) {
                throw new Exception("Medicine not found");
            }
            
            $new_stock = $medicine['current_stock'] + $quantity;
            
            // Update stock
            if (!$this->update($medicine_id, ['current_stock' => $new_stock])) {
                throw new Exception("Failed to update stock");
            }
            
            // Log the stock addition
            $log_id = 'LOG' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $log_sql = "INSERT INTO cm_medicine_usage_logs (log_id, medicine_id, usage_date, quantity_used, remaining_stock, purpose, used_by) 
                        VALUES (?, ?, CURDATE(), ?, ?, 'Stock Addition', ?)";
            
            $log_stmt = $this->db->prepare($log_sql);
            $log_values = [$log_id, $medicine_id, $quantity, $new_stock, $added_by];
            
            if (!$log_stmt->execute($log_values)) {
                throw new Exception("Failed to log stock addition");
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error adding stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deduct stock from medicine
     * @param string $medicine_id - Medicine ID
     * @param int $quantity - Quantity to deduct
     * @param string $purpose - Purpose of deduction
     * @param string $used_by - Who used the medicine
     * @return bool
     */
    public function deductStock($medicine_id, $quantity, $purpose, $used_by) {
        $this->beginTransaction();
        
        try {
            // Get current stock
            $medicine = $this->readById($medicine_id);
            if (!$medicine) {
                throw new Exception("Medicine not found");
            }
            
            if ($medicine['current_stock'] < $quantity) {
                throw new Exception("Insufficient stock");
            }
            
            $new_stock = $medicine['current_stock'] - $quantity;
            
            // Update stock
            if (!$this->update($medicine_id, ['current_stock' => $new_stock])) {
                throw new Exception("Failed to update stock");
            }
            
            // Log the stock deduction
            $log_id = 'LOG' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $log_sql = "INSERT INTO cm_medicine_usage_logs (log_id, medicine_id, usage_date, quantity_used, remaining_stock, purpose, used_by) 
                        VALUES (?, ?, CURDATE(), ?, ?, ?, ?)";
            
            $log_stmt = $this->db->prepare($log_sql);
            $log_values = [$log_id, $medicine_id, $quantity, $new_stock, $purpose, $used_by];
            
            if (!$log_stmt->execute($log_values)) {
                throw new Exception("Failed to log stock deduction");
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error deducting stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get medicines by category
     * @param string $category - Medicine category
     * @return array
     */
    public function getByCategory($category) {
        return $this->read(['category' => $category], 'medicine_name ASC');
    }
    
    /**
     * Get medicines by status
     * @param string $status - Medicine status
     * @return array
     */
    public function getByStatus($status) {
        return $this->read(['status' => $status], 'medicine_name ASC');
    }
    
    /**
     * Get low stock medicines
     * @return array
     */
    public function getLowStockMedicines() {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE current_stock <= reorder_level 
                AND status != 'Out of Stock'
                ORDER BY current_stock ASC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Get expired medicines
     * @return array
     */
    public function getExpiredMedicines() {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE expiry_date < CURDATE()
                ORDER BY expiry_date DESC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Get expiring soon medicines
     * @param int $days - Days to expiration
     * @return array
     */
    public function getExpiringSoon($days = 30) {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY expiry_date ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting expiring soon medicines: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search medicines
     * @param string $search - Search term
     * @return array
     */
    public function search($search) {
        $sql = "SELECT * FROM {$this->table_name} 
                WHERE medicine_name LIKE ? OR generic_name LIKE ? OR category LIKE ?
                ORDER BY medicine_name ASC";
        
        $search_term = "%{$search}%";
        $params = [$search_term, $search_term, $search_term];
        
        $result = $this->query($sql, $params);
        return $result ?: [];
    }
    
    /**
     * Get usage logs
     * @param string $medicine_id - Medicine ID (optional)
     * @param int $limit - Limit results
     * @return array
     */
    public function getUsageLogs($medicine_id = null, $limit = 50) {
        $sql = "SELECT mul.*, mi.medicine_name
                FROM cm_medicine_usage_logs mul
                INNER JOIN {$this->table_name} mi ON mul.medicine_id = mi.medicine_id";
        
        $params = [];
        
        if ($medicine_id) {
            $sql .= " WHERE mul.medicine_id = ?";
            $params[] = $medicine_id;
        }
        
        $sql .= " ORDER BY mul.usage_date DESC LIMIT ?";
        $params[] = $limit;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting usage logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get medicine categories
     * @return array
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM {$this->table_name} 
                WHERE category IS NOT NULL AND category != ''
                ORDER BY category ASC";
        
        $result = $this->query($sql);
        return $result ?: [];
    }
    
    /**
     * Update medicine status based on stock and expiry
     * @param string $medicine_id - Medicine ID
     * @return bool
     */
    public function updateStatus($medicine_id) {
        $sql = "UPDATE {$this->table_name} 
                SET status = CASE 
                    WHEN expiry_date < CURDATE() THEN 'Unavailable'
                    WHEN current_stock <= 0 THEN 'Out of Stock'
                    WHEN current_stock <= reorder_level THEN 'Low Stock'
                    ELSE 'Available'
                END
                WHERE medicine_id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$medicine_id]);
        } catch (PDOException $e) {
            error_log("Error updating medicine status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update all medicine statuses
     * @return bool
     */
    public function updateAllStatuses() {
        $sql = "UPDATE {$this->table_name} 
                SET status = CASE 
                    WHEN expiry_date < CURDATE() THEN 'Unavailable'
                    WHEN current_stock <= 0 THEN 'Out of Stock'
                    WHEN current_stock <= reorder_level THEN 'Low Stock'
                    ELSE 'Available'
                END";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating all medicine statuses: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate medicine data
     * @param array $data - Medicine data
     * @return array - Validation errors
     */
    public function validateMedicine($data) {
        $errors = [];
        
        // Required fields
        $required = ['medicine_name', 'current_stock', 'reorder_level'];
        $errors = array_merge($errors, $this->validateRequired($data, $required));
        
        // Stock validation
        if (!empty($data['current_stock']) && (!is_numeric($data['current_stock']) || $data['current_stock'] < 0)) {
            $errors['current_stock'] = 'Stock must be a positive number';
        }
        
        // Reorder level validation
        if (!empty($data['reorder_level']) && (!is_numeric($data['reorder_level']) || $data['reorder_level'] < 0)) {
            $errors['reorder_level'] = 'Reorder level must be a positive number';
        }
        
        // Cost validation
        if (!empty($data['unit_cost']) && (!is_numeric($data['unit_cost']) || $data['unit_cost'] < 0)) {
            $errors['unit_cost'] = 'Unit cost must be a positive number';
        }
        
        // Expiry date validation
        if (!empty($data['expiry_date'])) {
            $expiry_date = DateTime::createFromFormat('Y-m-d', $data['expiry_date']);
            if (!$expiry_date || $expiry_date <= new DateTime()) {
                $errors['expiry_date'] = 'Expiry date must be in the future';
            }
        }
        
        return $errors;
    }
    
    /**
     * Get stock alerts
     * @return array
     */
    public function getStockAlerts() {
        $alerts = [];
        
        try {
            // Low stock alerts
            $low_stock = $this->getLowStockMedicines();
            if (!empty($low_stock)) {
                $alerts['low_stock'] = $low_stock;
            }
            
            // Expired medicines
            $expired = $this->getExpiredMedicines();
            if (!empty($expired)) {
                $alerts['expired'] = $expired;
            }
            
            // Expiring soon
            $expiring_soon = $this->getExpiringSoon(30);
            if (!empty($expiring_soon)) {
                $alerts['expiring_soon'] = $expiring_soon;
            }
            
        } catch (Exception $e) {
            error_log("Error getting stock alerts: " . $e->getMessage());
        }
        
        return $alerts;
    }
    
    /**
     * Get all medicines
     * @return array
     */
    public function getAllMedicines() {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY medicine_name ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all medicines: " . $e->getMessage());
            return [];
        }
    }
}
?>
