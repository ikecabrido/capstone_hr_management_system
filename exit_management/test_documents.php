<?php
require_once "../auth/database.php";

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

// Test 1: Count ALL documents (regardless of status)
$stmt = $db->query("SELECT COUNT(*) as count FROM exit_documents");
$countAll = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 2: Count ACTIVE documents
$stmt = $db->query("SELECT COUNT(*) as count FROM exit_documents WHERE status = 'active'");
$countActive = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 3: Get ALL documents with all statuses
$stmt = $db->query("
    SELECT 
        d.id,
        d.employee_id,
        d.document_type,
        d.title,
        d.file_path,
        d.status,
        d.created_at,
        u.full_name as employee_name,
        u.id as user_id
    FROM exit_documents d
    LEFT JOIN users u ON d.employee_id = u.id
    ORDER BY d.created_at DESC
");
$allDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 4: Get ONLY active documents (the actual query used)
$stmt = $db->query("
    SELECT 
        d.id,
        d.employee_id,
        d.document_type,
        d.title,
        d.file_path,
        d.uploaded_by,
        d.status,
        d.created_at,
        d.updated_at,
        u.full_name as employee_name
    FROM exit_documents d
    LEFT JOIN users u ON d.employee_id = u.id
    WHERE d.status = 'active'
    ORDER BY d.created_at DESC
");
$activeDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 5: Get users to verify they exist
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
$activeUserCount = $stmt->fetch(PDO::FETCH_ASSOC);

// Test 6: Get list of active users
$stmt = $db->query("SELECT id, username, full_name FROM users WHERE status = 'active' ORDER BY full_name LIMIT 10");
$activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Test 7: Check table structure
$stmt = $db->query("DESCRIBE exit_documents");
$tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total_documents_count' => $countAll['count'],
    'active_documents_count' => $countActive['count'],
    'all_documents' => $allDocuments,
    'active_documents_query_result' => $activeDocuments,
    'active_users_count' => $activeUserCount['count'],
    'sample_active_users' => $activeUsers,
    'exit_documents_table_structure' => $tableStructure
], JSON_PRETTY_PRINT);
?>


