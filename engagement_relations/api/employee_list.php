<?php
require_once '../../auth/database.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT employee_id, full_name FROM employees ORDER BY full_name');
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($employees);