<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT medicine_id, medicine_name FROM cm_medicine_inventory");
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['medicine_id'] . ": " . $row['medicine_name'] . "\n";
}
