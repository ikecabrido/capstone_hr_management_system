<?php

/**
 * Upload an image file to a module-specific directory
 * @param array $file The $_FILES array for the uploaded file
 * @param string $folder The target folder (e.g., 'career', 'training', 'leadership')
 * @param int $maxSize Maximum file size in bytes
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function uploadImage($file, $folder, $maxSize = 2097152) {
    // Validate file upload
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Invalid file upload.'];
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size exceeds ' . ($maxSize / 1024 / 1024) . 'MB limit.'];
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP.'];
    }

    // Create directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'Could not create upload directory.'];
        }
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $extension;
    $filePath = $uploadDir . '/' . $filename;
    $relativePath = 'uploads/' . $folder . '/' . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file.'];
    }

    return ['success' => true, 'path' => $relativePath];
}

/**
 * Delete an image file
 * @param string $imagePath The relative path to the image
 * @return bool True if deletion successful
 */
function deleteImage($imagePath) {
    if (!$imagePath) {
        return false;
    }

    $fullPath = __DIR__ . '/../' . $imagePath;
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }

    return false;
}

/**
 * Get image URL with fallback to placeholder
 * @param string|null $imagePath The relative path to the image
 * @param string $placeholder The fallback placeholder image path
 * @return string The image URL
 */
function getImageUrl($imagePath, $placeholder = 'img/placeholder.gif') {
    if (!empty($imagePath) && file_exists(__DIR__ . '/../' . $imagePath)) {
        return $imagePath;
    }
    return $placeholder;
}

?>
