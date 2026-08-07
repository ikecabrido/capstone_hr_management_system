<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\GrievanceController;

if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            $creatorId = $_SESSION['user']['id'] ?? null;
            $payslipId = isset($data['payslip_id']) ? (int) $data['payslip_id'] : null;
            $payslipInformation = isset($data['payslip_information']) ? trim($data['payslip_information']) : null;
            $id = $ctrl->fileGrievance(
                $employeeId,
                $data['subject'], 
                $data['description'], 
                $data['category'] ?? 'Workplace Conflict', 
                $data['anonymous'] ?? 0, 
                $data['attachment_path'] ?? null, 
                $creatorId,
                $payslipId,
                $payslipInformation
            );
            jsonResponse(['id' => $id], 201);
            break;
        case 'payslips':
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            if (empty($employeeId)) {
                jsonResponse(['error' => 'Unauthorized'], 401);
            }
            $payslips = $ctrl->getEmployeePayslips((int) $employeeId);
            jsonResponse(['success' => true, 'data' => $payslips]);
            break;
        case 'payslip_items':
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            $payslipId = isset($data['payslip_id']) ? (int) $data['payslip_id'] : 0;
            if (empty($employeeId) || $payslipId <= 0) {
                jsonResponse(['error' => 'Invalid request'], 400);
            }
            $items = $ctrl->getPayslipItems((int) $employeeId, $payslipId);
            jsonResponse(['success' => true, 'data' => $items]);
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

