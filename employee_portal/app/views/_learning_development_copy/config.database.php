<?php

/**
 * Database Configuration
 * 
 * This file is imported by Database.php
 * Modify credentials as needed for your environment
 */

return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'hr_management',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    
    // Connection options
    'options' => [
        'errorMode' => 'exception',
        'defaultFetchMode' => 'assoc',
        'emulatePrepares' => false,
    ],
    
    // Pool settings (for future connection pooling)
    'pool' => [
        'min' => 1,
        'max' => 5,
    ],
];
