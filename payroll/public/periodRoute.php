<?php

require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../controllers/periodController.php';

header('Content-Type: application/json');

/* Auth */
$auth = new Auth();

if (!$auth->check()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

$controller = new PeriodController();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

$action = $_POST['action'] ?? null;
switch ($action) {
    /* CREATE */
    case 'create':
        try {
            if ($controller->create($_POST)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Period created'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create period'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create period: ' . $e->getMessage()
            ]);
            error_log('Period creation error: ' . $e->getMessage());
        }
        break;

    /* UPDATE STATUS */
    case 'update_status':
        try {
            if ($controller->updateStatus($_POST['id'], $_POST['status'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Status updated'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update status'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage()
            ]);
        }
        break;

    /* DELETE */
    case 'delete':
        try {
            if ($controller->delete($_POST['id'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Period deleted'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Cannot delete: Period in use'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
}
