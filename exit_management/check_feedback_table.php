<?php
$db = new PDO('mysql:host=localhost;dbname=hr-management;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $db->query('SHOW TABLES LIKE "exit_interview_feedback"');
echo $stmt->rowCount();
