<?php
// enrollment_handler.php - Handles enrollment/unenrollment AJAX requests
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$currentUserId = get_current_user_id();

if (!$currentUserId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$programId = intval($_POST['id'] ?? 0);

if (!$programId) {
    echo json_encode(['success' => false, 'message' => 'Invalid program ID']);
    exit;
}

try {
    if ($action === 'enroll') {
        // Check if already enrolled
        $stmt = $pdo->prepare('SELECT id FROM training_enrollments WHERE user_id = ? AND program_id = ?');
        $stmt->execute([$currentUserId, $programId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Already enrolled']);
            exit;
        }
        
        // Enroll user
        $stmt = $pdo->prepare('
            INSERT INTO training_enrollments (user_id, program_id, status, enrollment_date)
            VALUES (?, ?, ?, NOW())
        ');
        $stmt->execute([$currentUserId, $programId, 'pending']);
        
        echo json_encode(['success' => true, 'message' => 'Enrolled successfully']);
        exit;
    }
    
    if ($action === 'unenroll') {
        $stmt = $pdo->prepare('DELETE FROM training_enrollments WHERE user_id = ? AND program_id = ?');
        $stmt->execute([$currentUserId, $programId]);
        
        echo json_encode(['success' => true, 'message' => 'Unenrolled successfully']);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
} catch (Exception $e) {
    error_log('Enrollment handler error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
