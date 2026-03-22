<?php
require_once __DIR__ . '/../models/Survey.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class SurveyController {
    private $model;
    private $user;

    public function __construct($pdo, $user = null) {
        $this->model = new Survey($pdo);
        $this->user = $user;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                return $this->model->getAll();

            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'title' => 'required|min:3|max:255',
                    'description' => 'required|min:5',
                ]);

                $ok = $this->model->create($data['title'], $data['description'], $this->user['id'] ?? '1');
                echo json_encode(['success' => $ok]);
                break;

            case 'PUT':
                Auth::check();
                parse_str(file_get_contents('php://input'), $data);
                Validator::validate($data, [
                    'id' => 'required',
                    'title' => 'required|min:3|max:255',
                    'description' => 'required|min:5',
                ]);

                $ok = $this->model->update($data['id'], $data['title'], $data['description']);
                echo json_encode(['success' => $ok]);
                break;

            case 'DELETE':
                Auth::check();
                parse_str(file_get_contents('php://input'), $data);
                Validator::validate($data, ['id' => 'required']);
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