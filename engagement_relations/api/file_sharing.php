<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/../autoload.php';

use App\Models\SharedFile;

session_start();

if (!isset($_SESSION['user'])) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (empty($_FILES['shared_file']) || $_FILES['shared_file']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'File missing or upload error'], 400);
}

$file = $_FILES['shared_file'];
$allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'xlsx', 'xls'];
$maxSize = 10 * 1024 * 1024; // 10MB
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed, true)) {
    jsonResponse(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)], 400);
}

if ($file['size'] > $maxSize) {
    jsonResponse(['success' => false, 'message' => 'File too large. Max size is 10MB.'], 400);
}

$uploadDir = __DIR__ . '/../../uploads/social_files/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
$targetFileName = time() . '_' . $safeName;
$targetFile = $uploadDir . $targetFileName;

if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    jsonResponse(['success' => false, 'message' => 'Failed to store uploaded file.'], 500);
}

// Save to database
$sharedFileModel = new SharedFile();
$uploadedBy = $_SESSION['user']['employee_id'] ?? $_SESSION['user']['id'];
$description = $_POST['description'] ?? '';

$fileId = $sharedFileModel->createFile($safeName, 'uploads/social_files/' . $targetFileName, $file['size'], $ext, $uploadedBy, $description);

if (!$fileId) {
    // If DB insert failed, delete the uploaded file
    unlink($targetFile);
    jsonResponse(['success' => false, 'message' => 'Failed to save file information.'], 500);
}

$fileUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']) . '/../../uploads/social_files/' . $targetFileName);

jsonResponse([
    'success' => true,
    'message' => 'File shared successfully.',
    'file' => [
        'id' => $fileId,
        'name' => $safeName,
        'path' => 'uploads/social_files/' . $targetFileName,
        'url' => $fileUrl,
        'size' => $file['size'],
        'type' => $ext
    ]
], 201);
