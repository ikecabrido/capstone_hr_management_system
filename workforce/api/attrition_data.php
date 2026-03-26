<?php
/**
 * Attrition Data API
 * Returns attrition and turnover data
 */

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Analytics.php';

try {
    $analytics = new Analytics();
    
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    
    $data = [
        'attrition_data' => $analytics->getAttritionData($year),
        'attrition_rate' => $analytics->getAttritionRate($year),
        'separated_employees' => $analytics->getSeparatedEmployees()
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

?>
