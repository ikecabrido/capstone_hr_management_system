<?php
/**
 * View PHP Error Logs
 */

require_once "../app/core/Session.php";

Session::start();

// Define possible log file locations
$xampp_root = dirname(dirname(dirname(__DIR__)));
$possible_logs = [
    'C:\\xampp\\php\\logs\\php_error_log',
    'C:\\xampp\\php\\logs\\error.log',
    $xampp_root . '\\php\\logs\\php_error_log',
    $xampp_root . '\\php\\logs\\error.log',
    $xampp_root . '\\apache\\logs\\error.log',
    sys_get_temp_dir() . '\\php_errors.log'
];

// Get the error log file location
$error_log = ini_get('error_log');
if (empty($error_log) || $error_log === 'syslog') {
    $error_log = null;
    foreach ($possible_logs as $log_path) {
        if (file_exists($log_path)) {
            $error_log = $log_path;
            break;
        }
    }
    
    if (!$error_log) {
        $error_log = $possible_logs[0]; // Default to first option for display
    }
}

$logs = [];
$error_message = "";

if ($error_log && file_exists($error_log)) {
    $content = file_get_contents($error_log);
    // Get last 50 lines
    $lines = array_reverse(explode("\n", $content));
    $logs = array_slice($lines, 0, 100);
} else {
    $error_message = "Error log file not found. Checked locations:<br>" . implode("<br>", array_map('htmlspecialchars', $possible_logs ?? []));
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PHP Error Logs</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .log-container { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 600px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word; }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
    </style>
</head>
<body>
    <div class="container" style="padding: 20px;">
        <h2>PHP Error Logs</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <p style="color: #666; font-size: 12px;">Log file: <code><?php echo htmlspecialchars($error_log); ?></code></p>
            
            <div class="log-container">
                <?php foreach ($logs as $log): ?>
                    <?php 
                        $display_log = htmlspecialchars($log);
                        if (strpos($display_log, 'Error') !== false || strpos($display_log, 'error') !== false) {
                            echo '<span class="log-error">' . $display_log . '</span>' . "\n";
                        } elseif (strpos($display_log, 'Warning') !== false || strpos($display_log, 'warning') !== false) {
                            echo '<span class="log-warning">' . $display_log . '</span>' . "\n";
                        } else {
                            echo $display_log . "\n";
                        }
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <a href="qr_generate.php" class="btn btn-primary">← Back to QR Generate</a>
            <button onclick="location.reload()" class="btn btn-secondary" style="margin-left: 10px;">🔄 Refresh</button>
        </div>
    </div>
</body>
</html>
