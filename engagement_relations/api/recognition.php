<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\RecognitionController;

session_start();
if (!isset($_SESSION['user'])) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new RecognitionController();
$action = $_GET['action'] ?? 'list';
$data = inputData();

try {
    switch ($action) {
        case 'list':
            jsonResponse($ctrl->getRecognitions());
            break;
        case 'send':
            foreach (['receiver_id', 'message', 'points'] as $f) {
                if (!isset($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $id = $ctrl->sendRecognition($_SESSION['user']['id'], (int)$data['receiver_id'], $data['message'], (int)$data['points']);
            jsonResponse(['id' => $id], 201);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
