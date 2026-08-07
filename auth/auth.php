<?php

require_once "user.php";
require_once "database.php";

class Auth
{

    private $userModel;
    private $db;
    private ?string $lastBlockedUntil = null;
    private int $maxFailedAttempts = 3;
    private int $failedWindowMinutes = 10;
    // Reduce block duration to 1 minute to match request
    private int $blockMinutes = 1;

    // Session inactivity timeout (minutes)
    private int $sessionTimeoutMinutes = 10; // auto-logout after 10 minutes inactivity


    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getInstance()->getConnection();
        $this->createLoginAttemptsTable();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password)
    {
        $ipAddress = $this->getClientIp();

        $user = $this->userModel->findByUsername($username);

        $success = $user && password_verify($password, $user['password']);
        $this->recordLoginAttempt($username, $ipAddress, $success);

        if ($success) {
            $employeeId = $user['employee_id'] ?? null;
            if (empty($employeeId) && !empty($user['id']) && is_numeric($user['id'])) {
                $employeeId = 'EMP' . str_pad((string)$user['id'], 3, '0', STR_PAD_LEFT);
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'employee_id' => $employeeId,
                'username' => $user['username'],
                'name' => $user['full_name'],
                'role' => $user['role'],
                'theme' => $user['theme'] ?? 'light'
            ];

            // Track last activity for inactivity logout
            $_SESSION['last_activity'] = time();

            // Token support for API and cURL fallback
            if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
                $_SESSION['token'] = bin2hex(random_bytes(16));
            }

            return true;
        }

        return false;
    }

    public function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($forwarded[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function getLoginBlockMessage(string $username, string $ipAddress): ?string
    {
        $activeBlock = $this->getActiveBlock($username, $ipAddress);

        if ($activeBlock) {
            return "Too many failed login attempts. Please try again after {$activeBlock}.";
        }

        return null;
    }

    private function getActiveBlock(string $username, string $ipAddress): ?string
    {
        $sql = "
            SELECT MAX(blocked_until) AS blocked_until
            FROM login_attempts
            WHERE blocked_until IS NOT NULL
              AND blocked_until > NOW()
              AND (username = :username OR ip_address = :ip_address)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':ip_address' => $ipAddress
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['blocked_until'])) {
            return (new DateTime($row['blocked_until']))->format('g:i a');
        }

        return null;
    }

    public function getActiveBlockRemainingSeconds(string $username, string $ipAddress): ?int
    {
        $sql = "
            SELECT MAX(blocked_until) AS blocked_until
            FROM login_attempts
            WHERE blocked_until IS NOT NULL
              AND blocked_until > NOW()
              AND (username = :username OR ip_address = :ip_address)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':ip_address' => $ipAddress
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['blocked_until'])) {
            $blockedUntil = new DateTime($row['blocked_until']);
            $seconds = $blockedUntil->getTimestamp() - (new DateTime())->getTimestamp();
            return $seconds > 0 ? $seconds : 0;
        }

        return null;
    }

    public function getLastBlockedSeconds(): ?int
    {
        if (empty($this->lastBlockedUntil)) {
            return null;
        }

        $blockedUntil = new DateTime($this->lastBlockedUntil);
        $seconds = $blockedUntil->getTimestamp() - (new DateTime())->getTimestamp();
        return $seconds > 0 ? $seconds : 0;
    }

    private function createLoginAttemptsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(200) DEFAULT NULL,
                ip_address VARCHAR(45) NOT NULL,
                success TINYINT(1) NOT NULL DEFAULT 0,
                attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                blocked_until DATETIME DEFAULT NULL,
                INDEX idx_username_attempts (username),
                INDEX idx_ip_attempts (ip_address),
                INDEX idx_blocked_until (blocked_until)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->exec($sql);
    }

    private function getRecentFailedAttemptCounts(string $username, string $ipAddress): array
    {
        $window = (new DateTime())->modify("-{$this->failedWindowMinutes} minutes")->format('Y-m-d H:i:s');

        $sql = "
            SELECT
                SUM(CASE WHEN success = 0 AND username = :username THEN 1 ELSE 0 END) AS username_failures,
                SUM(CASE WHEN success = 0 AND ip_address = :ip_address THEN 1 ELSE 0 END) AS ip_failures
            FROM login_attempts
            WHERE attempted_at >= :window_start
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':ip_address' => $ipAddress,
            ':window_start' => $window
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'username_failures' => (int)($row['username_failures'] ?? 0),
            'ip_failures' => (int)($row['ip_failures'] ?? 0)
        ];
    }

    private function recordLoginAttempt(string $username, string $ipAddress, bool $success): ?string
    {
        $blockedUntil = null;
        if (!$success) {
            $failureCounts = $this->getRecentFailedAttemptCounts($username, $ipAddress);
            $usernameFailures = $failureCounts['username_failures'] + 1;
            $ipFailures = $failureCounts['ip_failures'] + 1;

            if ($usernameFailures >= $this->maxFailedAttempts || $ipFailures >= $this->maxFailedAttempts) {
                $blockedUntil = (new DateTime())->modify("+{$this->blockMinutes} minutes")->format('Y-m-d H:i:s');
            }
        }

        $sql = "
            INSERT INTO login_attempts (username, ip_address, success, attempted_at, blocked_until)
            VALUES (:username, :ip_address, :success, NOW(), :blocked_until)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':ip_address' => $ipAddress,
            ':success' => $success ? 1 : 0,
            ':blocked_until' => $blockedUntil
        ]);

        $this->lastBlockedUntil = $blockedUntil;
        return $blockedUntil;
    }

    public function getLastBlockedUntil(): ?string
    {
        return $this->lastBlockedUntil;
    }

    // Update last activity timestamp to now
    public function refreshSessionActivity(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }

    // Enforce session inactivity timeout; call this from auth_check or a common include
    public function enforceSessionTimeout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            // No authenticated user; nothing to enforce here
            return;
        }

        $last = $_SESSION['last_activity'] ?? time();
        $inactiveSeconds = time() - (int)$last;
        $timeoutSeconds = $this->sessionTimeoutMinutes * 60;

        if ($inactiveSeconds > $timeoutSeconds) {
            // Destroy session and force logout
            session_unset();
            session_destroy();

            // Redirect to the login page using a relative path based on the current request
            header('Location: ' . $this->getLoginFormPath());
            exit;
        }

        // Otherwise refresh activity
        $_SESSION['last_activity'] = time();
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public function role()
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public function logout()
    {
        session_destroy();
    }

    public function getLoginFormPath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $projectSegment = basename(dirname(__DIR__));
        $segments = array_values(array_filter(explode('/', $scriptName), 'strlen'));
        $projectIndex = array_search($projectSegment, $segments, true);

        if ($projectIndex !== false) {
            $directories = array_slice($segments, $projectIndex + 1, -1);
            $up = str_repeat('../', count($directories));
            return $up . 'login_form.php';
        }

        return 'login_form.php';
    }
}
