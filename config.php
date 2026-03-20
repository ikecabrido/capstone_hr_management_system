<?php
/**
 * Application Configuration
 * 
 * This file handles environment-specific configuration.
 * 
 * To use a different host when accessing from other devices:
 * 1. Change 'localhost' to your server IP address (e.g., '192.168.x.x')
 * 2. Ensure MySQL user 'root' has permissions from that IP
 */

// Database Configuration
// For local access: use 'localhost'
// For remote access: use your server IP address (e.g., '192.168.1.100')
define('DB_HOST', 'localhost');
define('DB_NAME', 'hr_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Settings
define('APP_NAME', 'Human Resource Management System');
define('APP_DEBUG', false); // Set to true for development to see detailed errors

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_UPLOAD_SIZE', 10485760); // 10MB in bytes

// Timezone
date_default_timezone_set('Asia/Manila');
