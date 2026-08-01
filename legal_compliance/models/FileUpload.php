<?php
/**
 * FileUpload Model
 * Handles file upload operations for incident evidence
 */

class FileUpload
{
    private string $uploadDir;
    private array $allowedTypes;
    private int $maxFileSize;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/incident_evidence/';
        $this->allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'image/pjpeg', // legacy clients
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain', 'text/csv',
            'video/mp4', 'video/quicktime',
            'audio/mpeg', 'audio/wav',
        ];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB

        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload single file
     */
    public function upload(array $file, ?int $uploadedBy = null): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'file_name' => '',
            'file_path' => '',
            'file_type' => '',
            'file_size' => 0
        ];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['message'] = $this->getUploadErrorMessage($file['error']);
            return $result;
        }

        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            $result['message'] = 'File size exceeds maximum limit of ' . ($this->maxFileSize / 1024 / 1024) . 'MB';
            return $result;
        }

        $fileType = $this->resolveAllowedMimeType($file);
        if ($fileType === '') {
            $result['message'] = 'File type not allowed';
            return $result;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;
        $filePath = $this->uploadDir . $uniqueName;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $result['success'] = true;
            $result['message'] = 'File uploaded successfully';
            $result['file_name'] = $file['name'];
            $result['file_path'] = 'uploads/incident_evidence/' . $uniqueName;
            $result['file_type'] = $fileType;
            $result['file_size'] = $file['size'];
        } else {
            $result['message'] = 'Failed to move uploaded file';
        }

        return $result;
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $files, ?int $uploadedBy = null): array
    {
        $results = [];
        if (!isset($files['name'], $files['error'], $files['tmp_name'], $files['size'], $files['type'])) {
            return $results;
        }

        // Single file field (name="evidence") — normalize to list shape
        if (!is_array($files['name'])) {
            $files = [
                'name' => [$files['name']],
                'type' => [$files['type']],
                'tmp_name' => [$files['tmp_name']],
                'error' => [$files['error']],
                'size' => [$files['size']],
            ];
        }

        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            $err = (int) ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE);
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $err,
                'size' => (int) ($files['size'][$i] ?? 0),
            ];

            $results[] = $this->upload($file, $uploadedBy);
        }

        return $results;
    }

    /**
     * Pick a MIME type we accept: browser hint, finfo on tmp file, or extension map (handles application/octet-stream on Windows).
     */
    private function resolveAllowedMimeType(array $file): string
    {
        $claimed = strtolower(trim((string) ($file['type'] ?? '')));
        if ($claimed === 'image/pjpeg') {
            $claimed = 'image/jpeg';
        }
        if ($claimed !== '' && $claimed !== 'application/octet-stream' && in_array($claimed, $this->allowedTypes, true)) {
            return $claimed;
        }

        $tmp = $file['tmp_name'] ?? '';
        if ($tmp !== '' && is_uploaded_file($tmp)) {
            $detected = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detected = strtolower((string) finfo_file($finfo, $tmp));
                    finfo_close($finfo);
                }
            }
            if ($detected === 'image/pjpeg') {
                $detected = 'image/jpeg';
            }
            if ($detected !== '' && in_array($detected, $this->allowedTypes, true)) {
                return $detected;
            }
        }

        $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $fromExt = $this->mimeTypeFromExtension($ext);
        if ($fromExt !== '' && in_array($fromExt, $this->allowedTypes, true)) {
            return $fromExt;
        }

        return '';
    }

    private function mimeTypeFromExtension(string $ext): string
    {
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jfif' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
        ];
        return $map[$ext] ?? '';
    }

    /**
     * Delete file
     */
    public function delete(string $filePath): bool
    {
        $fullPath = __DIR__ . '/../' . $filePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Get file info
     */
    public function getFileInfo(string $filePath): ?array
    {
        $fullPath = __DIR__ . '/../' . $filePath;
        
        if (file_exists($fullPath)) {
            return [
                'name' => basename($fullPath),
                'path' => $filePath,
                'size' => filesize($fullPath),
                'type' => mime_content_type($fullPath),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
            ];
        }
        
        return null;
    }

    /**
     * Validate file
     */
    public function validate(array $file): array
    {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $this->getUploadErrorMessage($file['error']);
        }

        if ($file['size'] > $this->maxFileSize) {
            $errors[] = 'File size exceeds maximum limit of ' . ($this->maxFileSize / 1024 / 1024) . 'MB';
        }

        if ($this->resolveAllowedMimeType($file) === '') {
            $errors[] = 'File type not allowed';
        }

        return $errors;
    }

    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $error): string
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive in HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Get allowed file types
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    /**
     * Get max file size
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * Get max file size in human readable format
     */
    public function getMaxFileSizeFormatted(): string
    {
        return ($this->maxFileSize / 1024 / 1024) . 'MB';
    }

    /**
     * Check if file type is image
     */
    public function isImage(string $fileType): bool
    {
        return strpos($fileType, 'image/') === 0;
    }

    /**
     * Check if file type is PDF
     */
    public function isPdf(string $fileType): bool
    {
        return $fileType === 'application/pdf';
    }

    /**
     * Check if file type is document
     */
    public function isDocument(string $fileType): bool
    {
        return in_array($fileType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ]);
    }

    /**
     * Get file icon class based on type
     */
    public function getFileIcon(string $fileType): string
    {
        if ($this->isImage($fileType)) {
            return 'fa-file-image';
        } elseif ($this->isPdf($fileType)) {
            return 'fa-file-pdf';
        } elseif ($this->isDocument($fileType)) {
            return 'fa-file-word';
        } elseif (strpos($fileType, 'video/') === 0) {
            return 'fa-file-video';
        } elseif (strpos($fileType, 'audio/') === 0) {
            return 'fa-file-audio';
        } else {
            return 'fa-file';
        }
    }

    /**
     * Format file size
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
