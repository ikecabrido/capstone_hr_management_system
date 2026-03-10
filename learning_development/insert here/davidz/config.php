<?php
// start session only if one hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration - adjust as needed for your environment
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hr_learning_dev');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// run a few lightweight migrations for older databases
// (these statements are safe to run multiple times)
try {
    $pdo->exec('ALTER TABLE career_paths ADD COLUMN IF NOT EXISTS prerequisites VARCHAR(255)');
    $pdo->exec('ALTER TABLE career_paths ADD COLUMN IF NOT EXISTS skills_required JSON');
    $pdo->exec('ALTER TABLE career_paths ADD COLUMN IF NOT EXISTS created_by INT');
    $pdo->exec('ALTER TABLE training_programs ADD COLUMN IF NOT EXISTS created_by INT');
    $pdo->exec('ALTER TABLE leadership_programs ADD COLUMN IF NOT EXISTS created_by INT');
    $pdo->exec('ALTER TABLE individual_development_plans ADD COLUMN IF NOT EXISTS created_by INT');
    $pdo->exec('ALTER TABLE compliance_trainings ADD COLUMN IF NOT EXISTS created_by INT');
} catch (Exception $e) {
    // ignore any errors during migration, column may already exist or engine may not support
}

// Simple helper: return current user role or null
function current_role() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function current_user_display() {
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest');
}

?>