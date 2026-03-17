<?php
// Simple test to verify realpath and file_exists work correctly for the image paths

$modulesDir = 'C:\xampp\htdocs\capstone_hr_management_system\learning_development\modules';
$imageRelativePath = 'modules/img/gifholder/gifholder-1.gif';

// Method 1: Original approach
echo "Method 1 (Original):\n";
$method1Path = $modulesDir . '/../' . $imageRelativePath;
echo "  Path: $method1Path\n";
echo "  Exists: " . (file_exists($method1Path) ? 'YES' : 'NO') . "\n\n";

// Method 2: With realpath (my fix)
echo "Method 2 (With realpath):\n";
$method2Path = realpath($modulesDir . '/../') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imageRelativePath);
echo "  Path: $method2Path\n";
echo "  Exists: " . (file_exists($method2Path) ? 'YES' : 'NO') . "\n\n";

// Method 3: Direct path
echo "Method 3 (Direct):\n";
$method3Path = 'C:\xampp\htdocs\capstone_hr_management_system\learning_development\modules\img\gifholder\gifholder-1.gif';
echo "  Path: $method3Path\n";
echo "  Exists: " . (file_exists($method3Path) ? 'YES' : 'NO') . "\n";
?>
