<?php
require_once '../../auth/database.php';

$db = Database::getInstance()->getConnection();
// Include associated user id when available so frontend can map to users.id
$sql = 'SELECT e.employee_id, e.full_name, u.id as user_id
	FROM employees e
	LEFT JOIN users u ON e.employee_id = u.employee_id
	ORDER BY e.full_name';
$stmt = $db->query($sql);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($employees);
