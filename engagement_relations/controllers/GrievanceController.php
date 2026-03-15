<?php
require_once __DIR__ . '/../models/Grievance.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class GrievanceController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new Grievance($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id'], $this->user['role'] ?? null, $this->user['id'] ?? null);
                    echo json_encode($result);
                } elseif (isset($_GET['employee_id'])) {
                    // Employees can only view their own grievances
                    if ($this->user['role'] === 'employee' && $_GET['employee_id'] != $this->user['id']) {
                        echo json_encode(['error' => 'Unauthorized']);
                    } else {
                        $result = $this->model->getByEmployee($_GET['employee_id']);
                        echo json_encode($result);
                    }
                } else {
                    return $this->model->getAll($this->user['role'] ?? null, $this->user['id'] ?? null);
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'employee_id' => 'required',
                    'subject' => 'required|min:5|max:255',
                    'description' => 'required|min:10',
                    'status' => 'enum:open,in-progress,resolved,closed',
                ]);
                $ok = $this->model->create($data['employee_id'], $data['subject'], $data['description'], $data['status'], $data['assigned_to']);
                echo json_encode(['success' => $ok]);
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'id' => 'required',
                    // status and assigned_to are validated in their respective branches
                ]);
                if (isset($data['status'])) {
                    $ok = $this->model->updateStatus($data['id'], $data['status']);
                } elseif (isset($data['assigned_to'])) {
                    $ok = $this->model->assign($data['id'], $data['assigned_to']);
                } else {
                    $ok = false;
                }
                echo json_encode(['success' => $ok]);
                break;
            case 'DELETE':
                Auth::check();
                parse_str(file_get_contents('php://input'), $data);
                Validator::validate($data, [
                    'id' => 'required',
                ]);
                $ok = $this->model->delete($data['id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
