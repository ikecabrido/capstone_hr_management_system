<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/CertificationController.php";

if (!in_array($_SESSION['user']['role'], ['learning', 'admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$certificationId = isset($_POST['certification_id']) ? (int)$_POST['certification_id'] : null;
if (!$certificationId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing certification_id']);
    exit;
}

$controller = new CertificationController();
$result = $controller->revoke($certificationId);

header('Content-Type: application/json');
echo json_encode($result);
