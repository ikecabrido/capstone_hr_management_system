<?php
require_once '../models/BiometricModule.php';
require_once '../../../auth/database.php';

header('Content-Type: application/json');

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    $biometricModule = new BiometricModule($db);
    $biometricModule->ensureTables();

    $input = file_get_contents('php://input');
    $payload = $input ? json_decode($input, true) : [];

    if (!$payload) {
        $payload = $_POST;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([
            'success' => true,
            'message' => 'Biometric sync endpoint is ready.',
            'endpoint' => 'POST JSON payload to create biometric log entries.'
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    $deviceId = !empty($payload['device_id']) ? (int) $payload['device_id'] : null;
    $employeeId = !empty($payload['employee_id']) ? (int) $payload['employee_id'] : null;
    $biometricId = trim($payload['biometric_id'] ?? '');

    if (!$deviceId && empty($payload['device_name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'device_id is required.']);
        exit;
    }

    if (!$employeeId && $biometricId !== '') {
        $stmt = $db->prepare("SELECT employee_id FROM biometric_enrollments WHERE biometric_id = :biometric_id AND enrollment_status = 'enrolled' AND is_active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->execute([':biometric_id' => $biometricId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $employeeId = (int) $row['employee_id'];
        }
    }

    $logId = $biometricModule->createLog([
        'device_id' => $deviceId,
        'employee_id' => $employeeId,
        'biometric_id' => $biometricId,
        'log_datetime' => $payload['log_datetime'] ?? date('Y-m-d H:i:s'),
        'punch_type' => $payload['punch_type'] ?? 'in',
        'quality_score' => $payload['quality_score'] ?? 0,
        'raw_data' => $payload['raw_data'] ?? null,
        'is_matched' => !empty($payload['is_matched']) ? 1 : ($employeeId ? 1 : 0),
        'match_confidence' => $payload['match_confidence'] ?? 0,
        'status' => $payload['status'] ?? 'pending',
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Biometric log received.',
        'log_id' => $logId,
        'employee_id' => $employeeId,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
