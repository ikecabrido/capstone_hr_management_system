<?php

require_once __DIR__ . '/../auth.php';
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
        break;

    /* UPDATE STATUS */
    case 'update_status':
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
        break;

    /* DELETE */
    case 'delete':
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
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
}
