<?php
require_once __DIR__ . '/../models/AnnouncementRead.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class AnnouncementReadController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new AnnouncementRead($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['announcement_id'])) {
                    return $this->model->getReadsByAnnouncement($_GET['announcement_id']);
                } elseif (isset($_GET['employee_id'])) {
                    return $this->model->getReadsByEmployee($_GET['employee_id']);
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'announcement_id' => 'required',
                    'employee_id' => 'required',
                ]);
                $ok = $this->model->markAsRead($data['announcement_id'], $data['employee_id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
?>
