<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/image_upload.php';

// Get a sample career path with cover_photo
$stmt = $pdo->query('SELECT id, name, cover_photo FROM career_paths LIMIT 1');
$career = $stmt->fetch(PDO::FETCH_ASSOC);

if ($career) {
    echo "Database Career Path ID: " . $career['id'] . "\n";
    echo "Name: " . $career['name'] . "\n";
    echo "Cover Photo (from DB): " . $career['cover_photo'] . "\n";
    
    $imageUrl = getImageUrl($career['cover_photo'] ?? null, 'modules/img/placeholder.gif');
    echo "getImageUrl result: " . $imageUrl . "\n";
    
    $fullPath = __DIR__ . '/../' . $career['cover_photo'];
    echo "Full server path checked: " . $fullPath . "\n";
    echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    
    // Also test what that would resolve in URL context
    $currentDir = getcwd();
    echo "\nCurrent working directory: " . $currentDir . "\n";
    echo "Script directory (__DIR__): " . __DIR__ . "\n";
} else {
    echo "No career paths found in database\n";
}
?>
