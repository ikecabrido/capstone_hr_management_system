<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Check if config exists
    if (!defined('DB_NAME')) {
        require_once __DIR__ . '/config/config.php';
    }
    
    echo json_encode([
        'step' => 'Config loaded',
        'db_name' => DB_NAME,
        'db_host' => DB_HOST,
        'db_user' => DB_USER
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
