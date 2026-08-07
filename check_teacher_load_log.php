<?php
$logFile = "c:\\xampp\\htdocs\\capstone_hr_management_system\\payroll\\views\\teacherLoadHandler.log";

echo "=== Checking teacherLoadHandler.log ===\n\n";

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    
    // Show last 50 lines
    $lastLines = array_slice($lines, -50);
    
    echo "Last " . count($lastLines) . " log entries:\n";
    echo str_repeat("-", 100) . "\n";
    foreach ($lastLines as $line) {
        if (!empty($line)) {
            echo $line . "\n";
        }
    }
} else {
    echo "❌ Log file does not exist yet.\n";
    echo "Path: $logFile\n\n";
    echo "Instructions:\n";
    echo "1. Go to Teacher Load Management page\n";
    echo "2. Fill in the form with:\n";
    echo "   - Teacher: Select any teacher\n";
    echo "   - Academic Year: 2026-2027\n";
    echo "   - Semester: Any semester\n";
    echo "   - Total Units: Any number > 0\n";
    echo "3. Click 'Save Load Assignment'\n";
    echo "4. Run this script again to see the log\n";
}
?>
