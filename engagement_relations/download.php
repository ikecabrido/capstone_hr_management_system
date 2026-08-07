<?php
session_start();
require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/autoload.php';

use App\Models\Announcement;

$fileId = (int)($_GET['id'] ?? 0);

if (!$fileId) {
    http_response_code(400);
    die('File ID is required.');
}

// Get file from database
$announcement = new Announcement();
$file = $announcement->getSharedFileById($fileId);

if (!$file) {
    http_response_code(404);
    die('File not found.');
}

// Build full file path
$filePath = __DIR__ . '/../' . ltrim($file['file_path'], '/\\');

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    die('File does not exist on server.');
}

// Check if it's a valid file (not a directory)
if (!is_file($filePath)) {
    http_response_code(400);
    die('Invalid file.');
}

// Get file info
$fileName = basename($filePath);
$fileSize = filesize($filePath);

// Determine MIME type
$mimeType = 'application/octet-stream';
if (function_exists('mime_content_type')) {
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
} elseif (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
    finfo_close($finfo);
} else {
    // Fallback: use file extension
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'txt' => 'text/plain',
    ];
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
}

// Set headers for download
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $fileSize);
header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', $fileName) . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Read and output file
readfile($filePath);
exit;
?>
