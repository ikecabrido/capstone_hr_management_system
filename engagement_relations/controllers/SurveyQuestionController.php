<?php
require_once __DIR__ . '/../models/SurveyQuestion.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class SurveyQuestionController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new SurveyQuestion($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['survey_id'])) {
                    return $this->model->getBySurvey($_GET['survey_id']);
                } elseif (isset($_GET['id'])) {
                    $result = $this->model->getById($_GET['id']);
                    echo json_encode($result);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'survey_id' => 'required',
                    'question_text' => 'required|min:5',
                    'question_type' => 'required|enum:scale,text,choice',
                ]);
                $ok = $this->model->create($data['survey_id'], $data['question_text'], $data['question_type']);
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
