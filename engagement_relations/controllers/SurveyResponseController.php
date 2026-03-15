<?php
require_once __DIR__ . '/../models/SurveyResponse.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../middleware/Validator.php';

class SurveyResponseController {
    private $model;
    private $user;
    
    public function __construct($pdo, $user = null) {
        $this->model = new SurveyResponse($pdo);
        $this->user = $user;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if (isset($_GET['survey_id'])) {
                    return $this->model->getBySurvey($_GET['survey_id']);
                } elseif (isset($_GET['employee_id'])) {
                    return $this->model->getByEmployee($_GET['employee_id']);
                } else {
                    return $this->model->getAll();
                }
                break;
            case 'POST':
                Auth::check();
                $data = json_decode(file_get_contents('php://input'), true);
                Validator::validate($data, [
                    'survey_id' => 'required',
                ]);
                $ok = $this->model->create($data['survey_id'], $this->user['id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
?>
