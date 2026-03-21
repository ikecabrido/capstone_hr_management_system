<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class EventController {
    private $model;
    public function __construct($pdo) { $this->model = new Event($pdo); }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id']);
                    echo json_encode($result);
                } elseif (isset($_GET['registrations'])) {
                    $result = $this->model->getRegistrations($_GET['registrations']);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['register'])) {
                    Validator::validate($data, [
                        'event_id' => 'required',
                        'employee_id' => 'required',
                    ]);
                } else {
                    Validator::validate($data, [
                        'title' => 'required|min:5|max:255',
                        'description' => 'required|min:10',
                        'event_date' => 'required',
                        'created_by' => 'required',
                    ]);
                }
                if (isset($data['register'])) {
                    $ok = $this->model->register($data['event_id'], $data['employee_id']);
                    echo json_encode(['success' => $ok]);
                } else {
                    $ok = $this->model->create($data['title'], $data['description'], $data['event_date'], $data['created_by']);
                    echo json_encode(['success' => $ok]);
                }
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['mark_attendance'])) {
                    Validator::validate($data, [
                        'registration_id' => 'required',
                    ]);
                } else {
                    Validator::validate($data, [
                        'id' => 'required',
                        'title' => 'required|min:5|max:255',
                        'description' => 'required|min:10',
                        'event_date' => 'required',
                    ]);
                }
                if (isset($data['mark_attendance'])) {
                    $ok = $this->model->markAttendance($data['registration_id']);
                } else {
                    $ok = $this->model->update($data['id'], $data['title'], $data['description'], $data['event_date']);
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
