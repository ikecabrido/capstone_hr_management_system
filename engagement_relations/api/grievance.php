<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\GrievanceController;

session_start();
if (!isset($_SESSION['user'])) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new GrievanceController();
$action = $_GET['action'] ?? 'list';
$data = inputData();

try {
    switch ($action) {
        case 'list':
            jsonResponse($ctrl->getGrievances());
            break;
        case 'create':
            foreach (['subject', 'description'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $id = $ctrl->fileGrievance($_SESSION['user']['id'], $data['subject'], $data['description']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'update':
            if (empty($data['id']) || empty($data['status'])) jsonResponse(['error' => 'id and status required'], 400);
            $res = $ctrl->updateStatus((int)$data['id'], $data['status']);
            if (!empty($data['comment'])) {
                $ctrl->addUpdate((int)$data['id'], $data['comment'], $_SESSION['user']['id']);
            }
            jsonResponse($res);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
