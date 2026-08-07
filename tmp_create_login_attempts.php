<?php
require_once 'auth/auth.php';

try {
    $auth = new Auth();
    echo "Auth initialized and login_attempts table creation attempted.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
