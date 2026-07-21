<?php
/**
 * Biometric Device API
 * REST API for device management operations
 * 
 * Endpoints:
 * POST   /api/device_api.php?action=create    - Create new device
 * GET    /api/device_api.php?action=list      - List all devices
 * GET    /api/device_api.php?action=detail    - Get device details
 * POST   /api/device_api.php?action=update    - Update device
 * POST   /api/device_api.php?action=sync      - Trigger sync
 * POST   /api/device_api.php?action=delete    - Delete device
 */

// Start session
session_start();

// Include required files
require_once('../../auth/database.php');
require_once('../../auth/auth_check.php');
require_once('../config/biometric_config.php');
require_once('../models/BiometricDevice.php');
require_once('../services/BiometricService.php');

// Set response header
header('Content-Type: application/json');

// Check authorization - only time/HR admin
if (!in_array($_SESSION['user']['role'] ?? $_SESSION['role'] ?? null, ['time', 'hr_admin', 'admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $device_model = new BiometricDevice($db);
    $biometric_service = new BiometricService($db);
    
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    
    switch ($action) {
        case 'create':
            handleCreate($device_model);
            break;
            
        case 'list':
            handleList($device_model);
            break;
            
        case 'detail':
            handleDetail($device_model);
            break;
            
        case 'update':
            handleUpdate($device_model);
            break;
            
        case 'sync':
            handleSync($biometric_service);
            break;
            
        case 'delete':
            handleDelete($device_model);
            break;
            
        case 'test_connection':
            handleTestConnection($device_model);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    logBiometric('ERROR', 'API error', ['error' => $e->getMessage()]);
}

// Handler Functions

function handleCreate($device_model) {
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    
    // Validate required fields
    if (empty($data['device_name']) || empty($data['location'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'device_name and location required']);
        return;
    }
    
    $device_model->device_name = $data['device_name'];
    $device_model->device_type = $data['device_type'] ?? 'fingerprint';
    $device_model->manufacturer = $data['manufacturer'] ?? 'NGTeco';
    $device_model->model = $data['model'] ?? '';
    $device_model->serial_number = $data['serial_number'] ?? null;
    $device_model->ip_address = $data['ip_address'] ?? null;
    $device_model->mac_address = $data['mac_address'] ?? null;
    $device_model->location = $data['location'];
    $device_model->status = $data['status'] ?? 'active';
    $device_model->capacity = $data['capacity'] ?? 100;
    $device_model->firmware_version = $data['firmware_version'] ?? null;
    $device_model->notes = $data['notes'] ?? null;
    
    if ($device_model->create()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Device created successfully'
        ]);
        logBiometric('INFO', 'Device created', ['device_name' => $device_model->device_name]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Failed to create device']);
    }
}

function handleList($device_model) {
    $devices = $device_model->getAll();
    
    echo json_encode([
        'success' => true,
        'data' => $devices,
        'count' => count($devices)
    ]);
}

function handleDetail($device_model) {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Device ID required']);
        return;
    }
    
    $device = $device_model->getById($id);
    
    if ($device) {
        $stats = $device_model->getPerformanceStats($id);
        
        echo json_encode([
            'success' => true,
            'data' => $device,
            'stats' => $stats
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Device not found']);
    }
}

function handleUpdate($device_model) {
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $id = $data['id'] ?? $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Device ID required']);
        return;
    }
    
    $device_model->id = $id;
    $device_model->device_name = $data['device_name'] ?? null;
    $device_model->status = $data['status'] ?? null;
    $device_model->location = $data['location'] ?? null;
    $device_model->firmware_version = $data['firmware_version'] ?? null;
    $device_model->notes = $data['notes'] ?? null;
    
    if ($device_model->update()) {
        echo json_encode([
            'success' => true,
            'message' => 'Device updated successfully'
        ]);
        logBiometric('INFO', 'Device updated', ['device_id' => $id]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Failed to update device']);
    }
}

function handleSync($biometric_service) {
    $device_id = $_GET['device_id'] ?? $_POST['device_id'] ?? null;
    
    if (!$device_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Device ID required']);
        return;
    }
    
    logBiometric('INFO', 'Sync triggered via API', ['device_id' => $device_id]);
    
    $result = $biometric_service->syncDevice($device_id);
    
    echo json_encode($result);
}

function handleDelete($device_model) {
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Device ID required']);
        return;
    }
    
    if ($device_model->delete($id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Device deleted successfully'
        ]);
        logBiometric('INFO', 'Device deleted', ['device_id' => $id]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Failed to delete device']);
    }
}

function handleTestConnection($device_model) {
    $ip = $_GET['ip'] ?? $_POST['ip'] ?? null;
    $port = $_GET['port'] ?? $_POST['port'] ?? 4370;
    
    if (!$ip) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'IP address required']);
        return;
    }
    
    $connected = $device_model->checkConnectivity($ip, $port);
    
    echo json_encode([
        'success' => $connected,
        'message' => $connected ? 'Device is reachable' : 'Device is not reachable',
        'ip' => $ip,
        'port' => $port
    ]);
}
