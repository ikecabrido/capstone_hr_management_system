<?php
require_once __DIR__ . '/../autoload.php';
use App\Models\Policy;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    http_response_code(400);
    echo 'Invalid policy ID.';
    exit;
}

$policy = new Policy();
$detail = $policy->find($id);

if (!$detail || empty($detail['attachment_path'])) {
    http_response_code(404);
    echo 'Policy attachment not found.';
    exit;
}

$uploadRoot = realpath(__DIR__ . '/../..');
$filePath = $uploadRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $detail['attachment_path']);

if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

$fileName = basename($filePath);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit;
