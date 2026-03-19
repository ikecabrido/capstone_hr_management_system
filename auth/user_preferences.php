<?php
/**
 * User Preferences Helper
 * Handles user-specific preferences like theme settings
 */

require_once __DIR__ . '/database.php';

/**
 * Save user theme preference to database
 * 
 * @param int $userId The user ID
 * @param string $theme Theme value ('light' or 'dark')
 * @return array Response with status and message
 */
function saveUserThemePreference($userId, $theme) {
    // Validate theme value
    if (!in_array($theme, ['light', 'dark'])) {
        return [
            'success' => false,
            'message' => 'Invalid theme value. Must be "light" or "dark".'
        ];
    }
    
    // Validate user ID
    if (!is_numeric($userId) || $userId <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid user ID.'
        ];
    }
    
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        // Check if theme column exists in users table, if not add it
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'theme'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE users ADD COLUMN theme VARCHAR(10) DEFAULT 'light' AFTER password");
        }
        
        // Use REPLACE INTO or INSERT...ON DUPLICATE KEY UPDATE
        $stmt = $pdo->prepare("
            INSERT INTO users (id, theme) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE theme = VALUES(theme)
        ");
        $stmt->execute([$userId, $theme]);
        
        return [
            'success' => true,
            'message' => 'Theme preference saved successfully.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error saving theme preference: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to save theme preference. Please try again.'
        ];
    }
}

/**
 * Get user theme preference from database
 * 
 * @param int $userId The user ID
 * @return string|null Theme value ('light', 'dark') or null if not set
 */
function getUserThemePreference($userId) {
    if (!is_numeric($userId) || $userId <= 0) {
        return null;
    }
    
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        $stmt = $pdo->prepare("SELECT theme FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['theme'] ?? null;
        
    } catch (PDOException $e) {
        error_log("Error getting theme preference: " . $e->getMessage());
        return null;
    }
}

/**
 * Save multiple user preferences at once
 * 
 * @param int $userId The user ID
 * @param array $preferences Key-value pairs of preferences
 * @return array Response with status and message
 */
function saveUserPreferences($userId, $preferences) {
    // Validate user ID
    if (!is_numeric($userId) || $userId <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid user ID.'
        ];
    }
    
    // Validate preferences is an array
    if (!is_array($preferences) || empty($preferences)) {
        return [
            'success' => false,
            'message' => 'Preferences must be a non-empty array.'
        ];
    }
    
    // Only allow certain preference keys for security
    $allowedPreferences = ['theme', 'language', 'timezone', 'notifications'];
    $filteredPreferences = array_intersect_key($preferences, array_flip($allowedPreferences));
    
    if (empty($filteredPreferences)) {
        return [
            'success' => false,
            'message' => 'No valid preferences provided.'
        ];
    }
    
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        // Build the update query dynamically
        $sets = [];
        $values = [];
        
        foreach ($filteredPreferences as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = htmlspecialchars(strip_tags($value));
        }
        
        $values[] = $userId;
        
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        
        return [
            'success' => true,
            'message' => 'Preferences saved successfully.'
        ];
        
    } catch (PDOException $e) {
        error_log("Error saving preferences: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to save preferences. Please try again.'
        ];
    }
}