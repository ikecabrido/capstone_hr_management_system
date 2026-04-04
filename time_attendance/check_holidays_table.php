<?php
require_once "../../auth/database.php";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if ta_holidays table exists
    $result = $db->query("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hr_management' AND TABLE_NAME = 'ta_holidays'");
    $exists = $result->fetch();
    
    echo json_encode([
        'success' => true,
        'ta_holidays_exists' => (bool)$exists,
        'database' => 'hr_management',
        'message' => $exists ? 'Table exists' : 'Table does not exist - migration needed'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
