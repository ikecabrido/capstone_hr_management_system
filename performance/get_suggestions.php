<?php
require_once __DIR__ . "/../auth/database.php";

$db = Database::getInstance()->getConnection();
$term = $_GET['term'] ?? '';
$type = $_GET['type'] ?? 'all'; // 'employee', 'goal', 'training', 'all'

$suggestions = [];

if (strlen($term) >= 1) {
    $term = "%$term%";

    // 1. Employee Suggestions
    if ($type == 'all' || $type == 'employee') {
        $stmt = $db->prepare("SELECT DISTINCT full_name as label, 'Employee' as category FROM users WHERE full_name LIKE ? LIMIT 5");
        $stmt->execute([$term]);
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // 2. Goal Title Suggestions
    if ($type == 'all' || $type == 'goal') {
        $stmt = $db->prepare("SELECT DISTINCT goal_title as label, 'Goal' as category FROM pm_goals WHERE goal_title LIKE ? LIMIT 5");
        $stmt->execute([$term]);
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // 3. Training Program Suggestions
    if ($type == 'all' || $type == 'training') {
        $stmt = $db->prepare("SELECT DISTINCT training_program as label, 'Training' as category FROM pm_training_recommendations WHERE training_program LIKE ? LIMIT 5");
        $stmt->execute([$term]);
        $suggestions = array_merge($suggestions, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

header('Content-Type: application/json');
echo json_encode($suggestions);
?>
