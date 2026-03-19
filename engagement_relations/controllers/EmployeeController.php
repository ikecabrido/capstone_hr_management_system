<?php
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class EmployeeController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new Employee($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id'], $this->user['role'] ?? null, $this->user['id'] ?? null);
                } elseif (isset($_GET['department_id'])) {
                    // Employees cannot filter by department
                    if ($this->user['role'] === 'employee') {
                        $result = $this->model->getById($this->user['id'], 'employee', $this->user['id']);
                    } else {
                        $result = $this->model->getByDepartment($_GET['department_id']);
                    }
                } else {
                    $result = $this->model->getAll($this->user['role'] ?? null, $this->user['id'] ?? null);
                }
                return $result;
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'name' => 'required|min:2|max:100',
                    'department_id' => 'required',
                    'email' => 'required|email',
                    'role' => 'required|enum:employee,hr,admin',
                    'status' => 'required|enum:active,inactive',
                ]);
                $ok = $this->model->create($data['name'], $data['department_id'], $data['email'], $data['role'], $data['status']);
                echo json_encode(['success' => $ok]);
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'id' => 'required',
                    'name' => 'required|min:2|max:100',
                    'department_id' => 'required',
                    'email' => 'required|email',
                    'role' => 'required|enum:employee,hr,admin',
                    'status' => 'required|enum:active,inactive',
                ]);
                $ok = $this->model->update($data['id'], $data['name'], $data['department_id'], $data['email'], $data['role'], $data['status']);
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
