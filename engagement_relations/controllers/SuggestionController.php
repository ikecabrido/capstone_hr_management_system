<?php
require_once __DIR__ . '/../models/Suggestion.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class SuggestionController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new Suggestion($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id'], $this->user['role'] ?? null, $this->user['id'] ?? null);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll($this->user['role'] ?? null, $this->user['id'] ?? null);
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'suggestion_text' => 'required|min:5',
                ]);
                $ok = $this->model->create($this->user['id'], $data['suggestion_text']);
                echo json_encode(['success' => $ok]);
                break;
            case 'PUT':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'id' => 'required',
                    'status' => 'required|enum:pending,accepted,rejected',
                ]);
                $ok = $this->model->updateStatus($data['id'], $data['status']);
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
?>
