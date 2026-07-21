<?php
/**
 * CRON JOB: Detect Absences (No check-in) - CONTINUOUS HOURLY
 * 
 * Run every hour from 6 AM to 8 PM (flexible schedules):
 * 0 6-20 * * * php detect_absences.php
 * 
 * For every 30 minutes:
 * 30 6-20 * * * php detect_absences.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../app/helpers/EnhancedAbsenceDetector.php';

try {
    $detector = new \App\Helpers\EnhancedAbsenceDetector();
    $result = $detector->detectAndMarkAbsenceToday();
    
    // Log results
    $logMessage = date('Y-m-d H:i:s') . " | Absence Detection Run (Hourly)\n";
    $logMessage .= "Status: " . $result['status'] . "\n";
    
    if ($result['status'] === 'completed') {
        $logMessage .= "Absent employees marked: " . $result['absence_count'] . "\n";
        if (!empty($result['results'])) {
            foreach ($result['results'] as $emp) {
                $logMessage .= "  - {$emp['name']} ({$emp['department']}): No check-in detected\n";
            }
        }
    } else {
        $logMessage .= "Reason: " . $result['reason'] . "\n";
    }
    
    $logMessage .= "---\n";
    
    @file_put_contents(__DIR__ . '/../logs/absence_detection.log', $logMessage, FILE_APPEND);
    
    exit(0);
    
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " | ERROR: " . $e->getMessage() . "\n";
    @file_put_contents(__DIR__ . '/../logs/absence_detection_error.log', $errorMessage, FILE_APPEND);
    exit(1);
}
?>
