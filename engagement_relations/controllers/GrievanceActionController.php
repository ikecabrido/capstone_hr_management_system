<?php
require_once __DIR__ . '/../models/GrievanceAction.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class GrievanceActionController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new GrievanceAction($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['grievance_id'])) {
                    return $this->model->getByGrievance($_GET['grievance_id']);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'grievance_id' => 'required',
                    'action_taken' => 'required|min:5',
                ]);
                $ok = $this->model->create($data['grievance_id'], $data['action_taken'], $this->user['id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
?>
