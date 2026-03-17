<?php
echo "Testing getImageUrl function\n";
echo "=====================================\n\n";

$imagePath = 'modules/img/gifholder/gifholder-1.gif';
$placeholder = 'modules/img/placeholder.gif';

$dirPath = __DIR__;
echo "Current __DIR__: $dirPath\n";
echo "Image path from DB: $imagePath\n";
echo "Placeholder: $placeholder\n\n";

// Build the full path like the function does
$fullPath = $dirPath . '/../' . $imagePath;
echo "Constructed path: $fullPath\n";

// Check with realpath
$realPath = realpath($dirPath . '/../') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
echo "Real path (normalized): $realPath\n\n";

// Check existence
echo "file_exists(\$fullPath): " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
echo "file_exists(\$realPath): " . (file_exists($realPath) ? 'YES' : 'NO') . "\n\n";

// Check what the improved function would return
if (!empty($imagePath) && file_exists($realPath)) {
    echo "Function would return: $imagePath\n";
} else {
    echo "Function would return placeholder: $placeholder\n";
}

// List directory to see what's actually there
echo "\nDirectory contents of ../modules/img/:\n";
$dir = dirname($dirPath) . '/modules/img/';
if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if (!in_array($f, ['.', '..'])) {
            echo "  - $f\n";
        }
    }
}
?>
