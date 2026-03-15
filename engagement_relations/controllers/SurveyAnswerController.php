<?php
require_once __DIR__ . '/../models/SurveyAnswer.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class SurveyAnswerController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new SurveyAnswer($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['response_id'])) {
                    return $this->model->getByResponse($_GET['response_id']);
                } elseif (isset($_GET['question_id'])) {
                    return $this->model->getByQuestion($_GET['question_id']);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'response_id' => 'required',
                    'question_id' => 'required',
                    'answer' => 'required',
                ]);
                $ok = $this->model->create($data['response_id'], $data['question_id'], $data['answer']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
?>
