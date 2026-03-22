<?php
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class AuditLogController {
    private $model;
    public function __construct($pdo) { $this->model = new AuditLog($pdo); }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id']);
                    echo json_encode($result);
                } elseif (isset($_GET['performed_by'])) {
                    $result = $this->model->getByUser($_GET['performed_by']);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'action' => 'required|min:3|max:255',
                    'performed_by' => 'required',
                    'target_type' => 'required|min:3|max:100',
                    'target_id' => 'required',
                    'details' => 'required|min:3',
                ]);
                $ok = $this->model->create($data['action'], $data['performed_by'], $data['target_type'], $data['target_id'], $data['details']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
