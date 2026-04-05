<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();
$sql = "SELECT medicine_id FROM cm_medicine_inventory WHERE medicine_id REGEXP '^[0-9]+$' ORDER BY CAST(medicine_id AS UNSIGNED) DESC LIMIT 1";
$stmt = $db->query($sql);
echo $stmt->fetchColumn();
