<?php
// start session only if one hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Normalize session keys to work with different auth systems (old app uses $_SESSION['user'] vs. legacy code using $_SESSION['username'] / $_SESSION['role'])
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    if (isset($_SESSION['user']['username'])) {
        $_SESSION['username'] = $_SESSION['user']['username'];
    }
    if (isset($_SESSION['user']['name'])) {
        $_SESSION['full_name'] = $_SESSION['user']['name'];
    }
    if (isset($_SESSION['user']['role'])) {
        $_SESSION['role'] = $_SESSION['user']['role'];
    }
}

// Database configuration - adjust as needed for your environment
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hr_management');
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

// Simple helper: return current user role or null (normalized to lowercase/trimmed)
function current_role() {
    if (!isset($_SESSION['role']) || !is_string($_SESSION['role'])) {
        return null;
    }
    return strtolower(trim($_SESSION['role']));
}

function current_user_display() {
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest');
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['username']) && !empty($_SESSION['username']);
}

// Check if user is authorized for CRUD operations (admin/manager/learning)
function can_manage() {
    if (!is_logged_in()) {
        return false;
    }

    $role = current_role();
    if (!$role) {
        return false;
    }

    // Support role variations (e.g., "Learning", "learning_admin", "learning-manager")
    if (in_array($role, ['admin', 'manager', 'learning'], true)) {
        return true;
    }

    return str_contains($role, 'learn');
}

// Get current user ID
function get_current_user_id() {
    static $userId = null;
    if ($userId === null) {
        $username = $_SESSION['username'] ?? null;
        if ($username) {
            try {
                global $pdo;
                $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $userId = $user ? $user['id'] : null;
            } catch (Exception $e) {
                error_log('Error getting user ID: ' . $e->getMessage());
                $userId = false;
            }
        } else {
            $userId = false;
        }
    }
    return $userId ?: null;
}

?>