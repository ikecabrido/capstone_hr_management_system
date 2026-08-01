<?php
require_once '../app/controllers/AuthController.php';
require_once '../app/core/Session.php';
require_once '../app/models/BiometricModule.php';
require_once '../../auth/database.php';

Session::start();

if (!AuthController::isAuthenticated()) {
    header('Location: ../../login_form.php');
    exit;
}

// Allow HR/time role or kiosk usage
if (!AuthController::hasRole('time')) {
    // still allow access if user is employee for kiosk viewing (optional)
}

$database = Database::getInstance();
$db = $database->getConnection();
$biometricModule = new BiometricModule($db);
$biometricModule->ensureTables();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Biometric Kiosk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (truncated backup of original file) */
    </style>
</head>
<body>
    <!-- original kiosk page backed up -->
</body>
</html>
