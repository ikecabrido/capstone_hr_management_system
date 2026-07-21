<?php
/**
 * BiometricModule
 * Lightweight biometric registration and device management for the time attendance system.
 */
class BiometricModule
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function ensureTables()
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS biometric_devices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_name VARCHAR(100) NOT NULL,
                device_type VARCHAR(50) NOT NULL DEFAULT 'fingerprint',
                manufacturer VARCHAR(100) DEFAULT NULL,
                model VARCHAR(100) DEFAULT NULL,
                serial_number VARCHAR(100) DEFAULT NULL,
                ip_address VARCHAR(100) DEFAULT NULL,
                port INT DEFAULT 4370,
                mac_address VARCHAR(100) DEFAULT NULL,
                location VARCHAR(150) DEFAULT NULL,
                status VARCHAR(30) DEFAULT 'active',
                last_sync DATETIME DEFAULT NULL,
                capacity INT DEFAULT 500,
                enrolled_count INT DEFAULT 0,
                firmware_version VARCHAR(50) DEFAULT NULL,
                notes TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS biometric_enrollments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                device_id INT NOT NULL,
                biometric_id VARCHAR(100) DEFAULT NULL,
                enrollment_type VARCHAR(50) DEFAULT 'fingerprint',
                finger_position VARCHAR(50) DEFAULT NULL,
                quality_score DECIMAL(5,2) DEFAULT 0.00,
                enrollment_status VARCHAR(30) DEFAULT 'pending',
                enrollment_date DATETIME DEFAULT NULL,
                verification_count INT DEFAULT 0,
                failed_attempts INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                notes TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS biometric_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_id INT DEFAULT NULL,
                employee_id INT DEFAULT NULL,
                biometric_id VARCHAR(100) DEFAULT NULL,
                log_datetime DATETIME DEFAULT NULL,
                punch_type VARCHAR(20) DEFAULT 'in',
                quality_score DECIMAL(5,2) DEFAULT 0.00,
                raw_data TEXT DEFAULT NULL,
                is_matched TINYINT(1) DEFAULT 0,
                match_confidence DECIMAL(5,2) DEFAULT 0.00,
                status VARCHAR(30) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        foreach ($queries as $query) {
            $this->db->exec($query);
        }

        return true;
    }

    public function createDevice(array $data)
    {
        $sql = "INSERT INTO biometric_devices (
                    device_name, device_type, manufacturer, model, serial_number, ip_address, port, mac_address, location, status, capacity, firmware_version, notes
                ) VALUES (
                    :device_name, :device_type, :manufacturer, :model, :serial_number, :ip_address, :port, :mac_address, :location, :status, :capacity, :firmware_version, :notes
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':device_name' => $data['device_name'] ?? 'NGTeco Device',
            ':device_type' => $data['device_type'] ?? 'fingerprint',
            ':manufacturer' => $data['manufacturer'] ?? 'NGTeco',
            ':model' => $data['model'] ?? 'Unknown',
            ':serial_number' => $data['serial_number'] ?? null,
            ':ip_address' => $data['ip_address'] ?? null,
            ':port' => !empty($data['port']) ? (int) $data['port'] : 4370,
            ':mac_address' => $data['mac_address'] ?? null,
            ':location' => $data['location'] ?? 'Main Entrance',
            ':status' => $data['status'] ?? 'active',
            ':capacity' => !empty($data['capacity']) ? (int) $data['capacity'] : 500,
            ':firmware_version' => $data['firmware_version'] ?? null,
            ':notes' => $data['notes'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getDevices()
    {
        $stmt = $this->db->prepare("SELECT * FROM biometric_devices ORDER BY location ASC, device_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEnrollment(array $data)
    {
        $sql = "INSERT INTO biometric_enrollments (
                    employee_id, device_id, biometric_id, enrollment_type, finger_position, quality_score, enrollment_status, enrollment_date, notes
                ) VALUES (
                    :employee_id, :device_id, :biometric_id, :enrollment_type, :finger_position, :quality_score, :enrollment_status, :enrollment_date, :notes
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':employee_id' => (int) $data['employee_id'],
            ':device_id' => (int) $data['device_id'],
            ':biometric_id' => $data['biometric_id'] ?? null,
            ':enrollment_type' => $data['enrollment_type'] ?? 'fingerprint',
            ':finger_position' => $data['finger_position'] ?? 'Right Thumb',
            ':quality_score' => !empty($data['quality_score']) ? (float) $data['quality_score'] : 0.00,
            ':enrollment_status' => $data['enrollment_status'] ?? 'pending',
            ':enrollment_date' => !empty($data['enrollment_date']) ? $data['enrollment_date'] : date('Y-m-d H:i:s'),
            ':notes' => $data['notes'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getEnrollments($employeeId = null)
    {
        $sql = "SELECT be.*, e.full_name, e.employee_no, bd.device_name, bd.location
                FROM biometric_enrollments be
                LEFT JOIN employees e ON be.employee_id = e.employee_id
                LEFT JOIN biometric_devices bd ON be.device_id = bd.id";

        if ($employeeId) {
            $sql .= " WHERE be.employee_id = :employee_id";
        }

        $sql .= " ORDER BY be.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if ($employeeId) {
            $stmt->bindValue(':employee_id', (int) $employeeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentLogs($limit = 10)
    {
        $stmt = $this->db->prepare("SELECT bl.*, e.full_name, bd.device_name
                    FROM biometric_logs bl
                    LEFT JOIN employees e ON bl.employee_id = e.employee_id
                    LEFT JOIN biometric_devices bd ON bl.device_id = bd.id
                    ORDER BY bl.created_at DESC
                    LIMIT :limit");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createLog(array $data)
    {
        $sql = "INSERT INTO biometric_logs (
                    device_id, employee_id, biometric_id, log_datetime, punch_type, quality_score, raw_data, is_matched, match_confidence, status
                ) VALUES (
                    :device_id, :employee_id, :biometric_id, :log_datetime, :punch_type, :quality_score, :raw_data, :is_matched, :match_confidence, :status
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':device_id' => !empty($data['device_id']) ? (int) $data['device_id'] : null,
            ':employee_id' => !empty($data['employee_id']) ? (int) $data['employee_id'] : null,
            ':biometric_id' => $data['biometric_id'] ?? null,
            ':log_datetime' => !empty($data['log_datetime']) ? $data['log_datetime'] : date('Y-m-d H:i:s'),
            ':punch_type' => $data['punch_type'] ?? 'in',
            ':quality_score' => !empty($data['quality_score']) ? (float) $data['quality_score'] : 0.00,
            ':raw_data' => $data['raw_data'] ?? null,
            ':is_matched' => !empty($data['is_matched']) ? 1 : 0,
            ':match_confidence' => !empty($data['match_confidence']) ? (float) $data['match_confidence'] : 0.00,
            ':status' => $data['status'] ?? 'pending',
        ]);

        $this->updateLastSync($data['device_id'] ?? null);
        return (int) $this->db->lastInsertId();
    }

    public function getStats()
    {
        $stats = [];
        $stats['devices'] = (int) $this->db->query("SELECT COUNT(*) FROM biometric_devices")->fetchColumn();
        $stats['enrollments'] = (int) $this->db->query("SELECT COUNT(*) FROM biometric_enrollments WHERE is_active = 1")->fetchColumn();
        $stats['pending_logs'] = (int) $this->db->query("SELECT COUNT(*) FROM biometric_logs WHERE status = 'pending'")->fetchColumn();
        $stats['today_logs'] = (int) $this->db->query("SELECT COUNT(*) FROM biometric_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn();

        return $stats;
    }

    public function updateLastSync($deviceId)
    {
        if (!$deviceId) {
            return true;
        }

        $stmt = $this->db->prepare("UPDATE biometric_devices SET last_sync = NOW() WHERE id = :id");
        $stmt->bindValue(':id', (int) $deviceId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function testConnection($ipAddress, $port = 4370)
    {
        $timeout = 3;
        $connection = @fsockopen($ipAddress, (int) $port, $errno, $errstr, $timeout);

        if ($connection) {
            fclose($connection);
            return ['success' => true, 'message' => 'Device reachable through the network.'];
        }

        return ['success' => false, 'message' => 'Unable to connect to the device: ' . $errstr . ' (' . $errno . ')'];
    }
}
