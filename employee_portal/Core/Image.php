<?php
class Image
{
    protected string $uploadDir;
    private static $basePath = '/hrmsys/public/';

    public function __construct(string $uploadDir = __DIR__ . '/../../public/images')
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload an image file
     * @param array $file 
     * @return string|null 
     * @throws Exception 
     */
    public function upload(array $file): ?string
    {
        if (empty($file['name'])) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error uploading file: ' . $file['error']);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $ext;
        $destination = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Failed to move uploaded file.');
        }

        return $filename;
    }

    public static function get($filename, $folder = 'images', $default = 'assets/images/default-profile.jpg')
    {
        if (!empty($filename)) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . self::$basePath . $folder . '/' . $filename;

            if (file_exists($filePath)) {
                return self::$basePath . $folder . '/' . $filename;
            }
        }

        return self::$basePath . $default;
    }
}
