<?php
require 'C:\xampp\htdocs\capstone_hr_management_system\capstone_hr_management_system\auth\database.php';
$db = Database::getInstance()->getConnection();
echo "DB_OK\n";
$stmt = $db->query("SHOW TABLES LIKE 'eer_award_votes'");
$row = $stmt->fetch();
echo ($row ? "TABLE_EXISTS" : "TABLE_MISSING");
