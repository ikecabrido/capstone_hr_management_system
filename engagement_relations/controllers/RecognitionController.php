<?php
require_once __DIR__ . '/../models/Recognition.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class RecognitionController {
    private $model;
    public function __construct($pdo) { $this->model = new Recognition($pdo); }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id']);
                    echo json_encode($result);
                } elseif (isset($_GET['employee_id'])) {
                    $result = $this->model->getForEmployee($_GET['employee_id']);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'from_employee_id' => 'required',
                    'to_employee_id' => 'required',
                    'type' => 'required|enum:peer,colleague',
                    'message' => 'min:2|max:500',
                ]);
                $ok = $this->model->create($data['from_employee_id'], $data['to_employee_id'], $data['type'], $data['message'], $data['reward_id']);
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
