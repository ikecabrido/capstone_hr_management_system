<?php
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';
class AnnouncementController {
    private $model;
    public function __construct($pdo) { $this->model = new Announcement($pdo); }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id']);
                    echo json_encode($result);
                } elseif (isset($_GET['read_status'])) {
                    $result = $this->model->getReadStatus($_GET['announcement_id'], $_GET['employee_id']);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['mark_read'])) {
                    Validator::validate($data, [
                        'title' => 'required|min:5|max:255',
                        'content' => 'required|min:10',
                        'created_by' => 'required',
                    ]);
                }
                if (isset($data['mark_read'])) {
                    $ok = $this->model->markAsRead($data['announcement_id'], $data['employee_id']);
                    echo json_encode(['success' => $ok]);
                } else {
                    $ok = $this->model->create($data['title'], $data['content'], $data['created_by']);
                    echo json_encode(['success' => $ok]);
                }
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'id' => 'required',
                    'title' => 'required|min:5|max:255',
                    'content' => 'required|min:10',
                ]);
                $ok = $this->model->update($data['id'], $data['title'], $data['content']);
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
