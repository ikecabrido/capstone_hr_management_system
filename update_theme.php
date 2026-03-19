<?php
/**
 * Update Theme Preference
 * API endpoint to save user's theme preference
 * 
 * Usage: POST to this file with 'theme' parameter
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in'
    ]);
    exit;
}

// Get parameters
$theme = $_POST['theme'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'User ID not found in session'
    ]);
    exit;
}

// Include the user preferences helper
require_once __DIR__ . '/auth/user_preferences.php';

// Save the theme preference
$result = saveUserThemePreference($userId, $theme);

if ($result['success']) {
    // Update session theme as well
    $_SESSION['user']['theme'] = $theme;
    
    echo json_encode([
        'success' => true,
        'message' => $result['message']
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}
