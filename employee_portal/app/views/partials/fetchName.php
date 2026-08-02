<?php
require_once __DIR__ . '/../../config/Database.php';

$database = new Database();
$conn = $database->getConnection();

$userId = $_SESSION['user_id'];

$query = "
    SELECT first_name, last_name
    FROM employees
    WHERE user_id = :user_id
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

$employee = $stmt->fetch(PDO::FETCH_ASSOC);
