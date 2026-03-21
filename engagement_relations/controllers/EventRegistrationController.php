<?php
require_once __DIR__ . '/../models/EventRegistration.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class EventRegistrationController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new EventRegistration($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['event_id'])) {
                    return $this->model->getByEvent($_GET['event_id']);
                } elseif (isset($_GET['employee_id'])) {
                    return $this->model->getByEmployee($_GET['employee_id']);
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'event_id' => 'required',
                    'employee_id' => 'required',
                ]);
                $ok = $this->model->register($data['event_id'], $data['employee_id']);
                echo json_encode(['success' => $ok]);
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'id' => 'required',
                ]);
                $ok = $this->model->markAttended($data['id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
?>
