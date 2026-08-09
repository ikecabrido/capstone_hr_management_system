<?php
// Lightweight theme update endpoint — safe no-op if not used.
header('Content-Type: application/json');
session_start();
$theme = $_POST['theme'] ?? null;
if ($theme && in_array($theme, ['dark', 'light'], true)) {
    // persist in session for now
    $_SESSION['theme'] = $theme;
    echo json_encode(['success' => true, 'theme' => $theme]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'invalid_theme']);
