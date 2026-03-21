<?php
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../config/db.php';


class ReportController {
    private $model;
    public function __construct($pdo) { $this->model = new Report($pdo); }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET') {
            if (isset($_GET['engagement'])) {
                $result = $this->model->getEngagementReport();
            } elseif (isset($_GET['complaints'])) {
                $result = $this->model->getComplaintTrends();
            } else {
                // Return both reports by default
                $result = [
                    'engagement' => $this->model->getEngagementReport(),
                    'complaints' => $this->model->getComplaintTrends()
                ];
            }
            return is_array($result) ? $result : [];
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
}
