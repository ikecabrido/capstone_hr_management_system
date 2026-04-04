<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\GrievanceController;

session_start();
$action = $_GET['action'] ?? 'list';
if (!isset($_SESSION['user']) && $action !== 'list') {
   jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new GrievanceController();
$action = $_GET['action'] ?? 'list';
$data = inputData();

try {
    switch ($action) {
        case 'list':
            $grievances = $ctrl->getGrievances();
            $updates = [];
            foreach ($grievances as $g) {
                if (method_exists($ctrl, 'history')) {
                    $updates[$g['eer_grievance_id']] = $ctrl->history($g['eer_grievance_id']);
                }
            }
            jsonResponse([
                'success' => true,
                'data' => $grievances,
                'grievance_updates' => $updates
            ]);
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
            error_log('Update Grievance Data: ' . json_encode($data));
            $res = $ctrl->updateStatus((int)$data['id'], $data['status']);
            if (!empty($data['comment'])) {
                $ctrl->addUpdate((int)$data['id'], $data['comment'], $_SESSION['user']['id']);
            }
            jsonResponse($res);
            break;
        case 'add_notes':
            if (empty($data['id']) || empty($data['notes'])) jsonResponse(['error' => 'id and notes are required'], 400);
            $ctrl->addInvestigationNotes((int)$data['id'], $data['notes'], $_SESSION['user']['id']);
            jsonResponse(['success' => true]);
            break;

        case 'mark_confidential':
            if (empty($data['id']) || !isset($data['confidential'])) jsonResponse(['error' => 'id and confidential flag are required'], 400);
            $ctrl->markConfidential((int)$data['id'], (bool)$data['confidential']);
            jsonResponse(['success' => true]);
            break;

        case 'resolve':
            if (empty($data['id']) || empty($data['resolution'])) jsonResponse(['error' => 'id and resolution details are required'], 400);
            $ctrl->resolveGrievance((int)$data['id'], $data['resolution'], $_SESSION['user']['id']);
            jsonResponse(['success' => true]);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
