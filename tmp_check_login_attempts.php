<?php
$db = new PDO('mysql:host=localhost;dbname=hr_data;charset=utf8mb4','root','');
$stmt = $db->query("SHOW TABLES LIKE 'login_attempts'");
$rows = $stmt->fetchAll(PDO::FETCH_NUM);
echo count($rows) ? "login_attempts exists\n" : "login_attempts missing\n";
