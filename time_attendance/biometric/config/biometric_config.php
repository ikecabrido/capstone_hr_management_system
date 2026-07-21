<?php
/**
 * Biometric Configuration
 * Centralized configuration for all biometric device integrations
 */

// Device Settings
define('BIOMETRIC_DEVICE_TYPE', 'ngteco'); // Options: 'ngteco', 'zkteco', 'morpho'
define('BIOMETRIC_DEVICE_IP', '192.168.1.100');
define('BIOMETRIC_DEVICE_PORT', 4370);
define('BIOMETRIC_DEVICE_TIMEOUT', 10); // seconds

// Sync Settings
define('BIOMETRIC_SYNC_INTERVAL', 600); // seconds (10 minutes)
define('BIOMETRIC_SYNC_BATCH_SIZE', 100); // records per sync
define('BIOMETRIC_MAX_SYNC_RETRIES', 3);
define('BIOMETRIC_RETRY_DELAY', 30); // seconds

// Matching Settings
define('BIOMETRIC_MIN_CONFIDENCE', 80); // 0-100 score
define('BIOMETRIC_DUPLICATE_WINDOW', 300); // seconds (5 minutes)
define('BIOMETRIC_MATCH_THRESHOLD', 80); // percentage

// Enrollment Settings
define('BIOMETRIC_MAX_RETRY_ATTEMPTS', 3);
define('BIOMETRIC_ENROLLMENT_TIMEOUT', 300); // seconds
define('BIOMETRIC_TEMPLATE_STORAGE', 'database'); // 'database' or 'device_only'

// Quality Settings
define('BIOMETRIC_MIN_QUALITY_SCORE', 70); // 0-100
define('BIOMETRIC_QUALITY_CHECK_ENABLED', true);

// Logging Settings
define('BIOMETRIC_LOG_FILE', __DIR__ . '/../logs/biometric_sync.log');
define('BIOMETRIC_DEBUG_MODE', true);
define('BIOMETRIC_LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Security Settings
define('BIOMETRIC_ENCRYPT_TEMPLATES', true);
define('BIOMETRIC_AUDIT_ALL_OPERATIONS', true);
define('BIOMETRIC_ALLOWED_ROLES', ['time_admin', 'hr_admin', 'admin']);

// API Settings
define('BIOMETRIC_API_TIMEOUT', 30); // seconds
define('BIOMETRIC_API_KEY_REQUIRED', false); // Set to true if device API requires auth
define('BIOMETRIC_API_KEY', ''); // Add your API key here
define('BIOMETRIC_API_SECRET', ''); // Add your API secret here

// Database Connection (Uses existing connection)
// require_once('../auth/database.php');

// Device Adapter Mapping
$DEVICE_ADAPTERS = [
    'ngteco' => 'NGTecoAdapter',
    'zkteco' => 'ZKTecoAdapter',
    'morpho' => 'MorphoAdapter',
    'hikvision' => 'HikvisionAdapter',
];

// Enrollment Status
$ENROLLMENT_STATUSES = [
    'pending' => 'Awaiting enrollment',
    'enrolled' => 'Active and verified',
    'rejected' => 'Enrollment failed',
    'expired' => 'Needs re-enrollment',
];

// Verification Status
$VERIFICATION_STATUSES = [
    'success' => 'Successfully verified',
    'failed' => 'Failed to verify',
    'duplicate' => 'Duplicate record detected',
    'after_hours' => 'Recorded after working hours',
];

// Exception Severity Levels
$EXCEPTION_SEVERITY = [
    'info' => 'Informational',
    'warning' => 'Warning (needs attention)',
    'critical' => 'Critical (immediate action required)',
];

// Finger Positions (for fingerprint systems)
$FINGER_POSITIONS = [
    'left_thumb' => 'Left Thumb',
    'left_index' => 'Left Index',
    'left_middle' => 'Left Middle',
    'left_ring' => 'Left Ring',
    'left_pinky' => 'Left Pinky',
    'right_thumb' => 'Right Thumb',
    'right_index' => 'Right Index',
    'right_middle' => 'Right Middle',
    'right_ring' => 'Right Ring',
    'right_pinky' => 'Right Pinky',
];

// Log Levels
$LOG_LEVELS = [
    'DEBUG' => 0,
    'INFO' => 1,
    'WARNING' => 2,
    'ERROR' => 3,
];

/**
 * Get device adapter class name based on device type
 */
function getBiometricDeviceAdapter($device_type = null) {
    global $DEVICE_ADAPTERS;
    $type = $device_type ?? BIOMETRIC_DEVICE_TYPE;
    return $DEVICE_ADAPTERS[$type] ?? 'GenericBiometricAdapter';
}

/**
 * Log biometric operation
 */
function logBiometric($level, $message, $data = []) {
    global $LOG_LEVELS;
    
    if (!BIOMETRIC_DEBUG_MODE) return;
    
    $min_level = $LOG_LEVELS[BIOMETRIC_LOG_LEVEL];
    $current_level = $LOG_LEVELS[$level] ?? 1;
    
    if ($current_level < $min_level) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message";
    
    if (!empty($data)) {
        $log_entry .= " | " . json_encode($data);
    }
    
    error_log($log_entry . PHP_EOL, 3, BIOMETRIC_LOG_FILE);
}

// Initialize log file
if (!file_exists(dirname(BIOMETRIC_LOG_FILE))) {
    mkdir(dirname(BIOMETRIC_LOG_FILE), 0755, true);
}

logBiometric('INFO', 'Biometric configuration loaded', [
    'device_type' => BIOMETRIC_DEVICE_TYPE,
    'device_ip' => BIOMETRIC_DEVICE_IP,
    'sync_interval' => BIOMETRIC_SYNC_INTERVAL,
]);
